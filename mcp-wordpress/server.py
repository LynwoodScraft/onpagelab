#!/usr/bin/env python3
"""
MCP Server — WordPress Multi-Site
Connecte Claude à plusieurs sites WordPress via l'API REST.

Configuration : fichier sites.json dans le même dossier que ce script.
"""

import os
import base64
import json
import mimetypes
from pathlib import Path

import httpx
from mcp.server.fastmcp import FastMCP

# ── Chargement de la configuration des sites ─────────────────
SITES_FILE = Path(__file__).parent / "sites.json"

def _load_sites() -> dict:
    if not SITES_FILE.exists():
        raise FileNotFoundError(f"Fichier de configuration introuvable : {SITES_FILE}")
    with open(SITES_FILE, "r", encoding="utf-8") as f:
        data = json.load(f)
    return data.get("sites", {})

def _get_site(site: str) -> dict:
    """Retourne la config d'un site par son nom, ou le site par défaut."""
    sites = _load_sites()
    if not sites:
        raise ValueError("Aucun site configuré dans sites.json")
    if site:
        if site not in sites:
            available = ", ".join(sites.keys())
            raise ValueError(f"Site '{site}' inconnu. Sites disponibles : {available}")
        return sites[site]
    # Retourne le premier site comme défaut
    default = next(iter(sites))
    return sites[default]

def _list_sites_names() -> list[str]:
    return list(_load_sites().keys())

# ── Helpers HTTP ─────────────────────────────────────────────
def _auth_header(cfg: dict) -> dict:
    token = base64.b64encode(f"{cfg['user']}:{cfg['app_password']}".encode()).decode()
    return {"Authorization": f"Basic {token}"}

def _api_base(cfg: dict) -> str:
    return f"{cfg['url'].rstrip('/')}/wp-json/wp/v2"

def _get(cfg: dict, endpoint: str, params: dict = None) -> dict | list:
    r = httpx.get(
        f"{_api_base(cfg)}/{endpoint}",
        headers=_auth_header(cfg),
        params=params or {},
        timeout=30,
    )
    r.raise_for_status()
    return r.json()

def _post(cfg: dict, endpoint: str, data: dict) -> dict:
    r = httpx.post(
        f"{_api_base(cfg)}/{endpoint}",
        headers=_auth_header(cfg),
        json=data,
        timeout=30,
    )
    r.raise_for_status()
    return r.json()

def _patch(cfg: dict, endpoint: str, data: dict) -> dict:
    r = httpx.patch(
        f"{_api_base(cfg)}/{endpoint}",
        headers=_auth_header(cfg),
        json=data,
        timeout=30,
    )
    r.raise_for_status()
    return r.json()

def _ok(data: dict) -> str:
    return json.dumps(data, ensure_ascii=False, indent=2)

# ── Serveur MCP ───────────────────────────────────────────────
mcp = FastMCP("wordpress-multisite")


# ── GESTION DES SITES ─────────────────────────────────────────

@mcp.tool()
def wordpress_list_sites() -> str:
    """
    Liste tous les sites WordPress configurés dans sites.json.
    Utilisez le nom retourné comme paramètre 'site' dans les autres outils.
    """
    sites = _load_sites()
    result = [
        {"name": name, "url": cfg["url"], "user": cfg["user"]}
        for name, cfg in sites.items()
    ]
    return _ok(result)


# ── ARTICLES (POSTS) ─────────────────────────────────────────

@mcp.tool()
def wordpress_list_posts(
    site: str = "",
    status: str = "any",
    per_page: int = 20,
    page: int = 1,
    search: str = "",
) -> str:
    """
    Liste les articles d'un site WordPress.
    - site      : nom du site (laisser vide pour le site par défaut)
    - status    : 'publish', 'draft', 'any'
    - per_page  : nombre de résultats (max 100)
    - search    : filtre par mot-clé dans le titre
    """
    cfg = _get_site(site)
    params: dict = {"status": status, "per_page": per_page, "page": page}
    if search:
        params["search"] = search
    posts = _get(cfg, "posts", params)
    result = [
        {
            "id":     p["id"],
            "title":  p["title"]["rendered"],
            "status": p["status"],
            "slug":   p["slug"],
            "date":   p["date"],
            "link":   p["link"],
            "edit":   f"{cfg['url']}/wp-admin/post.php?post={p['id']}&action=edit",
        }
        for p in posts
    ]
    return _ok(result)


@mcp.tool()
def wordpress_get_post(post_id: int, site: str = "") -> str:
    """Retourne le contenu complet d'un article par son ID."""
    cfg = _get_site(site)
    p = _get(cfg, f"posts/{post_id}")
    return _ok({
        "id":         p["id"],
        "title":      p["title"]["rendered"],
        "content":    p["content"]["rendered"],
        "excerpt":    p["excerpt"]["rendered"],
        "status":     p["status"],
        "slug":       p["slug"],
        "date":       p["date"],
        "link":       p["link"],
        "categories": p.get("categories", []),
        "tags":       p.get("tags", []),
    })


@mcp.tool()
def wordpress_create_post(
    title: str,
    content: str,
    site: str = "",
    status: str = "draft",
    slug: str = "",
    excerpt: str = "",
    categories: list[int] = None,
    tags: list[int] = None,
    meta_title: str = "",
    meta_description: str = "",
) -> str:
    """
    Crée un article WordPress.
    - site             : nom du site cible
    - status           : 'draft' (défaut) ou 'publish'
    - meta_title       : balise title Yoast SEO
    - meta_description : meta description Yoast SEO
    """
    cfg = _get_site(site)
    data: dict = {
        "title":   title,
        "content": content,
        "status":  status,
        "excerpt": excerpt,
    }
    if slug:
        data["slug"] = slug
    if categories:
        data["categories"] = categories
    if tags:
        data["tags"] = tags
    if meta_title or meta_description:
        data["meta"] = {}
        if meta_title:
            data["meta"]["_yoast_wpseo_title"] = meta_title
        if meta_description:
            data["meta"]["_yoast_wpseo_metadesc"] = meta_description

    p = _post(cfg, "posts", data)
    return _ok({
        "id":     p["id"],
        "title":  p["title"]["rendered"],
        "status": p["status"],
        "link":   p["link"],
        "edit":   f"{cfg['url']}/wp-admin/post.php?post={p['id']}&action=edit",
    })


@mcp.tool()
def wordpress_update_post(
    post_id: int,
    site: str = "",
    title: str = "",
    content: str = "",
    status: str = "",
    excerpt: str = "",
    slug: str = "",
    meta_title: str = "",
    meta_description: str = "",
) -> str:
    """Met à jour un article existant. Seuls les champs fournis sont modifiés."""
    cfg = _get_site(site)
    data: dict = {}
    if title:       data["title"]   = title
    if content:     data["content"] = content
    if status:      data["status"]  = status
    if excerpt:     data["excerpt"] = excerpt
    if slug:        data["slug"]    = slug
    if meta_title or meta_description:
        data["meta"] = {}
        if meta_title:
            data["meta"]["_yoast_wpseo_title"] = meta_title
        if meta_description:
            data["meta"]["_yoast_wpseo_metadesc"] = meta_description

    p = _patch(cfg, f"posts/{post_id}", data)
    return _ok({
        "id":     p["id"],
        "title":  p["title"]["rendered"],
        "status": p["status"],
        "link":   p["link"],
    })


@mcp.tool()
def wordpress_publish_post(post_id: int, site: str = "") -> str:
    """Publie un article actuellement en brouillon."""
    cfg = _get_site(site)
    p = _patch(cfg, f"posts/{post_id}", {"status": "publish"})
    return _ok({
        "id":     p["id"],
        "title":  p["title"]["rendered"],
        "status": p["status"],
        "link":   p["link"],
    })


# ── PAGES ─────────────────────────────────────────────────────

@mcp.tool()
def wordpress_list_pages(site: str = "", status: str = "any", per_page: int = 20) -> str:
    """Liste les pages WordPress (pas les articles)."""
    cfg = _get_site(site)
    pages = _get(cfg, "pages", {"status": status, "per_page": per_page})
    result = [
        {
            "id":     p["id"],
            "title":  p["title"]["rendered"],
            "status": p["status"],
            "slug":   p["slug"],
            "link":   p["link"],
            "edit":   f"{cfg['url']}/wp-admin/post.php?post={p['id']}&action=edit",
        }
        for p in pages
    ]
    return _ok(result)


@mcp.tool()
def wordpress_create_page(
    title: str,
    content: str,
    site: str = "",
    status: str = "draft",
    slug: str = "",
    meta_title: str = "",
    meta_description: str = "",
) -> str:
    """Crée une page WordPress (À propos, Contact, Mentions légales…)."""
    cfg = _get_site(site)
    data: dict = {"title": title, "content": content, "status": status}
    if slug:
        data["slug"] = slug
    if meta_title or meta_description:
        data["meta"] = {}
        if meta_title:
            data["meta"]["_yoast_wpseo_title"] = meta_title
        if meta_description:
            data["meta"]["_yoast_wpseo_metadesc"] = meta_description

    p = _post(cfg, "pages", data)
    return _ok({
        "id":     p["id"],
        "title":  p["title"]["rendered"],
        "status": p["status"],
        "link":   p["link"],
        "edit":   f"{cfg['url']}/wp-admin/post.php?post={p['id']}&action=edit",
    })


# ── CATÉGORIES & TAGS ─────────────────────────────────────────

@mcp.tool()
def wordpress_list_categories(site: str = "") -> str:
    """Liste toutes les catégories avec leur ID, nom, slug et nombre d'articles."""
    cfg = _get_site(site)
    cats = _get(cfg, "categories", {"per_page": 100})
    return _ok([
        {"id": c["id"], "name": c["name"], "slug": c["slug"], "count": c["count"]}
        for c in cats
    ])


@mcp.tool()
def wordpress_create_category(
    name: str,
    site: str = "",
    slug: str = "",
    description: str = "",
) -> str:
    """Crée une nouvelle catégorie."""
    cfg = _get_site(site)
    data: dict = {"name": name}
    if slug:        data["slug"]        = slug
    if description: data["description"] = description
    c = _post(cfg, "categories", data)
    return _ok({"id": c["id"], "name": c["name"], "slug": c["slug"]})


@mcp.tool()
def wordpress_list_tags(site: str = "") -> str:
    """Liste tous les tags."""
    cfg = _get_site(site)
    tags = _get(cfg, "tags", {"per_page": 100})
    return _ok([
        {"id": t["id"], "name": t["name"], "slug": t["slug"], "count": t["count"]}
        for t in tags
    ])


# ── MÉDIAS ────────────────────────────────────────────────────

@mcp.tool()
def wordpress_set_featured_image(
    post_id: int,
    image_path: str,
    site: str = "",
    alt_text: str = "",
    caption: str = "",
) -> str:
    """
    Uploade une image locale et la définit comme image à la une.
    - image_path : chemin absolu vers le fichier (PNG, JPG, WEBP)
    """
    cfg = _get_site(site)
    path = Path(image_path)
    if not path.exists():
        return _ok({"error": f"Fichier introuvable : {image_path}"})

    mime = mimetypes.guess_type(str(path))[0] or "image/jpeg"
    headers = {
        **_auth_header(cfg),
        "Content-Disposition": f'attachment; filename="{path.name}"',
        "Content-Type": mime,
    }
    with open(path, "rb") as f:
        r = httpx.post(
            f"{_api_base(cfg)}/media",
            headers=headers,
            content=f.read(),
            timeout=60,
        )
    r.raise_for_status()
    media = r.json()
    media_id = media["id"]

    update: dict = {}
    if alt_text: update["alt_text"] = alt_text
    if caption:  update["caption"]  = caption
    if update:
        _patch(cfg, f"media/{media_id}", update)

    _patch(cfg, f"posts/{post_id}", {"featured_media": media_id})

    return _ok({
        "media_id":  media_id,
        "media_url": media["source_url"],
        "post_id":   post_id,
        "message":   "Image uploadée et définie comme image à la une.",
    })


# ── POINT D'ENTRÉE ────────────────────────────────────────────
if __name__ == "__main__":
    mcp.run()
