# OnPageLab — Architecture du site & Stratégie SEO
*Dernière mise à jour : Mars 2026*

---

## 1. Identité du projet

| Élément | Valeur |
|---------|--------|
| **Nom de marque** | OnPageLab |
| **Tagline FR** | L'analyse SEO on-page qui va à l'essentiel |
| **Tagline EN** | On-page SEO analysis that gets straight to the point |
| **Domaine suggéré** | onpagelab.io |
| **Produit** | Outil SaaS d'analyse SEO on-page (technique + sémantique) |
| **Cible** | Tous profils SEO (débutants → experts) |
| **Langues** | Français (défaut) + Anglais |

### Palette de couleurs
| Token | Hex | Usage |
|-------|-----|-------|
| Primary | `#2563EB` | CTAs, liens, accents |
| Accent | `#7C3AED` | Dégradés, secondaire |
| Dark | `#0F172A` | Hero dark, footer |
| Success | `#10B981` | Score bon |
| Warning | `#F59E0B` | Score moyen |
| Error | `#EF4444` | Score mauvais |

---

## 2. Architecture des pages — Version française (défaut)

```
/ (Accueil)
├── /outil-seo/                           → Tool page (template-tool.php)
├── /features/
│   ├── /features/audit-technique-seo/    → LP Audit technique (template-feature.php)
│   ├── /features/analyse-semantique/     → LP Analyse sémantique
│   ├── /features/balises-meta/           → LP Balises meta
│   ├── /features/maillage-interne/       → LP Maillage interne
│   └── /features/densite-mots-cles/      → LP Densité de mots-clés
├── /blog/                                → Archive blog
│   ├── /blog/seo-on-page-guide-complet/              → Pilier FR 1
│   ├── /blog/audit-seo-on-page-guide/                → Pilier FR 2
│   ├── /blog/optimisation-balise-title/              → Cluster
│   ├── /blog/meta-description-seo/                   → Cluster
│   ├── /blog/densite-mots-cles-seo/                  → Cluster
│   ├── /blog/maillage-interne-seo-guide/             → Cluster
│   ├── /blog/seo-on-page-vs-off-page/                → Cluster
│   ├── /blog/balises-hn-seo/                         → Cluster
│   ├── /blog/score-seo-page/                         → Cluster
│   └── /blog/analyse-semantique-seo/                 → Cluster
├── /tarifs/                              → Pricing page
├── /a-propos/                            → About page
├── /contact/                             → Contact
├── /mentions-legales/                    → Legal
└── /politique-de-confidentialite/        → Privacy
```

## 3. Architecture — Version anglaise

```
/en/
├── /en/seo-analyzer/                     → Tool page EN
├── /en/features/
│   ├── /en/features/on-page-seo-audit/
│   ├── /en/features/content-seo-analysis/
│   ├── /en/features/meta-tags-checker/
│   ├── /en/features/internal-link-analyzer/
│   └── /en/features/keyword-density-checker/
├── /en/blog/
│   ├── /en/blog/what-is-on-page-seo/                → Pillar EN 1
│   ├── /en/blog/on-page-seo-checklist/              → Pillar EN 2
│   ├── /en/blog/title-tag-seo-optimization/
│   ├── /en/blog/meta-description-best-practices/
│   ├── /en/blog/keyword-density-guide/
│   ├── /en/blog/internal-linking-seo/
│   ├── /en/blog/on-page-vs-off-page-seo/
│   └── /en/blog/heading-tags-h1-h2-h3-seo/
└── /en/pricing/
```

---

## 4. Périmètre de mots-clés — FRANÇAIS

### Cluster 1 — Outil principal (Intent : Commercial / Transactionnel)
*Pages cibles : /outil-seo/ + /accueil/*

| Mot-clé | Vol. mensuel (FR) | Difficulté | Priorité |
|---------|------------------|------------|---------|
| outil analyse seo on-page | 200–600 | Moyen | 🔴 P1 |
| analyseur seo on-page | 100–300 | Faible | 🔴 P1 |
| outil audit seo | 400–1 200 | Fort | 🔴 P1 |
| checker seo on-page | 100–300 | Faible | 🟠 P2 |
| vérificateur seo page | 100–300 | Faible | 🟠 P2 |
| analyser une page seo | 100–300 | Faible | 🟠 P2 |
| tester seo d'une page | 50–150 | Faible | 🟡 P3 |
| score seo page | 100–400 | Faible | 🟡 P3 |

### Cluster 2 — Audit SEO on-page (Intent : Informationnel → Transitionnel)
*Pages cibles : /blog/audit-seo-on-page-guide/ + /features/audit-technique-seo/*

| Mot-clé | Vol. mensuel | Difficulté | Priorité |
|---------|-------------|------------|---------|
| audit seo on-page | 400–1 500 | Moyen | 🔴 P1 |
| comment faire un audit seo on-page | 200–600 | Moyen | 🔴 P1 |
| checklist audit seo on-page | 100–400 | Faible | 🟠 P2 |
| audit sémantique seo | 100–400 | Faible | 🟠 P2 |
| audit technique seo on-page | 200–600 | Moyen | 🟠 P2 |
| grille audit seo | 50–150 | Faible | 🟡 P3 |
| checklist seo on-page | 100–400 | Faible | 🟠 P2 |

### Cluster 3 — SEO on-page généraliste (Intent : Informationnel)
*Pages cibles : /blog/seo-on-page-guide-complet/ + /accueil/*

| Mot-clé | Vol. mensuel | Difficulté | Priorité |
|---------|-------------|------------|---------|
| seo on-page | 1 000–3 000 | Fort | 🔴 P1 |
| optimisation on-page | 400–1 200 | Moyen | 🔴 P1 |
| c'est quoi le seo on-page | 200–600 | Faible | 🟠 P2 |
| seo on-page definition | 100–400 | Faible | 🟠 P2 |
| facteurs seo on-page | 100–400 | Moyen | 🟠 P2 |
| seo on-page vs off-page | 200–600 | Faible | 🟠 P2 |

### Cluster 4 — Balises meta (Intent : Info + Comm)
*Pages cibles : /features/balises-meta/ + blog articles*

| Mot-clé | Vol. mensuel | Difficulté | Priorité |
|---------|-------------|------------|---------|
| optimisation balise title | 200–600 | Moyen | 🔴 P1 |
| balise meta description seo | 300–900 | Moyen | 🔴 P1 |
| balise title seo | 300–900 | Moyen | 🔴 P1 |
| comment écrire une meta description | 100–400 | Faible | 🟠 P2 |
| longueur meta description | 100–400 | Faible | 🟠 P2 |
| balise title longueur | 100–300 | Faible | 🟠 P2 |
| balise open graph | 200–600 | Faible | 🟡 P3 |

### Cluster 5 — Sémantique & Contenu (Intent : Informationnel)
*Pages cibles : /features/analyse-semantique/ + /features/densite-mots-cles/*

| Mot-clé | Vol. mensuel | Difficulté | Priorité |
|---------|-------------|------------|---------|
| densité de mots-clés seo | 200–600 | Faible | 🔴 P1 |
| analyse sémantique seo | 100–400 | Faible | 🟠 P2 |
| champ sémantique seo | 100–300 | Faible | 🟠 P2 |
| densité de mots-clés | 300–900 | Faible | 🟠 P2 |
| richesse sémantique | 100–300 | Faible | 🟡 P3 |

### Cluster 6 — Maillage interne (Intent : Informationnel)
*Page cible : /features/maillage-interne/ + blog*

| Mot-clé | Vol. mensuel | Difficulté | Priorité |
|---------|-------------|------------|---------|
| maillage interne seo | 400–1 200 | Moyen | 🔴 P1 |
| lien interne seo | 300–900 | Moyen | 🔴 P1 |
| optimisation maillage interne | 100–300 | Faible | 🟠 P2 |
| analyse maillage interne site | 50–150 | Faible | 🟠 P2 |
| pagerank interne | 50–150 | Faible | 🟡 P3 |

### Cluster 7 — Structure Hn (Intent : Informationnel)
*Page cible : blog article*

| Mot-clé | Vol. mensuel | Difficulté | Priorité |
|---------|-------------|------------|---------|
| balises h1 h2 h3 seo | 200–600 | Faible | 🟠 P2 |
| optimisation balise h1 | 100–300 | Faible | 🟠 P2 |
| structure titres seo | 100–300 | Faible | 🟠 P2 |
| h1 unique seo | 50–150 | Faible | 🟡 P3 |

---

## 5. Périmètre de mots-clés — ENGLISH

### Cluster 1 — Main Tool (Intent: Commercial / Transactional)
*Target pages: /en/seo-analyzer/ + homepage*

| Keyword | Monthly Vol (EN) | KD | Priority |
|---------|-----------------|-----|---------|
| on-page seo analysis tool | 1 000–3 000 | Medium | 🔴 P1 |
| on-page seo checker | 2 000–6 000 | High | 🔴 P1 |
| on-page seo analyzer | 1 000–3 000 | Medium | 🔴 P1 |
| free on-page seo tool | 800–2 500 | Medium | 🔴 P1 |
| on-page seo audit tool | 600–2 000 | Medium | 🟠 P2 |
| seo page analyzer | 800–2 500 | Medium | 🟠 P2 |
| seo score checker | 600–2 000 | Medium | 🟠 P2 |
| website seo checker | 2 000–6 000 | High | 🟡 P3 |

### Cluster 2 — On-Page SEO Audit (Intent: Info/Commercial)
*Target pages: /en/blog/on-page-seo-checklist/ + feature pages*

| Keyword | Monthly Vol | KD | Priority |
|---------|------------|-----|---------|
| on-page seo audit | 2 000–6 000 | Medium | 🔴 P1 |
| on-page seo checklist | 1 000–3 000 | Medium | 🔴 P1 |
| how to do on-page seo | 1 000–3 000 | Medium | 🔴 P1 |
| on-page seo analysis | 1 000–3 000 | Medium | 🟠 P2 |
| on-page seo audit checklist | 400–1 500 | Low | 🟠 P2 |
| on-page seo guide | 800–2 500 | Medium | 🟠 P2 |

### Cluster 3 — On-Page Optimization (Intent: Informational)
*Target pages: /en/blog/what-is-on-page-seo/ + homepage*

| Keyword | Monthly Vol | KD | Priority |
|---------|------------|-----|---------|
| on-page seo | 5 000–15 000 | High | 🔴 P1 |
| on-page seo optimization | 2 000–6 000 | High | 🔴 P1 |
| what is on-page seo | 2 000–6 000 | Medium | 🔴 P1 |
| on-page vs off-page seo | 1 000–3 000 | Low | 🟠 P2 |
| on-page seo factors | 800–2 500 | Medium | 🟠 P2 |
| on-page seo examples | 400–1 500 | Low | 🟡 P3 |

### Cluster 4 — Meta Tags (Intent: Info/Commercial)
*Target pages: /en/features/meta-tags-checker/ + blog*

| Keyword | Monthly Vol | KD | Priority |
|---------|------------|-----|---------|
| title tag seo | 2 000–6 000 | Medium | 🔴 P1 |
| meta description seo | 2 000–6 000 | Medium | 🔴 P1 |
| title tag optimization | 1 000–3 000 | Medium | 🔴 P1 |
| meta tags checker | 600–2 000 | Low | 🟠 P2 |
| meta description checker | 400–1 500 | Low | 🟠 P2 |
| how to write meta description | 600–2 000 | Low | 🟠 P2 |
| title tag length | 400–1 500 | Low | 🟡 P3 |

### Cluster 5 — Keyword Density & Content (Intent: Info/Commercial)
*Target pages: /en/features/keyword-density-checker/ + blog*

| Keyword | Monthly Vol | KD | Priority |
|---------|------------|-----|---------|
| keyword density checker | 1 000–3 000 | Low | 🔴 P1 |
| keyword density | 2 000–6 000 | Medium | 🔴 P1 |
| keyword density tool | 800–2 500 | Low | 🟠 P2 |
| content seo analysis | 600–2 000 | Medium | 🟠 P2 |
| keyword density in seo | 400–1 500 | Low | 🟠 P2 |

### Cluster 6 — Internal Linking (Intent: Informational)
*Target pages: /en/features/internal-link-analyzer/ + blog*

| Keyword | Monthly Vol | KD | Priority |
|---------|------------|-----|---------|
| internal linking seo | 1 000–3 000 | Medium | 🔴 P1 |
| internal link analyzer | 400–1 500 | Low | 🔴 P1 |
| internal links seo | 1 000–3 000 | Medium | 🟠 P2 |
| internal linking strategy | 600–2 000 | Medium | 🟠 P2 |
| internal link checker | 400–1 500 | Low | 🟠 P2 |

### Cluster 7 — Heading Tags (Intent: Informational)
*Target page: /en/blog/heading-tags-h1-h2-h3-seo/*

| Keyword | Monthly Vol | KD | Priority |
|---------|------------|-----|---------|
| h1 tag seo | 800–2 500 | Low | 🟠 P2 |
| heading tags seo | 600–2 000 | Low | 🟠 P2 |
| h2 h3 tags seo | 400–1 500 | Low | 🟠 P2 |
| h1 tag optimization | 300–1 000 | Low | 🟡 P3 |

---

## 6. Maillage interne — Carte de linking

### Règles de maillage
- Chaque article de blog → lien vers `/outil-seo/` (CTA)
- Chaque article de blog → lien vers au moins 1 LP Feature correspondante
- Chaque LP Feature → lien vers `/outil-seo/`
- Chaque LP Feature → liens vers 2–3 articles de blog liés
- Homepage → liens vers toutes les LPs features
- Homepage → liens vers les 3 derniers articles blog

### Matrix interne

| Page source | Pages cibles |
|-------------|-------------|
| /outil-seo/ | /blog/audit-seo-on-page-guide/ + /blog/seo-on-page-guide-complet/ + /features/* |
| /features/audit-technique-seo/ | /outil-seo/ + /blog/audit-seo-on-page-guide/ + /blog/balises-hn-seo/ |
| /features/analyse-semantique/ | /outil-seo/ + /blog/analyse-semantique-seo/ + /features/densite-mots-cles/ |
| /features/balises-meta/ | /outil-seo/ + /blog/optimisation-balise-title/ + /blog/meta-description-seo/ |
| /features/maillage-interne/ | /outil-seo/ + /blog/maillage-interne-seo-guide/ |
| /features/densite-mots-cles/ | /outil-seo/ + /blog/densite-mots-cles-seo/ + /features/analyse-semantique/ |
| /blog/seo-on-page-guide-complet/ | /outil-seo/ + toutes /features/* + /blog/audit-seo-on-page-guide/ |
| /blog/audit-seo-on-page-guide/ | /outil-seo/ + /features/audit-technique-seo/ + /blog/seo-on-page-guide-complet/ |

---

## 7. Configuration WordPress requise

### Plugins indispensables
| Plugin | Usage |
|--------|-------|
| **Yoast SEO** ou **Rank Math** | Meta SEO, sitemap, schema |
| **Polylang** ou **WPML** | Gestion bilingue FR/EN |
| **WP Rocket** ou **LiteSpeed Cache** | Performances / Core Web Vitals |
| **Imagify** ou **ShortPixel** | Compression images WebP |
| **Redirection** | Gestion des redirections 301 |
| **WP Mail SMTP** | Livraison des emails transactionnels |
| **UpdraftPlus** | Sauvegardes automatiques |

### Configuration technique SEO
- URLs : `/%postname%/` ou `/%category%/%postname%/` pour le blog
- HTTPS obligatoire (certificat SSL)
- `robots.txt` : bloquer `/wp-admin/`, `/feed/`, `/wp-json/` (optionnel)
- Sitemap : généré par Yoast/Rank Math, soumis à GSC
- Balises `hreflang` sur toutes les pages bilingues
- Schema Organization sur le site entier
- Schema Article sur les posts de blog
- Schema FAQPage sur la homepage et les LPs
- Schema SoftwareApplication sur la page outil

### Checklist technique au lancement
- [ ] HTTPS actif
- [ ] Google Search Console configuré
- [ ] Google Analytics 4 installé
- [ ] Sitemap XML soumis
- [ ] Core Web Vitals : LCP < 2.5s, FID < 100ms, CLS < 0.1
- [ ] Toutes les images < 200 Ko (WebP)
- [ ] Balises hreflang sur toutes les pages
- [ ] Schema markup validé (Rich Results Test)
- [ ] Robots.txt correct
- [ ] Redirections 404 → 301 configurées

---

*Fin du document d'architecture — OnPageLab*
