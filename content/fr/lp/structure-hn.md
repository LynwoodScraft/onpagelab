---
meta_title: "Optimiser la structure des titres H1 H2 H3 SEO | OnPageLab"
meta_description: "Vérifiez la hiérarchie de vos titres Hn, la présence du mot-clé dans le H1, les sauts de niveaux. Score de structure et recommandations. Gratuit."
h1: "Analyse de la structure des titres Hn SEO"
url: "/features/structure-hn/"
canonical: "https://onpagelab.io/features/structure-hn/"
langue: "fr"
hreflang_en: "https://onpagelab.io/en/features/heading-structure/"
schema: SoftwareApplication + FAQPage + BreadcrumbList
couleur_feature: "teal"
icone_feature: "list"
---

## Analyse de la structure des titres Hn SEO

La hiérarchie H1–H2–H3 est le plan de votre contenu. Elle guide à la fois les lecteurs et les bots de Google. OnPageLab extrait et analyse toute la structure des titres de votre page pour détecter les erreurs et optimiser la pertinence sémantique.

**CTA :** [Analyser la structure de ma page →] → /outil-seo/

---

## Pourquoi la structure Hn est-elle importante pour le SEO ?

Les balises de titre (H1 à H6) remplissent trois fonctions essentielles :

**Signaux de pertinence pour Google** : Google utilise les titres pour comprendre la structure d'une page et identifier les sujets traités. Un H1 sans mot-clé, une absence de H2 ou un H1 dupliqué envoient des signaux faibles.

**Accessibilité** : Les lecteurs d'écran (pour les personnes malvoyantes) utilisent les titres pour naviguer dans le contenu. Une structure logique améliore l'accessibilité WCAG.

**Lisibilité et UX** : Des titres bien structurés permettent aux lecteurs de scanner le contenu rapidement, ce qui améliore le temps passé sur page.

---

## Ce qu'OnPageLab analyse dans vos titres Hn

### Balise H1
- **Présence** : chaque page doit avoir exactement un H1.
- **Unicité** : plusieurs H1 sur la même page est une erreur fréquente avec les page builders.
- **Longueur** : optimal entre 20 et 70 caractères.
- **Mot-clé principal** : présence du terme cible dans le H1 (exact ou variante).
- **Correspondance avec le title** : H1 et title doivent être complémentaires mais différents.

### Balises H2
- **Nombre** : pour un article de 1 500+ mots, au moins 3–5 H2 sont recommandés.
- **Mot-clé dans les H2** : présence du mot-clé ou de ses variantes dans les titres de section.
- **Longueur** : optimal entre 30 et 80 caractères.
- **Diversité** : les H2 ne doivent pas être répétitifs ou trop proches dans leur formulation.

### Balises H3–H6
- **Hiérarchie** : un H3 doit toujours être sous un H2 (pas de saut de H1 → H3 sans H2 intermédiaire).
- **Usage** : les H4–H6 sont rarement nécessaires. Leur présence en nombre excessif peut diluer la structure.
- **Présence de mots-clés secondaires** : les H3 peuvent cibler des variantes longue traîne.

### Sauts de niveaux
Erreur fréquente : passer d'un H1 directement à un H3, ou d'un H2 à un H4. OnPageLab détecte ces sauts et les signale car ils indiquent une structure incohérente.

---

## Visualisation de l'arbre Hn

OnPageLab génère un arbre visuel de la structure complète des titres de votre page :

```
H1 — SEO on-page : guide complet pour optimiser vos pages
 └── H2 — Qu'est-ce que le SEO on-page ?
      └── H3 — Les facteurs on-page techniques
      └── H3 — Les facteurs on-page sémantiques
 └── H2 — Comment optimiser sa balise title
      └── H3 — Longueur idéale du title
      └── H3 — Exemples de titles optimisés
 └── H2 — La structure Hn : guide complet
```

Cette visualisation permet d'identifier d'un coup d'œil les problèmes de hiérarchie, les titres trop longs, et la distribution des mots-clés dans la structure.

---

## Score de structure Hn

| Critère | Poids | Optimal |
|---|---|---|
| Un seul H1 | 30 % | Exactement 1 H1 |
| Mot-clé dans le H1 | 25 % | Présence exacte ou variante |
| Pas de saut de niveau | 20 % | Hiérarchie logique |
| Au moins 3 H2 | 15 % | ≥ 3 H2 (contenu long) |
| Mot-clé dans ≥ 1 H2 | 10 % | Présence dans min. 1 H2 |

---

## Erreurs de structure Hn les plus fréquentes

### H1 absent
Sans H1, Google ne reçoit aucun signal de titre principal. Le H1 est souvent généré automatiquement par les CMS mais peut être effacé par des thèmes mal configurés.

### Plusieurs H1 sur une même page
Certains templates WordPress ou constructeurs de pages placent le titre de la page ET le nom du site ou du blog en H1. Résultat : deux H1 = ambiguïté sur le sujet principal.

### H2 utilisé pour le style, pas pour la structure
Utiliser `<h2>` pour des éléments visuels (citations, call-to-action) plutôt que pour structurer le contenu dilue la pertinence sémantique.

### H1 identique au title
Le H1 et le title ont des rôles complémentaires. Ils devraient partager le mot-clé principal mais être formulés différemment : le title est orienté SERP (avec la marque), le H1 est orienté lecteur (plus long, plus descriptif).

### Sauts de niveaux
Passer directement de H1 à H3 sans H2 intermédiaire est une erreur de structure. Même si Google peut comprendre le contenu, c'est un signal d'incohérence architecturale.

---

## FAQ sur la structure Hn

**Peut-on avoir un H1 différent du title ?**
Oui, et c'est même recommandé. Le title est optimisé pour les SERP (sous 590 px, avec le nom de marque). Le H1 peut être plus long, plus descriptif, plus orienté lecteur. Les deux doivent contenir le mot-clé principal.

**Faut-il mettre le mot-clé dans chaque H2 ?**
Non. Sur-optimiser chaque titre avec le même mot-clé exact ressemble à du keyword stuffing. L'idéal est de l'inclure dans 1 à 3 H2 et d'utiliser des variantes sémantiques dans les autres.

**Les H4, H5, H6 ont-ils un impact SEO ?**
Impact minimal. Google lit les titres H4 à H6 comme du texte ordinaire en termes de poids sémantique. Utilisez-les uniquement si votre contenu nécessite vraiment un troisième niveau de sous-sections.

**Mon page builder crée des balises de titre avec des `<div>` stylisés. Est-ce un problème ?**
Oui. Un élément visuel qui ressemble à un titre H2 mais est codé en `<div class="faux-h2">` n'est pas reconnu comme titre par Google. Assurez-vous que vos titres utilisent les vraies balises HTML `<h1>` à `<h6>`.

**Quelle est la longueur idéale pour un H1 ?**
Entre 20 et 70 caractères. Trop court = trop vague. Trop long = difficile à lire et moins impactant. Le H1 doit être un titre clair, précis et incluant le mot-clé principal.

---

## Autres modules d'analyse OnPageLab

- [Audit technique SEO](/features/audit-technique/) — Title, meta, canonical, robots
- [Analyse sémantique](/features/analyse-semantique/) — Densité de mots-clés, couverture LSI
- [Optimisation des balises meta](/features/balises-meta/) — Longueur en pixels, prévisualisation SERP
- [Analyse du maillage interne](/features/maillage-interne/) — Liens internes et textes d'ancre
- [Score SEO global](/outil-seo/) — Rapport complet en 30 secondes

---

Liens internes :
- /outil-seo/ (CTA × 3)
- /features/audit-technique/
- /features/analyse-semantique/
- /features/balises-meta/
- /features/maillage-interne/
- /blog/guide-h1-h2-h3/
- /blog/seo-on-page-guide-complet/
