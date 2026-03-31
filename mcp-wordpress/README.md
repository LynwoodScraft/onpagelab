# MCP WordPress Multi-Site

Connecte Claude à plusieurs sites WordPress via l'API REST native.
Aucun plugin requis — fonctionne avec WordPress 5.6+.

---

## Installation

### 1. Installer les dépendances Python

```bash
pip install mcp httpx
```

### 2. Générer un Application Password sur chaque site

Pour chaque WordPress à connecter :

1. WP Admin → **Users → Your Profile**
2. Section **Application Passwords** (tout en bas)
3. Name : `Claude MCP` → **Add New Application Password**
4. Copiez le mot de passe (format `xxxx xxxx xxxx xxxx xxxx xxxx`)

### 3. Configurer les sites

Éditez `sites.json` et renseignez vos informations :

```json
{
  "sites": {
    "onpagelab-staging": {
      "url": "https://staging.onpagelab.fr",
      "user": "pierre",
      "app_password": "xxxx xxxx xxxx xxxx xxxx xxxx"
    },
    "surfcoachtregor": {
      "url": "https://surfcoachtregor.fr",
      "user": "pierre",
      "app_password": "xxxx xxxx xxxx xxxx xxxx xxxx"
    }
  }
}
```

> Le nom de chaque entrée (ex: `onpagelab-staging`) est celui que vous utiliserez dans Claude comme paramètre `site`.

### 4. Ajouter le MCP dans Claude Desktop

Ouvrez `~/Library/Application Support/Claude/claude_desktop_config.json`
et ajoutez le bloc suivant dans `mcpServers` :

```json
"wordpress-multisite": {
  "command": "python3",
  "args": ["/chemin/absolu/vers/mcp-wordpress/server.py"]
}
```

Remplacez `/chemin/absolu/vers/` par le vrai chemin du dossier sur votre Mac.

### 5. Redémarrer Claude Desktop

Cmd+Q puis relancez. Le MCP apparaîtra dans la liste des outils.

---

## Ajouter un nouveau site

Il suffit d'ajouter une entrée dans `sites.json` — aucune modification du code requise :

```json
"mon-nouveau-site": {
  "url": "https://mon-site.fr",
  "user": "admin",
  "app_password": "xxxx xxxx xxxx xxxx xxxx xxxx"
}
```

---

## Outils disponibles

| Outil | Description |
|---|---|
| `wordpress_list_sites` | Liste tous les sites configurés |
| `wordpress_list_posts` | Liste les articles |
| `wordpress_get_post` | Récupère un article par ID |
| `wordpress_create_post` | Crée un article |
| `wordpress_update_post` | Met à jour un article |
| `wordpress_publish_post` | Publie un brouillon |
| `wordpress_list_pages` | Liste les pages |
| `wordpress_create_page` | Crée une page |
| `wordpress_list_categories` | Liste les catégories |
| `wordpress_create_category` | Crée une catégorie |
| `wordpress_list_tags` | Liste les tags |
| `wordpress_set_featured_image` | Uploade et assigne une image à la une |

---

## Exemple d'utilisation dans Claude

```
Crée un article brouillon sur onpagelab-staging avec le titre "Guide SEO 2025"
Publie l'article ID 42 sur surfcoachtregor
Liste les articles en draft de onpagelab-staging
```
