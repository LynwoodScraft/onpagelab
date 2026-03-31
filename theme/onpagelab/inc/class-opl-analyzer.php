<?php
/**
 * OnPageLab — SEO On-Page Analyzer Engine
 *
 * Crawle une URL, parse le HTML avec DOMDocument et évalue
 * 20 facteurs on-page pour produire un rapport structuré.
 *
 * @package OnPageLab
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class OPL_Analyzer {

    /* ──────────────────────────────────────────────
     *  Properties
     * ────────────────────────────────────────────── */

    private $url;
    private $keyword;
    private $dom;
    private $xpath;
    private $html;
    private $http_code = 0;
    private $error     = '';

    /** Parsed host of the target URL */
    private $target_host = '';

    /** Body text (stripped of tags) */
    private $body_text = '';

    /** Word count of body */
    private $word_count = 0;

    /** TTFB in milliseconds (measured during fetch) */
    private $ttfb_ms = 0;

    /** HTML response size in bytes */
    private $html_size = 0;

    /* ──────────────────────────────────────────────
     *  Public API
     * ────────────────────────────────────────────── */

    /**
     * Run a full on-page analysis.
     *
     * @param  string $url     Fully-qualified URL.
     * @param  string $keyword Optional target keyword.
     * @return array|WP_Error  Structured result or error.
     */
    public static function analyze( string $url, string $keyword = '' ) {
        $instance = new self( $url, $keyword );
        return $instance->run();
    }

    /* ──────────────────────────────────────────────
     *  Constructor
     * ────────────────────────────────────────────── */

    private function __construct( string $url, string $keyword ) {
        $this->url     = $url;
        $this->keyword = mb_strtolower( trim( $keyword ) );
        $parsed        = wp_parse_url( $url );
        $this->target_host = $parsed['host'] ?? '';
    }

    /* ──────────────────────────────────────────────
     *  Main pipeline
     * ────────────────────────────────────────────── */

    private function run() {

        /* 1 — Fetch */
        $fetched = $this->fetch_url();
        if ( is_wp_error( $fetched ) ) {
            return $fetched;
        }

        /* 2 — Parse */
        $parsed = $this->parse_html();
        if ( is_wp_error( $parsed ) ) {
            return $parsed;
        }

        /* 3 — Extract body text */
        $this->extract_body_text();

        /* 4 — Run 20 factor analyzers */
        $title      = $this->analyze_title_tag();
        $meta       = $this->analyze_meta_description();
        $h1         = $this->analyze_h1();
        $headings   = $this->analyze_heading_structure();
        $density    = $this->analyze_keyword_density();
        $content    = $this->analyze_content_length();
        $int_links  = $this->analyze_internal_links();
        $ext_links  = $this->analyze_external_links();
        $images     = $this->analyze_images();
        $canonical  = $this->analyze_canonical();
        $robots     = $this->analyze_robots_meta();
        $social     = $this->analyze_social_tags();
        /* New 8 analyzers */
        $url_struct = $this->analyze_url_structure();
        $page_speed = $this->analyze_page_speed();
        $viewport   = $this->analyze_viewport();
        $schema     = $this->analyze_structured_data();
        $richness   = $this->analyze_content_richness();
        $freshness  = $this->analyze_content_freshness();
        $robots_txt = $this->analyze_robots_txt();
        $sitemap    = $this->analyze_sitemap();

        /* 5 — Calculate sub-scores */
        $has_kw = ! empty( $this->keyword );

        $technique_score = $this->avg_scores([ $title, $meta, $canonical, $robots, $url_struct, $page_speed, $robots_txt ]);
        $structure_score = $this->avg_scores([ $h1, $headings, $richness ]);
        $maillage_score  = $this->avg_scores([ $int_links, $ext_links, $images ]);
        $balises_score   = $this->avg_scores([ $social, $schema, $viewport, $sitemap ]);

        if ( $has_kw ) {
            $semantic_score = $this->avg_scores([ $density, $content, $freshness ]);
        } else {
            $semantic_score = null;
        }

        /* 6 — Global score */
        if ( $has_kw ) {
            $global = round(
                $technique_score * 0.20 +
                $semantic_score  * 0.25 +
                $structure_score * 0.20 +
                $maillage_score  * 0.20 +
                $balises_score   * 0.15
            );
        } else {
            $global = round(
                $technique_score * 0.30 +
                $structure_score * 0.25 +
                $maillage_score  * 0.25 +
                $balises_score   * 0.20
            );
        }
        $global = max( 0, min( 100, $global ) );

        /* 7 — Build subscores array */
        $subscores = [
            [ 'label' => 'Technique',  'score' => (int) $technique_score ],
            [ 'label' => 'Structure',  'score' => (int) $structure_score ],
            [ 'label' => 'Maillage',   'score' => (int) $maillage_score ],
            [ 'label' => 'Balises',    'score' => (int) $balises_score ],
        ];
        if ( $has_kw ) {
            array_splice( $subscores, 1, 0, [
                [ 'label' => 'Sémantique', 'score' => (int) $semantic_score ],
            ]);
        }

        /* 8 — Collect issues (20 factors) */
        $issues = $this->collect_issues([
            $title, $meta, $h1, $headings, $density,
            $content, $int_links, $ext_links, $images,
            $canonical, $robots, $social,
            $url_struct, $page_speed, $viewport, $schema,
            $richness, $freshness, $robots_txt, $sitemap,
        ]);

        /* 9 — Build tab data */
        $technical_tab = $this->build_technical_tab(
            $title, $meta, $h1, $canonical, $robots, $social,
            $url_struct, $page_speed, $viewport, $schema, $robots_txt, $sitemap
        );
        $semantic_tab  = $has_kw
            ? $this->build_semantic_tab( $density, $content, $title, $h1, $headings, $richness, $freshness )
            : [];
        $links_tab     = $this->build_links_tab( $int_links, $ext_links, $images );

        /* 10 — Hn detailed analysis */
        $hn_tab = $this->analyze_hn_detailed();

        /* 11 — Return */
        return [
            'url'        => $this->url,
            'http_code'  => $this->http_code,
            'score'      => $global,
            'subscores'  => $subscores,
            'issues'     => $issues,
            'technical'  => $technical_tab,
            'semantic'   => $semantic_tab,
            'links'      => $links_tab,
            'hn'         => $hn_tab,
            'keyword'    => $this->keyword,
            'word_count' => $this->word_count,
        ];
    }

    /* ══════════════════════════════════════════════
     *  FETCHING & PARSING
     * ══════════════════════════════════════════════ */

    private function fetch_url() {
        $t_start  = microtime( true );
        $response = wp_remote_get( $this->url, [
            'timeout'     => 30,
            'redirection' => 5,
            'sslverify'   => false,
            'user-agent'  => 'OnPageLab/1.0 (+https://onpagelab.io)',
            'headers'     => [
                'Accept'          => 'text/html,application/xhtml+xml',
                'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
            ],
        ] );
        $this->ttfb_ms = (int) round( ( microtime( true ) - $t_start ) * 1000 );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'fetch_failed', sprintf(
                /* translators: %s: technical error message */
                __( 'Impossible de récupérer la page : %s', 'onpagelab' ),
                $response->get_error_message()
            ) );
        }

        $this->http_code = (int) wp_remote_retrieve_response_code( $response );
        $this->html      = wp_remote_retrieve_body( $response );
        $this->html_size = strlen( $this->html );

        if ( $this->http_code >= 400 ) {
            return new WP_Error( 'http_error', sprintf(
                __( 'Le serveur a répondu avec une erreur HTTP %d.', 'onpagelab' ),
                $this->http_code
            ) );
        }

        if ( empty( $this->html ) ) {
            return new WP_Error( 'empty_body', __( 'Le serveur a renvoyé une réponse vide.', 'onpagelab' ) );
        }

        return true;
    }

    private function parse_html() {
        $this->dom = new DOMDocument();

        /* Handle encoding */
        $html = $this->html;
        if ( ! preg_match( '/<meta[^>]+charset/i', $html ) ) {
            $html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html;
        }

        libxml_use_internal_errors( true );
        $loaded = $this->dom->loadHTML( $html, LIBXML_NOWARNING | LIBXML_NOERROR );
        libxml_clear_errors();

        if ( ! $loaded ) {
            return new WP_Error( 'parse_failed', __( 'Impossible de parser le HTML de la page.', 'onpagelab' ) );
        }

        $this->xpath = new DOMXPath( $this->dom );
        return true;
    }

    private function extract_body_text() {
        $body = $this->xpath->query( '//body' );
        if ( $body->length > 0 ) {
            /* Remove script/style content before extracting text */
            $remove = $this->xpath->query( '//script | //style | //noscript' );
            foreach ( $remove as $node ) {
                $node->parentNode->removeChild( $node );
            }
            $this->body_text = trim( preg_replace( '/\s+/', ' ', $body->item( 0 )->textContent ) );
        }
        $this->word_count = str_word_count( $this->body_text );
    }

    /* ══════════════════════════════════════════════
     *  12 FACTOR ANALYZERS
     *  Each returns: [ 'score' => int, 'data' => [...], 'issues' => [...] ]
     * ══════════════════════════════════════════════ */

    /* 1 — TITLE TAG ----------------------------------------- */
    private function analyze_title_tag(): array {
        $nodes = $this->xpath->query( '//head/title' );
        $title = $nodes->length > 0 ? trim( $nodes->item( 0 )->textContent ) : '';
        $issues = [];
        $score  = 0;

        if ( empty( $title ) ) {
            $issues[] = $this->issue( 'error', 'Balise title absente', 'Aucune balise <title> détectée. C\'est un signal critique pour le référencement.' );
            return [ 'score' => 0, 'data' => [ 'value' => '', 'pixel_width' => 0, 'char_count' => 0 ], 'issues' => $issues ];
        }

        $px    = $this->pixel_width( $title );
        $chars = mb_strlen( $title );
        $score = 70; // present

        /* Length scoring */
        if ( $px >= 480 && $px <= 580 ) {
            $score = 100;
        } elseif ( $px < 200 ) {
            $score = 30;
            $issues[] = $this->issue( 'error', 'Title trop court', sprintf( '%d px (%d car.), optimal : 480–580 px.', $px, $chars ) );
        } elseif ( $px > 580 ) {
            $score = 60;
            $issues[] = $this->issue( 'warning', 'Title trop long', sprintf( '%d px (%d car.), Google va tronquer le titre dans les SERP. Optimal : 480–580 px.', $px, $chars ) );
        }

        /* Keyword check */
        $has_kw = false;
        if ( $this->keyword ) {
            $has_kw = $this->contains_keyword( $title );
            if ( ! $has_kw ) {
                $score -= 15;
                $issues[] = $this->issue( 'warning', 'Mot-clé absent du title', sprintf( 'Le mot-clé « %s » n\'apparaît pas dans la balise title.', $this->keyword ) );
            } else {
                $pos = mb_stripos( $title, $this->keyword );
                if ( $pos !== false && $pos <= 5 ) {
                    $issues[] = $this->issue( 'success', 'Mot-clé en début de title', 'Le mot-clé apparaît en début de balise title, bonne pratique.' );
                }
            }
        }

        if ( empty( $issues ) ) {
            $issues[] = $this->issue( 'success', 'Balise title optimisée', sprintf( '%d px (%d car.), longueur idéale.', $px, $chars ) );
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [ 'value' => $title, 'pixel_width' => $px, 'char_count' => $chars, 'has_keyword' => $has_kw ],
            'issues' => $issues,
        ];
    }

    /* 2 — META DESCRIPTION ---------------------------------- */
    private function analyze_meta_description(): array {
        $node = $this->xpath->query( '//head/meta[@name="description"]/@content' );
        $desc = $node->length > 0 ? trim( $node->item( 0 )->nodeValue ) : '';
        $issues = [];

        if ( empty( $desc ) ) {
            $issues[] = $this->issue( 'warning', 'Meta description absente', 'Google pourrait en générer une automatiquement, mais elle ne sera pas optimisée.' );
            return [ 'score' => 0, 'data' => [ 'value' => '', 'pixel_width' => 0, 'char_count' => 0 ], 'issues' => $issues ];
        }

        $px    = $this->pixel_width( $desc );
        $chars = mb_strlen( $desc );
        $score = 70;

        if ( $px >= 400 && $px <= 920 ) {
            $score = 100;
        } elseif ( $px < 300 ) {
            $score = 40;
            $issues[] = $this->issue( 'warning', 'Meta description trop courte', sprintf( '%d px (%d car.), optimal : 400–920 px (≈120–160 car.).', $px, $chars ) );
        } elseif ( $px > 920 ) {
            $score = 65;
            $issues[] = $this->issue( 'warning', 'Meta description trop longue', sprintf( '%d px (%d car.), Google va tronquer. Optimal : 400–920 px.', $px, $chars ) );
        }

        if ( $this->keyword ) {
            if ( ! $this->contains_keyword( $desc ) ) {
                $score -= 10;
                $issues[] = $this->issue( 'warning', 'Mot-clé absent de la meta description', 'Google met en gras les mots recherchés, inclure le mot-clé améliore le CTR.' );
            }
        }

        if ( empty( $issues ) ) {
            $issues[] = $this->issue( 'success', 'Meta description optimisée', sprintf( '%d px (%d car.), longueur idéale.', $px, $chars ) );
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [ 'value' => $desc, 'pixel_width' => $px, 'char_count' => $chars ],
            'issues' => $issues,
        ];
    }

    /* 3 — H1 TAG -------------------------------------------- */
    private function analyze_h1(): array {
        $nodes  = $this->xpath->query( '//h1' );
        $count  = $nodes->length;
        $issues = [];
        $h1_text = $count > 0 ? trim( $nodes->item( 0 )->textContent ) : '';

        if ( $count === 0 ) {
            $issues[] = $this->issue( 'error', 'Balise H1 absente', 'Chaque page doit posséder exactement un H1 décrivant le sujet principal.' );
            return [ 'score' => 0, 'data' => [ 'value' => '', 'count' => 0 ], 'issues' => $issues ];
        }

        $score = 80;

        if ( $count > 1 ) {
            $score = 50;
            $issues[] = $this->issue( 'warning', 'Plusieurs H1 détectés', sprintf( '%d balises H1 trouvées. Une seule est recommandée par page.', $count ) );
        }

        $len = mb_strlen( $h1_text );
        if ( $len < 10 ) {
            $score -= 15;
            $issues[] = $this->issue( 'warning', 'H1 très court', sprintf( '%d caractères, un H1 trop court peut manquer de contexte.', $len ) );
        } elseif ( $len > 100 ) {
            $score -= 10;
            $issues[] = $this->issue( 'warning', 'H1 trop long', sprintf( '%d caractères, préférez un H1 concis (30–70 car.).', $len ) );
        }

        $has_kw = false;
        if ( $this->keyword ) {
            $has_kw = $this->contains_keyword( $h1_text );
            if ( $has_kw ) {
                $score = min( 100, $score + 20 );
                $issues[] = $this->issue( 'success', 'Mot-clé présent dans le H1', 'Le H1 contient le mot-clé cible.' );
            } else {
                $score -= 15;
                $issues[] = $this->issue( 'warning', 'Mot-clé absent du H1', sprintf( 'Le mot-clé « %s » n\'apparaît pas dans le H1.', $this->keyword ) );
            }
        }

        if ( $count === 1 && empty( $issues ) ) {
            $issues[] = $this->issue( 'success', 'H1 unique et bien formé', $h1_text );
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [ 'value' => $h1_text, 'count' => $count, 'has_keyword' => $has_kw ],
            'issues' => $issues,
        ];
    }

    /* 4 — HEADING STRUCTURE --------------------------------- */
    private function analyze_heading_structure(): array {
        $headings = [];
        $issues   = [];

        for ( $lvl = 1; $lvl <= 6; $lvl++ ) {
            $nodes = $this->xpath->query( "//h{$lvl}" );
            foreach ( $nodes as $node ) {
                $headings[] = [
                    'tag'  => "H{$lvl}",
                    'level' => $lvl,
                    'text' => trim( $node->textContent ),
                ];
            }
        }

        /* Re-order by document order (DOMXPath returns by query order) */
        /* We keep the array as-is since we query H1→H6 sequentially */

        $score = 80;
        $h2_count = 0;
        $h2_with_kw = 0;
        $prev_level = 0;
        $jumps = [];

        foreach ( $headings as $h ) {
            if ( $h['level'] === 2 ) {
                $h2_count++;
                if ( $this->keyword && $this->contains_keyword( $h['text'] ) ) {
                    $h2_with_kw++;
                }
            }

            /* Detect level jumps (e.g. H1 → H3, skipping H2) */
            if ( $prev_level > 0 && $h['level'] > $prev_level + 1 ) {
                $jumps[] = sprintf( 'H%d → H%d', $prev_level, $h['level'] );
            }
            $prev_level = $h['level'];
        }

        if ( count( $headings ) < 2 ) {
            $score = 30;
            $issues[] = $this->issue( 'warning', 'Structure de titres trop pauvre', 'Moins de 2 titres détectés. Structurez le contenu avec des H2/H3.' );
        }

        if ( ! empty( $jumps ) ) {
            $score -= 20;
            $issues[] = $this->issue( 'warning', 'Saut de niveau dans la hiérarchie Hn', 'Sauts détectés : ' . implode( ', ', $jumps ) . '. Respectez la cascade H1 → H2 → H3.' );
        }

        if ( $h2_count === 0 && count( $headings ) > 0 ) {
            $score -= 15;
            $issues[] = $this->issue( 'warning', 'Aucun H2 détecté', 'Les H2 structurent votre contenu et aident Google à comprendre les sous-thèmes.' );
        } elseif ( $h2_count < 3 && $this->word_count > 600 ) {
            $issues[] = $this->issue( 'warning', 'Peu de H2', sprintf( '%d H2 pour %d mots, ajoutez des sous-titres pour un contenu plus lisible.', $h2_count, $this->word_count ) );
        }

        if ( $this->keyword && $h2_count > 0 && $h2_with_kw === 0 ) {
            $score -= 10;
            $issues[] = $this->issue( 'warning', 'Mot-clé absent des H2', 'Incluez le mot-clé dans au moins un H2 pour renforcer la pertinence.' );
        }

        if ( empty( $issues ) ) {
            $issues[] = $this->issue( 'success', 'Hiérarchie des titres correcte', sprintf( '%d titres, pas de saut de niveau.', count( $headings ) ) );
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [
                'tree'       => $headings,
                'total'      => count( $headings ),
                'h2_count'   => $h2_count,
                'h2_with_kw' => $h2_with_kw,
                'jumps'      => $jumps,
            ],
            'issues' => $issues,
        ];
    }

    /* 5 — KEYWORD DENSITY ----------------------------------- */
    private function analyze_keyword_density(): array {
        $issues = [];

        if ( empty( $this->keyword ) ) {
            return [ 'score' => 0, 'data' => [], 'issues' => [] ];
        }

        $body_lower = mb_strtolower( $this->body_text );
        $kw_lower   = $this->keyword;

        /* Count occurrences */
        $occurrences = mb_substr_count( $body_lower, $kw_lower );
        $density     = $this->word_count > 0 ? round( ( $occurrences / $this->word_count ) * 100, 2 ) : 0;

        /* Score */
        $score = 50;
        if ( $density >= 0.5 && $density <= 2.0 ) {
            $score = 100;
        } elseif ( $density > 2.0 && $density <= 3.0 ) {
            $score = 70;
            $issues[] = $this->issue( 'warning', 'Densité de mot-clé élevée', sprintf( '%.1f %%, risque de sur-optimisation. Optimal : 0,5–2 %%.', $density ) );
        } elseif ( $density > 3.0 ) {
            $score = 30;
            $issues[] = $this->issue( 'error', 'Keyword stuffing détecté', sprintf( '%.1f %%, trop d\'occurrences du mot-clé. Réduisez à 0,5–2 %%.', $density ) );
        } elseif ( $density > 0 && $density < 0.5 ) {
            $score = 65;
            $issues[] = $this->issue( 'warning', 'Densité de mot-clé faible', sprintf( '%.1f %% (%d occurrences sur %d mots). Optimal : 0,5–2 %%.', $density, $occurrences, $this->word_count ) );
        } elseif ( $occurrences === 0 ) {
            $score = 10;
            $issues[] = $this->issue( 'error', 'Mot-clé absent du contenu', sprintf( 'Le mot-clé « %s » n\'apparaît pas dans le texte de la page.', $this->keyword ) );
        }

        /* First 100 words check */
        $first_words = implode( ' ', array_slice( explode( ' ', $body_lower ), 0, 100 ) );
        $in_first_100 = mb_strpos( $first_words, $kw_lower ) !== false;
        if ( ! $in_first_100 && $occurrences > 0 ) {
            $issues[] = $this->issue( 'warning', 'Mot-clé absent des 100 premiers mots', 'Placez le mot-clé dans l\'introduction pour signaler la pertinence.' );
        } elseif ( $in_first_100 ) {
            $issues[] = $this->issue( 'success', 'Mot-clé dans les 100 premiers mots', 'Bonne pratique : le mot-clé est présent dès l\'introduction.' );
        }

        if ( $density >= 0.5 && $density <= 2.0 ) {
            $issues[] = $this->issue( 'success', 'Densité de mot-clé optimale', sprintf( '%.1f %%, dans la fourchette recommandée.', $density ) );
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [
                'keyword'      => $this->keyword,
                'occurrences'  => $occurrences,
                'word_count'   => $this->word_count,
                'density'      => $density,
                'in_first_100' => $in_first_100,
            ],
            'issues' => $issues,
        ];
    }

    /* 6 — CONTENT LENGTH ------------------------------------ */
    private function analyze_content_length(): array {
        $wc     = $this->word_count;
        $issues = [];
        $score  = 50;

        if ( $wc < 100 ) {
            $score = 10;
            $issues[] = $this->issue( 'error', 'Contenu très insuffisant', sprintf( '%d mots, un minimum de 300 mots est recommandé pour le référencement.', $wc ) );
        } elseif ( $wc < 300 ) {
            $score = 30;
            $issues[] = $this->issue( 'warning', 'Contenu trop court', sprintf( '%d mots, les pages de moins de 300 mots peinent à se positionner.', $wc ) );
        } elseif ( $wc < 600 ) {
            $score = 70;
            $issues[] = $this->issue( 'warning', 'Contenu correct mais léger', sprintf( '%d mots, suffisant, mais un contenu plus riche (600+ mots) améliorerait le positionnement.', $wc ) );
        } elseif ( $wc <= 3000 ) {
            $score = 100;
            $issues[] = $this->issue( 'success', 'Longueur du contenu optimale', sprintf( '%d mots, contenu riche et détaillé.', $wc ) );
        } else {
            $score = 90;
            $issues[] = $this->issue( 'success', 'Contenu très long', sprintf( '%d mots, excellent niveau de détail.', $wc ) );
        }

        return [ 'score' => $score, 'data' => [ 'word_count' => $wc ], 'issues' => $issues ];
    }

    /* 7 — INTERNAL LINKS ------------------------------------ */
    private function analyze_internal_links(): array {
        $links = $this->xpath->query( '//a[@href]' );
        $internal = [];
        $issues   = [];

        foreach ( $links as $a ) {
            $href = trim( $a->getAttribute( 'href' ) );
            if ( empty( $href ) || strpos( $href, '#' ) === 0 || strpos( $href, 'javascript:' ) === 0 || strpos( $href, 'mailto:' ) === 0 || strpos( $href, 'tel:' ) === 0 ) {
                continue;
            }
            if ( $this->is_internal_link( $href ) ) {
                $rel = strtolower( $a->getAttribute( 'rel' ) );
                $internal[] = [
                    'url'    => $href,
                    'anchor' => trim( $a->textContent ),
                    'rel'    => str_contains( $rel, 'nofollow' ) ? 'nofollow' : 'dofollow',
                ];
            }
        }

        $total     = count( $internal );
        $dofollow  = count( array_filter( $internal, fn( $l ) => $l['rel'] === 'dofollow' ) );
        $nofollow  = $total - $dofollow;
        $score     = 50;

        if ( $total === 0 ) {
            $score = 0;
            $issues[] = $this->issue( 'error', 'Aucun lien interne', 'Cette page est orpheline côté maillage, ajoutez des liens vers d\'autres pages de votre site.' );
        } elseif ( $total < 3 ) {
            $score = 40;
            $issues[] = $this->issue( 'warning', 'Peu de liens internes', sprintf( '%d lien(s) interne(s) seulement. Visez au minimum 3–5 pour un maillage efficace.', $total ) );
        } elseif ( $total <= 20 ) {
            $score = 100;
            $issues[] = $this->issue( 'success', 'Maillage interne correct', sprintf( '%d liens internes dont %d dofollow.', $total, $dofollow ) );
        } else {
            $score = 85;
            $issues[] = $this->issue( 'success', 'Maillage interne riche', sprintf( '%d liens internes trouvés.', $total ) );
        }

        /* Anchor text quality */
        $generic_anchors = [ 'cliquez ici', 'click here', 'en savoir plus', 'lire la suite', 'ici', 'here', 'link', 'lien', 'plus' ];
        $generic_count   = 0;
        foreach ( $internal as $l ) {
            if ( in_array( mb_strtolower( trim( $l['anchor'] ) ), $generic_anchors, true ) ) {
                $generic_count++;
            }
        }
        if ( $generic_count > 0 ) {
            $score -= 10;
            $issues[] = $this->issue( 'warning', 'Ancres génériques détectées', sprintf( '%d lien(s) avec ancre générique (« cliquez ici », « en savoir plus »). Utilisez des ancres descriptives.', $generic_count ) );
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [
                'total'     => $total,
                'dofollow'  => $dofollow,
                'nofollow'  => $nofollow,
                'details'   => array_slice( $internal, 0, 20 ),
            ],
            'issues' => $issues,
        ];
    }

    /* 8 — EXTERNAL LINKS ------------------------------------ */
    private function analyze_external_links(): array {
        $links    = $this->xpath->query( '//a[@href]' );
        $external = [];
        $issues   = [];

        foreach ( $links as $a ) {
            $href = trim( $a->getAttribute( 'href' ) );
            if ( empty( $href ) || strpos( $href, '#' ) === 0 || strpos( $href, 'javascript:' ) === 0 || strpos( $href, 'mailto:' ) === 0 || strpos( $href, 'tel:' ) === 0 ) {
                continue;
            }
            if ( ! $this->is_internal_link( $href ) && preg_match( '#^https?://#', $href ) ) {
                $rel = strtolower( $a->getAttribute( 'rel' ) );
                $external[] = [
                    'url'    => $href,
                    'anchor' => trim( $a->textContent ),
                    'rel'    => str_contains( $rel, 'nofollow' ) ? 'nofollow' : 'dofollow',
                ];
            }
        }

        $total    = count( $external );
        $dofollow = count( array_filter( $external, fn( $l ) => $l['rel'] === 'dofollow' ) );
        $nofollow = $total - $dofollow;
        $score    = 60;

        if ( $total === 0 ) {
            $score = 40;
            $issues[] = $this->issue( 'warning', 'Aucun lien externe', 'Lier vers des sources fiables renforce la crédibilité de votre contenu.' );
        } elseif ( $total <= 5 ) {
            $score = 100;
            $issues[] = $this->issue( 'success', 'Liens externes présents', sprintf( '%d lien(s) externe(s), bon équilibre.', $total ) );
        } else {
            $score = 80;
            $issues[] = $this->issue( 'success', 'Liens externes nombreux', sprintf( '%d liens externes trouvés.', $total ) );
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [
                'total'    => $total,
                'dofollow' => $dofollow,
                'nofollow' => $nofollow,
                'details'  => array_slice( $external, 0, 15 ),
            ],
            'issues' => $issues,
        ];
    }

    /* 9 — IMAGES -------------------------------------------- */
    private function analyze_images(): array {
        $imgs   = $this->xpath->query( '//img' );
        $total  = $imgs->length;
        $issues = [];
        $with_alt    = 0;
        $without_alt = 0;
        $kw_in_alt   = 0;

        foreach ( $imgs as $img ) {
            $alt = trim( $img->getAttribute( 'alt' ) );
            if ( ! empty( $alt ) ) {
                $with_alt++;
                if ( $this->keyword && $this->contains_keyword( $alt ) ) {
                    $kw_in_alt++;
                }
            } else {
                $without_alt++;
            }
        }

        $score = 70;

        if ( $total === 0 ) {
            $score = 40;
            $issues[] = $this->issue( 'warning', 'Aucune image détectée', 'Les images enrichissent le contenu et améliorent l\'engagement. Envisagez d\'en ajouter.' );
        } elseif ( $without_alt > 0 ) {
            $ratio = $with_alt / $total;
            $score = (int) ( $ratio * 100 );
            $issues[] = $this->issue( 'warning', 'Images sans attribut alt', sprintf( '%d image(s) sur %d n\'ont pas d\'attribut alt. L\'alt est essentiel pour l\'accessibilité et le SEO.', $without_alt, $total ) );
        } else {
            $score = 100;
            $issues[] = $this->issue( 'success', 'Toutes les images ont un alt', sprintf( '%d image(s), toutes avec attribut alt.', $total ) );
        }

        if ( $this->keyword && $total > 0 && $kw_in_alt === 0 ) {
            $score -= 10;
            $issues[] = $this->issue( 'warning', 'Mot-clé absent des alt', 'Aucune image ne mentionne le mot-clé dans son attribut alt.' );
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [
                'total'       => $total,
                'with_alt'    => $with_alt,
                'without_alt' => $without_alt,
                'kw_in_alt'   => $kw_in_alt,
            ],
            'issues' => $issues,
        ];
    }

    /* 10 — CANONICAL ---------------------------------------- */
    private function analyze_canonical(): array {
        $node = $this->xpath->query( '//head/link[@rel="canonical"]/@href' );
        $canonical = $node->length > 0 ? trim( $node->item( 0 )->nodeValue ) : '';
        $issues = [];

        if ( empty( $canonical ) ) {
            $issues[] = $this->issue( 'warning', 'Balise canonical absente', 'Ajoutez une balise canonical auto-référente pour éviter les problèmes de contenu dupliqué.' );
            return [ 'score' => 30, 'data' => [ 'value' => '', 'is_self' => false ], 'issues' => $issues ];
        }

        $is_self = $this->normalize_url( $canonical ) === $this->normalize_url( $this->url );

        if ( $is_self ) {
            $issues[] = $this->issue( 'success', 'Canonical auto-référente', 'La balise canonical pointe vers l\'URL de la page, c\'est correct.' );
            return [ 'score' => 100, 'data' => [ 'value' => $canonical, 'is_self' => true ], 'issues' => $issues ];
        }

        /* External canonical */
        $issues[] = $this->issue( 'warning', 'Canonical vers une autre URL', sprintf( 'La canonical pointe vers %s, cette page ne sera pas indexée en tant que telle.', $canonical ) );
        return [ 'score' => 60, 'data' => [ 'value' => $canonical, 'is_self' => false ], 'issues' => $issues ];
    }

    /* 11 — ROBOTS META -------------------------------------- */
    private function analyze_robots_meta(): array {
        $node  = $this->xpath->query( '//head/meta[@name="robots"]/@content' );
        $value = $node->length > 0 ? mb_strtolower( trim( $node->item( 0 )->nodeValue ) ) : '';
        $issues = [];

        if ( empty( $value ) ) {
            /* No robots meta = index, follow by default */
            $issues[] = $this->issue( 'success', 'Pas de balise robots restrictive', 'Par défaut, la page est indexable et les liens sont suivis.' );
            return [ 'score' => 100, 'data' => [ 'value' => 'index, follow (défaut)', 'is_indexable' => true ], 'issues' => $issues ];
        }

        $is_noindex  = str_contains( $value, 'noindex' );
        $is_nofollow = str_contains( $value, 'nofollow' );

        if ( $is_noindex ) {
            $issues[] = $this->issue( 'error', 'Page en noindex', 'La balise robots contient « noindex », cette page ne sera pas indexée par Google.' );
            return [ 'score' => 0, 'data' => [ 'value' => $value, 'is_indexable' => false ], 'issues' => $issues ];
        }

        if ( $is_nofollow ) {
            $issues[] = $this->issue( 'warning', 'Liens en nofollow', 'La balise robots contient « nofollow », Google ne suivra pas les liens de cette page.' );
            return [ 'score' => 60, 'data' => [ 'value' => $value, 'is_indexable' => true ], 'issues' => $issues ];
        }

        $issues[] = $this->issue( 'success', 'Balise robots correcte', $value );
        return [ 'score' => 100, 'data' => [ 'value' => $value, 'is_indexable' => true ], 'issues' => $issues ];
    }

    /* 12 — OPEN GRAPH & TWITTER CARD ----------------------- */
    private function analyze_social_tags(): array {
        $og_tags = [
            'og:title'       => '',
            'og:description' => '',
            'og:image'       => '',
            'og:url'         => '',
            'og:type'        => '',
        ];
        $tw_tags = [
            'twitter:card'        => '',
            'twitter:title'       => '',
            'twitter:description' => '',
            'twitter:image'       => '',
        ];

        /* OG */
        foreach ( array_keys( $og_tags ) as $prop ) {
            $node = $this->xpath->query( sprintf( '//head/meta[@property="%s"]/@content', $prop ) );
            if ( $node->length > 0 ) {
                $og_tags[ $prop ] = trim( $node->item( 0 )->nodeValue );
            }
        }

        /* Twitter */
        foreach ( array_keys( $tw_tags ) as $name ) {
            $node = $this->xpath->query( sprintf( '//head/meta[@name="%s"]/@content', $name ) );
            if ( $node->length > 0 ) {
                $tw_tags[ $name ] = trim( $node->item( 0 )->nodeValue );
            }
        }

        $og_present  = array_filter( $og_tags );
        $og_missing  = array_diff_key( $og_tags, $og_present );
        $tw_present  = array_filter( $tw_tags );
        $tw_missing  = array_diff_key( $tw_tags, $tw_present );
        $total_found = count( $og_present ) + count( $tw_present );
        $issues      = [];

        if ( $total_found === 0 ) {
            $score = 0;
            $issues[] = $this->issue( 'warning', 'Aucune balise sociale', 'Aucune balise Open Graph ni Twitter Card, le partage social ne sera pas optimisé.' );
        } elseif ( $total_found <= 3 ) {
            $score = 40;
            $issues[] = $this->issue( 'warning', 'Balises sociales incomplètes', sprintf( '%d balise(s) présente(s) sur 9. Complétez og:title, og:description, og:image au minimum.', $total_found ) );
        } elseif ( $total_found <= 6 ) {
            $score = 70;
        } else {
            $score = 100;
        }

        if ( empty( $og_tags['og:image'] ) && $score > 0 ) {
            $issues[] = $this->issue( 'warning', 'og:image manquant', 'Sans image OG, les partages sur Facebook/LinkedIn afficheront une vignette par défaut.' );
        }

        if ( $total_found >= 7 ) {
            $issues[] = $this->issue( 'success', 'Balises sociales complètes', sprintf( '%d balises Open Graph et Twitter Card détectées.', $total_found ) );
        }

        return [
            'score'  => $score,
            'data'   => [
                'og_present'  => array_keys( $og_present ),
                'og_missing'  => array_keys( $og_missing ),
                'tw_present'  => array_keys( $tw_present ),
                'tw_missing'  => array_keys( $tw_missing ),
                'og_values'   => $og_tags,
                'tw_values'   => $tw_tags,
            ],
            'issues' => $issues,
        ];
    }


    /* 13 — URL STRUCTURE ----------------------------------- */
    private function analyze_url_structure(): array {
        $issues = [];
        $score  = 100;
        $parsed = wp_parse_url( $this->url );

        $is_https = ( $parsed['scheme'] ?? '' ) === 'https';
        if ( ! $is_https ) {
            $score -= 30;
            $issues[] = $this->issue( 'error', 'URL non sécurisée (HTTP)', 'La page utilise HTTP. Google favorise les pages HTTPS. Migrez vers HTTPS.' );
        } else {
            $issues[] = $this->issue( 'success', 'URL sécurisée (HTTPS)', 'La page est servie en HTTPS.' );
        }

        $url_length = mb_strlen( $this->url );
        if ( $url_length > 115 ) {
            $score -= 15;
            $issues[] = $this->issue( 'warning', 'URL trop longue', sprintf( 'L\'URL fait %d caractères. Préférez des URLs courtes (< 115 car.).', $url_length ) );
        }

        $path  = $parsed['path'] ?? '/';
        $depth = max( 0, substr_count( rtrim( $path, '/' ), '/' ) );
        if ( $depth > 3 ) {
            $score -= 10;
            $issues[] = $this->issue( 'warning', 'URL trop profonde', sprintf( 'L\'URL est à %d niveaux de profondeur. Préférez 3 niveaux maximum pour le crawl budget.', $depth ) );
        }

        if ( ! empty( $this->keyword ) ) {
            $kw_slug   = str_replace( ' ', '-', mb_strtolower( $this->keyword ) );
            $kw_plain  = str_replace( ' ', '', mb_strtolower( $this->keyword ) );
            $url_lower = mb_strtolower( $this->url );
            $has_kw_url = mb_strpos( $url_lower, $kw_slug ) !== false || mb_strpos( $url_lower, $kw_plain ) !== false;
            if ( ! $has_kw_url ) {
                $score -= 10;
                $issues[] = $this->issue( 'warning', 'Mot-clé absent de l\'URL', 'Inclure le mot-clé dans l\'URL améliore le titlematchScore.' );
            } else {
                $issues[] = $this->issue( 'success', 'Mot-clé présent dans l\'URL', 'Le mot-clé est visible dans l\'URL.' );
            }
        }

        if ( preg_match( '/[^a-zA-Z0-9\-\_\/\.\~\%]/', urldecode( $path ) ) ) {
            $score -= 5;
            $issues[] = $this->issue( 'warning', 'Caractères spéciaux dans l\'URL', 'Évitez les accents, majuscules et caractères spéciaux dans les slugs d\'URL.' );
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [
                'is_https'   => $is_https,
                'url_length' => $url_length,
                'depth'      => $depth,
            ],
            'issues' => $issues,
        ];
    }

    /* 14 — PAGE SPEED (TTFB) -------------------------------- */
    private function analyze_page_speed(): array {
        $issues = [];
        $ttfb   = $this->ttfb_ms;
        $score  = 100;

        if ( $ttfb === 0 ) {
            $issues[] = $this->issue( 'warning', 'TTFB non mesuré', 'Le temps de réponse serveur n\'a pas pu être mesuré.' );
            return [ 'score' => 50, 'data' => [ 'ttfb_ms' => 0, 'html_size_kb' => 0 ], 'issues' => $issues ];
        }

        $size_kb = round( $this->html_size / 1024, 1 );

        if ( $ttfb < 200 ) {
            $issues[] = $this->issue( 'success', 'Excellent TTFB', sprintf( 'Le serveur a répondu en %d ms (objectif Core Web Vitals : < 200 ms).', $ttfb ) );
        } elseif ( $ttfb < 600 ) {
            $score -= 20;
            $issues[] = $this->issue( 'warning', 'TTFB moyen', sprintf( 'Le serveur a répondu en %d ms. Visez < 200 ms (cache, CDN, optimisation serveur).', $ttfb ) );
        } else {
            $score -= 45;
            $issues[] = $this->issue( 'error', 'TTFB trop élevé', sprintf( 'Le serveur a répondu en %d ms. Ce délai pénalise les Core Web Vitals et l\'expérience utilisateur.', $ttfb ) );
        }

        if ( $size_kb > 500 ) {
            $score -= 15;
            $issues[] = $this->issue( 'warning', 'Page HTML volumineuse', sprintf( 'La page HTML fait %.1f Ko. Un HTML léger (< 100 Ko) améliore la vitesse de rendu.', $size_kb ) );
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [
                'ttfb_ms'      => $ttfb,
                'html_size_kb' => $size_kb,
            ],
            'issues' => $issues,
        ];
    }

    /* 15 — VIEWPORT & HTML LANG ----------------------------- */
    private function analyze_viewport(): array {
        $issues = [];
        $score  = 100;

        $vp_node  = $this->xpath->query( '//head/meta[@name="viewport"]/@content' );
        $viewport = $vp_node->length > 0 ? trim( $vp_node->item( 0 )->nodeValue ) : '';

        if ( empty( $viewport ) ) {
            $score -= 40;
            $issues[] = $this->issue( 'error', 'Balise viewport absente', 'La balise <meta name="viewport"> est indispensable pour la compatibilité mobile (Mobile-First Indexing).' );
        } elseif ( strpos( $viewport, 'width=device-width' ) === false ) {
            $score -= 20;
            $issues[] = $this->issue( 'warning', 'Viewport mal configurée', 'Utilisez content="width=device-width, initial-scale=1" pour une compatibilité mobile optimale.' );
        } else {
            $issues[] = $this->issue( 'success', 'Balise viewport présente', $viewport );
        }

        $lang_node = $this->xpath->query( '/html/@lang' );
        $lang      = $lang_node->length > 0 ? trim( $lang_node->item( 0 )->nodeValue ) : '';

        if ( empty( $lang ) ) {
            $score -= 15;
            $issues[] = $this->issue( 'warning', 'Attribut lang absent', 'Ajoutez lang="fr" (ou la langue appropriée) à la balise <html> pour aider Google et les LLMs.' );
        } else {
            $issues[] = $this->issue( 'success', 'Attribut lang défini', sprintf( 'lang="%s"', $lang ) );
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [
                'viewport' => $viewport,
                'lang'     => $lang,
            ],
            'issues' => $issues,
        ];
    }

    /* 16 — STRUCTURED DATA (JSON-LD) ------------------------ */
    private function analyze_structured_data(): array {
        $issues = [];
        $score  = 30;
        $types  = [];

        $nodes = $this->xpath->query( '//script[@type="application/ld+json"]' );
        foreach ( $nodes as $node ) {
            $json = trim( $node->textContent );
            if ( empty( $json ) ) continue;
            $data = json_decode( $json, true );
            if ( ! $data ) continue;
            $items = isset( $data['@graph'] ) ? $data['@graph'] : [ $data ];
            foreach ( $items as $item ) {
                $type = $item['@type'] ?? '';
                if ( $type ) $types[] = $type;
            }
        }
        $types = array_values( array_unique( $types ) );

        if ( empty( $types ) ) {
            $issues[] = $this->issue( 'warning', 'Aucune donnée structurée JSON-LD', 'Ajoutez du balisage JSON-LD (Article, Organization, FAQPage, BreadcrumbList…) pour les résultats enrichis et l\'autorité auprès des LLMs.' );
        } else {
            $score = 100;
            $has_org = in_array( 'Organization', $types ) || in_array( 'LocalBusiness', $types );
            $has_bc  = in_array( 'BreadcrumbList', $types );
            $issues[] = $this->issue( 'success', 'Données structurées JSON-LD présentes', sprintf( 'Types détectés : %s', implode( ', ', $types ) ) );
            if ( ! $has_org ) {
                $score  -= 10;
                $issues[] = $this->issue( 'warning', 'Schema Organization manquant', 'Ajoutez un schema Organization ou LocalBusiness pour renforcer l\'entité de votre marque.' );
            }
            if ( ! $has_bc ) {
                $score  -= 5;
                $issues[] = $this->issue( 'warning', 'BreadcrumbList absent', 'Un schema BreadcrumbList aide Google à afficher le fil d\'ariane dans les SERPs.' );
            }
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [ 'types' => $types, 'count' => count( $types ) ],
            'issues' => $issues,
        ];
    }

    /* 17 — CONTENT RICHNESS --------------------------------- */
    private function analyze_content_richness(): array {
        $issues = [];
        $score  = 50;

        $lists  = $this->xpath->query( '//body//ul | //body//ol' )->length;
        $tables = $this->xpath->query( '//body//table' )->length;
        $bolds  = $this->xpath->query( '//body//strong | //body//b' )->length;
        $videos = $this->xpath->query( '//body//video | //body//iframe[contains(@src,"youtube") or contains(@src,"vimeo")]' )->length;

        $toc_nodes = $this->xpath->query(
            '//body//*[contains(@class,"toc") or contains(@class,"table-of-contents") or contains(@id,"toc") or contains(@id,"table-of-contents") or contains(@class,"sommaire")]'
        );
        $has_toc = $toc_nodes->length > 0;

        if ( $lists > 0 ) {
            $score  += 10;
            $issues[] = $this->issue( 'success', 'Listes présentes', sprintf( '%d liste(s) détectée(s). Les listes améliorent la mise en forme et les featured snippets.', $lists ) );
        } else {
            $issues[] = $this->issue( 'warning', 'Aucune liste détectée', 'Ajoutez des listes (ul/ol) pour structurer le contenu et viser les featured snippets.' );
        }

        if ( $tables > 0 ) {
            $score  += 5;
            $issues[] = $this->issue( 'success', 'Tableaux présents', sprintf( '%d tableau(x) détecté(s).', $tables ) );
        }

        if ( $bolds > 0 ) {
            $score  += 10;
            $issues[] = $this->issue( 'success', 'Mises en gras présentes', sprintf( '%d élément(s) <strong>/<b>.', $bolds ) );
        } else {
            $issues[] = $this->issue( 'warning', 'Aucune mise en gras', 'Utilisez <strong> pour mettre en valeur les mots-clés importants.' );
        }

        if ( $videos > 0 ) {
            $score  += 10;
            $issues[] = $this->issue( 'success', 'Contenu vidéo présent', sprintf( '%d vidéo(s) intégrée(s).', $videos ) );
        }

        if ( $has_toc ) {
            $score  += 5;
            $issues[] = $this->issue( 'success', 'Sommaire (ToC) détecté', 'Un sommaire améliore l\'expérience utilisateur et favorise les sitelinks en SERP.' );
        } elseif ( $this->word_count > 1500 ) {
            $issues[] = $this->issue( 'warning', 'Sommaire absent', 'Pour un contenu long (> 1500 mots), un sommaire améliore l\'UX et réduit le taux de rebond.' );
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [
                'lists'   => $lists,
                'tables'  => $tables,
                'bolds'   => $bolds,
                'videos'  => $videos,
                'has_toc' => $has_toc,
            ],
            'issues' => $issues,
        ];
    }

    /* 18 — CONTENT FRESHNESS -------------------------------- */
    private function analyze_content_freshness(): array {
        $issues   = [];
        $score    = 60;
        $pub_date = '';
        $mod_date = '';

        $nodes = $this->xpath->query( '//script[@type="application/ld+json"]' );
        foreach ( $nodes as $node ) {
            $data = json_decode( $node->textContent, true );
            if ( ! $data ) continue;
            $items = isset( $data['@graph'] ) ? $data['@graph'] : [ $data ];
            foreach ( $items as $item ) {
                if ( ! empty( $item['datePublished'] ) && empty( $pub_date ) ) $pub_date = $item['datePublished'];
                if ( ! empty( $item['dateModified'] ) && empty( $mod_date ) )  $mod_date = $item['dateModified'];
            }
        }

        if ( empty( $pub_date ) ) {
            $og_pub = $this->xpath->query( '//head/meta[@property="article:published_time"]/@content' );
            if ( $og_pub->length > 0 ) $pub_date = trim( $og_pub->item( 0 )->nodeValue );
        }
        if ( empty( $mod_date ) ) {
            $og_mod = $this->xpath->query( '//head/meta[@property="article:modified_time"]/@content' );
            if ( $og_mod->length > 0 ) $mod_date = trim( $og_mod->item( 0 )->nodeValue );
        }

        $now = time();

        if ( ! empty( $mod_date ) ) {
            $ts   = strtotime( $mod_date );
            $days = $ts ? (int) round( ( $now - $ts ) / 86400 ) : -1;
            if ( $days >= 0 && $days < 180 ) {
                $score    = 100;
                $issues[] = $this->issue( 'success', 'Contenu récemment mis à jour', sprintf( 'Dernière mise à jour il y a %d jours.', $days ) );
            } elseif ( $days >= 0 && $days < 365 ) {
                $score    = 75;
                $issues[] = $this->issue( 'warning', 'Contenu vieillissant', sprintf( 'Dernière mise à jour il y a %d jours. Pensez à rafraîchir le contenu.', $days ) );
            } else {
                $score    = 40;
                $issues[] = $this->issue( 'warning', 'Contenu potentiellement obsolète', sprintf( 'Pas de mise à jour depuis %d jours. Actualisez le contenu pour améliorer le Content Freshness Score.', $days ) );
            }
        } elseif ( ! empty( $pub_date ) ) {
            $ts      = strtotime( $pub_date );
            $days    = $ts ? (int) round( ( $now - $ts ) / 86400 ) : -1;
            $score   = $days >= 0 && $days < 365 ? 60 : 40;
            $issues[] = $this->issue( 'warning', 'Date de publication trouvée, pas de mise à jour', sprintf( 'Publié le %s. Ajoutez une propriété dateModified pour signaler les mises à jour.', $pub_date ) );
        } else {
            $score    = 40;
            $issues[] = $this->issue( 'warning', 'Aucune date de contenu détectée', 'Ajoutez datePublished et dateModified (JSON-LD ou Open Graph) pour le Content Freshness Score de Google.' );
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [
                'pub_date' => $pub_date,
                'mod_date' => $mod_date,
            ],
            'issues' => $issues,
        ];
    }

    /* 19 — ROBOTS.TXT --------------------------------------- */
    private function analyze_robots_txt(): array {
        $issues = [];
        $score  = 100;

        $parsed     = wp_parse_url( $this->url );
        $base       = ( $parsed['scheme'] ?? 'https' ) . '://' . ( $parsed['host'] ?? '' );
        $robots_url = rtrim( $base, '/' ) . '/robots.txt';
        $path       = $parsed['path'] ?? '/';

        $response = wp_remote_get( $robots_url, [
            'timeout'   => 10,
            'sslverify' => false,
        ] );

        if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
            $score    = 40;
            $issues[] = $this->issue( 'error', 'robots.txt introuvable', sprintf( 'Aucun fichier robots.txt trouvé à %s. Ce fichier est essentiel pour guider les crawlers.', $robots_url ) );
            return [ 'score' => $score, 'data' => [ 'url' => $robots_url, 'found' => false, 'blocks_url' => false ], 'issues' => $issues ];
        }

        $content    = wp_remote_retrieve_body( $response );
        $blocks_url = false;
        $in_ua      = false;

        foreach ( explode( "
", $content ) as $line ) {
            $line = trim( $line );
            if ( stripos( $line, 'user-agent:' ) !== false ) {
                $ua    = strtolower( trim( substr( $line, strpos( $line, ':' ) + 1 ) ) );
                $in_ua = $ua === '*';
            }
            if ( $in_ua && stripos( $line, 'disallow:' ) !== false ) {
                $disallow = trim( substr( $line, strpos( $line, ':' ) + 1 ) );
                if ( $disallow === '/' || ( $disallow !== '' && strpos( $path, $disallow ) === 0 ) ) {
                    $blocks_url = true;
                    break;
                }
            }
        }

        if ( $blocks_url ) {
            $score    = 0;
            $issues[] = $this->issue( 'error', 'URL bloquée par robots.txt', 'Le fichier robots.txt bloque le crawl de cette URL. Google ne peut pas l\'indexer.' );
        } else {
            $issues[] = $this->issue( 'success', 'robots.txt présent et URL crawlable', 'Le fichier robots.txt autorise le crawl de cette page.' );
            if ( stripos( $content, 'sitemap:' ) !== false ) {
                $issues[] = $this->issue( 'success', 'Sitemap référencé dans robots.txt', 'Le fichier robots.txt contient une référence au sitemap.' );
            }
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [
                'url'        => $robots_url,
                'found'      => true,
                'blocks_url' => $blocks_url,
            ],
            'issues' => $issues,
        ];
    }

    /* 20 — SITEMAP XML --------------------------------------- */
    private function analyze_sitemap(): array {
        $issues = [];
        $score  = 30;

        $parsed = wp_parse_url( $this->url );
        $base   = ( $parsed['scheme'] ?? 'https' ) . '://' . ( $parsed['host'] ?? '' );
        $found  = false;
        $used   = rtrim( $base, '/' ) . '/sitemap.xml';

        foreach ( [ '/sitemap.xml', '/sitemap_index.xml', '/sitemap-index.xml' ] as $p ) {
            $url  = rtrim( $base, '/' ) . $p;
            $resp = wp_remote_get( $url, [ 'timeout' => 10, 'sslverify' => false ] );
            if ( ! is_wp_error( $resp ) && wp_remote_retrieve_response_code( $resp ) === 200 ) {
                $found = true;
                $used  = $url;
                break;
            }
        }

        if ( ! $found ) {
            $issues[] = $this->issue( 'warning', 'Sitemap XML introuvable', 'Aucun fichier sitemap.xml trouvé sur ce domaine. Un sitemap garantit l\'indexation de toutes les pages.' );
        } else {
            $score    = 100;
            $issues[] = $this->issue( 'success', 'Sitemap XML présent', sprintf( 'Sitemap trouvé : %s', $used ) );
        }

        return [
            'score'  => max( 0, min( 100, $score ) ),
            'data'   => [ 'found' => $found, 'url' => $used ],
            'issues' => $issues,
        ];
    }

    /* ══════════════════════════════════════════════
     *  TAB BUILDERS
     * ══════════════════════════════════════════════ */

    private function build_technical_tab( $title, $meta, $h1, $canonical, $robots, $social, $url_struct = null, $page_speed = null, $viewport = null, $schema = null, $robots_txt = null, $sitemap = null ): array {
        $tab = [];

        $tab[] = [
            'id'     => 'http_code',
            'label'  => 'Code HTTP',
            'value'  => (string) $this->http_code,
            'status' => $this->http_code === 200 ? 'success' : 'warning',
        ];

        $tab[] = [
            'id'          => 'title_tag',
            'label'       => 'Balise Title',
            'value'       => $title['data']['value'] ?: '(absente)',
            'pixel_width' => $title['data']['pixel_width'] ?? 0,
            'optimal'     => '480–580 px',
            'status'      => $this->score_to_status( $title['score'] ),
        ];

        $tab[] = [
            'id'          => 'meta_description',
            'label'       => 'Meta Description',
            'value'       => $this->truncate( $meta['data']['value'] ?: '(absente)', 120 ),
            'pixel_width' => $meta['data']['pixel_width'] ?? 0,
            'optimal'     => '400–920 px',
            'status'      => $this->score_to_status( $meta['score'] ),
        ];

        $tab[] = [
            'id'     => 'h1_tag',
            'label'  => 'Balise H1',
            'value'  => $h1['data']['value'] ?: '(absente)',
            'count'  => $h1['data']['count'],
            'status' => $this->score_to_status( $h1['score'] ),
        ];

        $tab[] = [
            'id'     => 'canonical',
            'label'  => 'Canonical',
            'value'  => $canonical['data']['value'] ?: '(absente)',
            'status' => $this->score_to_status( $canonical['score'] ),
            'detail' => $canonical['data']['is_self'] ? 'Auto-référente' : 'Externe ou absente',
        ];

        $tab[] = [
            'id'     => 'robots_meta',
            'label'  => 'Robots Meta',
            'value'  => $robots['data']['value'],
            'status' => $this->score_to_status( $robots['score'] ),
        ];

        /* OG */
        $og_p = $social['data']['og_present'];
        $og_m = $social['data']['og_missing'];
        $tab[] = [
            'id'      => 'og_tags',
            'label'   => 'Open Graph',
            'value'   => count( $og_p ) . '/5 balises',
            'present' => $og_p,
            'missing' => $og_m,
            'status'  => count( $og_p ) >= 3 ? 'success' : ( count( $og_p ) > 0 ? 'warning' : 'error' ),
        ];

        /* Twitter */
        $tw_p = $social['data']['tw_present'];
        $tw_m = $social['data']['tw_missing'];
        $tab[] = [
            'id'      => 'tw_tags',
            'label'   => 'Twitter Card',
            'value'   => count( $tw_p ) . '/4 balises',
            'present' => $tw_p,
            'missing' => $tw_m,
            'status'  => count( $tw_p ) >= 2 ? 'success' : ( count( $tw_p ) > 0 ? 'warning' : 'error' ),
        ];

        /* HTTPS & URL */
        if ( $url_struct ) {
            $tab[] = [
                'id'     => 'https',
                'label'  => 'HTTPS',
                'value'  => $url_struct['data']['is_https'] ? 'Oui' : 'Non',
                'status' => $url_struct['data']['is_https'] ? 'success' : 'error',
            ];
            $tab[] = [
                'id'     => 'url_depth',
                'label'  => 'Profondeur URL',
                'value'  => $url_struct['data']['depth'] . ' niveaux',
                'status' => $url_struct['data']['depth'] <= 3 ? 'success' : 'warning',
            ];
            $tab[] = [
                'id'     => 'url_length',
                'label'  => 'Longueur URL',
                'value'  => $url_struct['data']['url_length'] . ' car.',
                'status' => $url_struct['data']['url_length'] <= 115 ? 'success' : 'warning',
            ];
        }

        /* TTFB */
        if ( $page_speed ) {
            $tab[] = [
                'id'     => 'ttfb',
                'label'  => 'TTFB serveur',
                'value'  => $page_speed['data']['ttfb_ms'] . ' ms',
                'detail' => $page_speed['data']['html_size_kb'] . ' Ko HTML',
                'status' => $this->score_to_status( $page_speed['score'] ),
            ];
        }

        /* Viewport & lang */
        if ( $viewport ) {
            $tab[] = [
                'id'     => 'viewport',
                'label'  => 'Balise viewport',
                'value'  => $viewport['data']['viewport'] ?: '(absente)',
                'status' => $this->score_to_status( $viewport['score'] ),
            ];
            $tab[] = [
                'id'     => 'html_lang',
                'label'  => 'HTML lang',
                'value'  => $viewport['data']['lang'] ?: '(absent)',
                'status' => ! empty( $viewport['data']['lang'] ) ? 'success' : 'warning',
            ];
        }

        /* Structured data */
        if ( $schema ) {
            $tab[] = [
                'id'     => 'schema',
                'label'  => 'Données structurées',
                'value'  => $schema['data']['count'] > 0 ? implode( ', ', $schema['data']['types'] ) : '(aucune)',
                'status' => $this->score_to_status( $schema['score'] ),
            ];
        }

        /* robots.txt */
        if ( $robots_txt ) {
            $tab[] = [
                'id'     => 'robots_txt',
                'label'  => 'robots.txt',
                'value'  => $robots_txt['data']['found'] ? ( $robots_txt['data']['blocks_url'] ? 'Bloqué' : 'OK' ) : 'Introuvable',
                'status' => $this->score_to_status( $robots_txt['score'] ),
            ];
        }

        /* Sitemap */
        if ( $sitemap ) {
            $tab[] = [
                'id'     => 'sitemap',
                'label'  => 'Sitemap XML',
                'value'  => $sitemap['data']['found'] ? 'Présent' : 'Introuvable',
                'detail' => $sitemap['data']['found'] ? $sitemap['data']['url'] : '',
                'status' => $this->score_to_status( $sitemap['score'] ),
            ];
        }

        return $tab;
    }

    private function build_semantic_tab( $density, $content, $title, $h1, $headings, $richness = null, $freshness = null ): array {
        $tab = [];

        $tab[] = [
            'id'          => 'keyword',
            'label'       => 'Mot-clé cible',
            'value'       => $this->keyword,
            'status'      => 'info',
        ];

        $tab[] = [
            'id'          => 'density_body',
            'label'       => 'Densité dans le body',
            'density'     => $density['data']['density'] ?? 0,
            'occurrences' => $density['data']['occurrences'] ?? 0,
            'word_count'  => $density['data']['word_count'] ?? 0,
            'optimal'     => '0,5–2,0 %',
            'status'      => $this->score_to_status( $density['score'] ),
        ];

        $tab[] = [
            'id'     => 'kw_in_title',
            'label'  => 'Mot-clé dans le Title',
            'value'  => ( $title['data']['has_keyword'] ?? false ) ? 'Oui' : 'Non',
            'status' => ( $title['data']['has_keyword'] ?? false ) ? 'success' : 'warning',
        ];

        $tab[] = [
            'id'     => 'kw_in_h1',
            'label'  => 'Mot-clé dans le H1',
            'value'  => ( $h1['data']['has_keyword'] ?? false ) ? 'Oui' : 'Non',
            'status' => ( $h1['data']['has_keyword'] ?? false ) ? 'success' : 'warning',
        ];

        $tab[] = [
            'id'     => 'kw_in_h2',
            'label'  => 'Mot-clé dans les H2',
            'value'  => sprintf( '%d/%d H2', $headings['data']['h2_with_kw'] ?? 0, $headings['data']['h2_count'] ?? 0 ),
            'status' => ( $headings['data']['h2_with_kw'] ?? 0 ) > 0 ? 'success' : 'warning',
        ];

        $tab[] = [
            'id'     => 'first_100',
            'label'  => 'Mot-clé dans les 100 premiers mots',
            'value'  => ( $density['data']['in_first_100'] ?? false ) ? 'Oui' : 'Non',
            'status' => ( $density['data']['in_first_100'] ?? false ) ? 'success' : 'warning',
        ];

        $tab[] = [
            'id'         => 'content_length',
            'label'      => 'Longueur du contenu',
            'word_count' => $content['data']['word_count'] ?? 0,
            'status'     => $this->score_to_status( $content['score'] ),
        ];

        /* Content richness */
        if ( $richness ) {
            $tab[] = [
                'id'     => 'richness_lists',
                'label'  => 'Listes ul/ol',
                'value'  => $richness['data']['lists'] . ' liste(s)',
                'status' => $richness['data']['lists'] > 0 ? 'success' : 'warning',
            ];
            $tab[] = [
                'id'     => 'richness_bold',
                'label'  => 'Mises en gras',
                'value'  => $richness['data']['bolds'] . ' <strong>',
                'status' => $richness['data']['bolds'] > 0 ? 'success' : 'warning',
            ];
            $tab[] = [
                'id'     => 'richness_toc',
                'label'  => 'Sommaire (ToC)',
                'value'  => $richness['data']['has_toc'] ? 'Présent' : 'Absent',
                'status' => $richness['data']['has_toc'] ? 'success' : 'warning',
            ];
        }

        /* Content freshness */
        if ( $freshness ) {
            $date_val = ! empty( $freshness['data']['mod_date'] )
                ? $freshness['data']['mod_date']
                : ( ! empty( $freshness['data']['pub_date'] ) ? $freshness['data']['pub_date'] : 'Non renseignée' );
            $tab[] = [
                'id'     => 'freshness',
                'label'  => 'Fraîcheur du contenu',
                'value'  => $date_val,
                'status' => $this->score_to_status( $freshness['score'] ),
            ];
        }

        return $tab;
    }

    private function build_links_tab( $int_links, $ext_links, $images ): array {
        return [
            [
                'id'       => 'internal_links',
                'label'    => 'Liens internes',
                'total'    => $int_links['data']['total'],
                'dofollow' => $int_links['data']['dofollow'],
                'nofollow' => $int_links['data']['nofollow'],
                'details'  => $int_links['data']['details'],
                'status'   => $this->score_to_status( $int_links['score'] ),
            ],
            [
                'id'       => 'external_links',
                'label'    => 'Liens externes',
                'total'    => $ext_links['data']['total'],
                'dofollow' => $ext_links['data']['dofollow'],
                'nofollow' => $ext_links['data']['nofollow'],
                'details'  => $ext_links['data']['details'],
                'status'   => $this->score_to_status( $ext_links['score'] ),
            ],
            [
                'id'          => 'images',
                'label'       => 'Images',
                'total'       => $images['data']['total'],
                'with_alt'    => $images['data']['with_alt'],
                'without_alt' => $images['data']['without_alt'],
                'status'      => $this->score_to_status( $images['score'] ),
            ],
        ];
    }

    /* ══════════════════════════════════════════════
     *  HELPERS
     * ══════════════════════════════════════════════ */

    /** Collect & sort issues from all factors */
    private function collect_issues( array $factors ): array {
        $all = [];
        foreach ( $factors as $f ) {
            if ( ! empty( $f['issues'] ) ) {
                foreach ( $f['issues'] as $issue ) {
                    $all[] = $issue;
                }
            }
        }
        /* Sort: errors first, then warnings, then successes */
        usort( $all, function ( $a, $b ) {
            $order = [ 'error' => 0, 'warning' => 1, 'success' => 2 ];
            return ( $order[ $a['type'] ] ?? 3 ) <=> ( $order[ $b['type'] ] ?? 3 );
        } );
        return $all;
    }

    /** Average score from multiple factor results */
    private function avg_scores( array $factors ): float {
        $scores = array_filter( array_map( fn( $f ) => $f['score'] ?? null, $factors ), fn( $s ) => $s !== null );
        return count( $scores ) > 0 ? round( array_sum( $scores ) / count( $scores ) ) : 0;
    }

    /** Create a standardised issue array */
    private function issue( string $type, string $title, string $desc ): array {
        return [ 'type' => $type, 'title' => $title, 'description' => $desc ];
    }

    /** Score → status string */
    private function score_to_status( int $score ): string {
        if ( $score >= 80 ) return 'success';
        if ( $score >= 50 ) return 'warning';
        return 'error';
    }

    /** Check if text contains keyword (case-insensitive, multibyte-safe) */
    private function contains_keyword( string $text ): bool {
        if ( empty( $this->keyword ) ) return false;
        return mb_stripos( $text, $this->keyword ) !== false;
    }

    /** Check if a link is internal */
    private function is_internal_link( string $href ): bool {
        if ( strpos( $href, '/' ) === 0 && strpos( $href, '//' ) !== 0 ) {
            return true;
        }
        $parsed = wp_parse_url( $href );
        $host   = $parsed['host'] ?? '';
        return $host === $this->target_host || $host === 'www.' . $this->target_host || 'www.' . $host === $this->target_host;
    }

    /** Normalize URL for comparison */
    private function normalize_url( string $url ): string {
        $url = strtolower( trim( $url ) );
        $url = rtrim( $url, '/' );
        $url = preg_replace( '#^https?://(www\.)?#', '', $url );
        return $url;
    }

    /** Truncate string */
    private function truncate( string $str, int $max ): string {
        if ( mb_strlen( $str ) <= $max ) return $str;
        return mb_substr( $str, 0, $max ) . '…';
    }

    /* ══════════════════════════════════════════════
     *  STRUCTURE HN — DETAILED ANALYSIS (5 criteria)
     * ══════════════════════════════════════════════ */

    /**
     * Run the 5-criteria Hn structure analysis and return a structured report.
     */
    private function analyze_hn_detailed(): array {

        /* Query all headings in document order */
        $heading_nodes = $this->xpath->query( '//h1|//h2|//h3|//h4|//h5|//h6' );

        $tree = [];
        foreach ( $heading_nodes as $node ) {
            $level  = (int) substr( $node->nodeName, 1 );
            $text   = trim( $node->textContent );
            $tree[] = [
                'tag'    => strtoupper( $node->nodeName ),
                'level'  => $level,
                'text'   => $text,
                'length' => mb_strlen( $text ),
                'words'  => str_word_count( strip_tags( $text ) ),
            ];
        }

        /* Get <title> and meta description for coherence check */
        $title_nodes = $this->xpath->query( '//title' );
        $title_text  = $title_nodes->length ? trim( $title_nodes->item( 0 )->textContent ) : '';

        $meta_nodes = $this->xpath->query( '//meta[@name="description"]/@content' );
        $meta_text  = $meta_nodes->length ? trim( $meta_nodes->item( 0 )->nodeValue ) : '';

        /* H1 values */
        $h1_nodes = $this->xpath->query( '//h1' );
        $h1_count = $h1_nodes->length;
        $h1_text  = $h1_count > 0 ? trim( $h1_nodes->item( 0 )->textContent ) : '';

        /* Run the 5 criteria */
        $c1 = $this->hn_criterion_h1( $h1_count, $h1_text, $title_text );
        $c2 = $this->hn_criterion_tree( $tree );
        $c3 = $this->hn_criterion_semantic( $tree );
        $c4 = $this->hn_criterion_length( $tree );
        $c5 = $this->hn_criterion_coherence( $h1_text, $title_text, $meta_text );

        /* Global Hn score */
        $hn_score = (int) round( ( $c1['score'] + $c2['score'] + $c3['score'] + $c4['score'] + $c5['score'] ) / 5 );

        return [
            'score'    => $hn_score,
            'tree'     => $tree,
            'h1_text'  => $h1_text,
            'counts'   => $this->hn_level_counts( $tree ),
            'criteria' => [ $c1, $c2, $c3, $c4, $c5 ],
        ];
    }

    /** Count headings per level */
    private function hn_level_counts( array $tree ): array {
        $counts = [ 'H1' => 0, 'H2' => 0, 'H3' => 0, 'H4' => 0, 'H5' => 0, 'H6' => 0 ];
        foreach ( $tree as $h ) {
            if ( isset( $counts[ $h['tag'] ] ) ) {
                $counts[ $h['tag'] ]++;
            }
        }
        return $counts;
    }

    /* ── Criterion 1 — H1 présence et unicité ── */
    private function hn_criterion_h1( int $count, string $h1_text, string $title_text ): array {
        $score  = 100;
        $issues = [];

        if ( $count === 0 ) {
            return [
                'id'     => 'h1_uniqueness',
                'label'  => 'Présence et unicité du H1',
                'score'  => 0,
                'status' => 'error',
                'issues' => [ [ 'type' => 'error', 'msg' => 'Aucun H1 trouvé sur la page.' ] ],
                'value'  => '',
            ];
        }

        if ( $count > 1 ) {
            $score -= 30;
            $issues[] = [ 'type' => 'warning', 'msg' => sprintf( '%d balises H1 détectées — une seule est recommandée.', $count ) ];
        } else {
            $issues[] = [ 'type' => 'success', 'msg' => 'H1 unique : présent une seule fois.' ];
        }

        if ( $this->keyword ) {
            if ( $this->contains_keyword( $h1_text ) ) {
                $issues[] = [ 'type' => 'success', 'msg' => sprintf( 'Mot-clé « %s » présent dans le H1.', $this->keyword ) ];
                $score     = min( 100, $score + 10 );
            } else {
                $score -= 20;
                $issues[] = [ 'type' => 'warning', 'msg' => sprintf( 'Mot-clé « %s » absent du H1.', $this->keyword ) ];
            }
        }

        if ( $title_text ) {
            $sim = $this->text_similarity( mb_strtolower( $h1_text ), mb_strtolower( $title_text ) );
            if ( $sim >= 95 ) {
                $score -= 10;
                $issues[] = [ 'type' => 'warning', 'msg' => sprintf( 'H1 quasi-identique au title (%d%% de similarité) — différenciez les formulations.', $sim ) ];
            } elseif ( $sim >= 40 ) {
                $score = min( 100, $score + 5 );
                $issues[] = [ 'type' => 'success', 'msg' => sprintf( 'H1 cohérent avec le title (%d%% de similarité) sans être identique.', $sim ) ];
            } else {
                $issues[] = [ 'type' => 'info', 'msg' => sprintf( 'Faible cohérence H1/title (%d%%) — vérifiez qu\'ils portent sur le même sujet.', $sim ) ];
            }
        }

        $status = $score >= 80 ? 'success' : ( $score >= 50 ? 'warning' : 'error' );
        return [
            'id'     => 'h1_uniqueness',
            'label'  => 'Présence et unicité du H1',
            'score'  => max( 0, $score ),
            'status' => $status,
            'issues' => $issues,
            'value'  => $h1_text,
        ];
    }

    /* ── Criterion 2 — Arborescence H1-H6 ── */
    private function hn_criterion_tree( array $tree ): array {
        $score       = 100;
        $issues      = [];
        $total       = count( $tree );
        $prev_level  = 0;
        $jumps       = [];
        $used_levels = [];

        if ( $total === 0 ) {
            return [
                'id'      => 'hn_tree',
                'label'   => 'Arborescence H1–H6',
                'score'   => 0,
                'status'  => 'error',
                'issues'  => [ [ 'type' => 'error', 'msg' => 'Aucun titre détecté sur la page.' ] ],
                'depth'   => 0,
                'jumps'   => [],
                'missing' => [],
            ];
        }

        foreach ( $tree as $h ) {
            $used_levels[ $h['level'] ] = true;
            if ( $prev_level > 0 && $h['level'] > $prev_level + 1 ) {
                $jumps[] = sprintf( 'H%d → H%d', $prev_level, $h['level'] );
            }
            $prev_level = $h['level'];
        }

        $max_depth       = max( array_keys( $used_levels ) );
        $missing_between = [];
        for ( $l = 1; $l <= $max_depth; $l++ ) {
            if ( ! isset( $used_levels[ $l ] ) ) {
                $missing_between[] = "H{$l}";
            }
        }

        if ( ! empty( $jumps ) ) {
            $score   -= count( $jumps ) * 15;
            $issues[] = [ 'type' => 'warning', 'msg' => 'Saut(s) de niveau détecté(s) : ' . implode( ', ', array_unique( $jumps ) ) . '.' ];
        } else {
            $issues[] = [ 'type' => 'success', 'msg' => 'Aucun saut de niveau — hiérarchie respectée.' ];
        }

        if ( ! empty( $missing_between ) ) {
            $issues[] = [ 'type' => 'info', 'msg' => 'Niveaux absents dans la plage utilisée : ' . implode( ', ', $missing_between ) . '.' ];
        }

        if ( $total < 3 ) {
            $score   -= 20;
            $issues[] = [ 'type' => 'warning', 'msg' => sprintf( 'Seulement %d titre(s) — enrichissez la structure avec des H2/H3.', $total ) ];
        } else {
            $issues[] = [ 'type' => 'success', 'msg' => sprintf( '%d titres détectés (profondeur maximale : H%d).', $total, $max_depth ) ];
        }

        $status = $score >= 80 ? 'success' : ( $score >= 50 ? 'warning' : 'error' );
        return [
            'id'      => 'hn_tree',
            'label'   => 'Arborescence H1–H6',
            'score'   => max( 0, min( 100, $score ) ),
            'status'  => $status,
            'issues'  => $issues,
            'depth'   => $max_depth,
            'jumps'   => array_values( array_unique( $jumps ) ),
            'missing' => $missing_between,
        ];
    }

    /* ── Criterion 3 — Analyse sémantique des titres ── */
    private function hn_criterion_semantic( array $tree ): array {
        $score  = 70;
        $issues = [];

        if ( empty( $this->keyword ) ) {
            return [
                'id'             => 'hn_semantic',
                'label'          => 'Analyse sémantique des titres',
                'score'          => 0,
                'status'         => 'info',
                'issues'         => [ [ 'type' => 'info', 'msg' => 'Ajoutez un mot-clé cible pour activer l\'analyse sémantique des titres.' ] ],
                'kw_in_h2'       => 0,
                'kw_in_h3'       => 0,
                'h2_total'       => 0,
                'covered_levels' => [],
            ];
        }

        $kw_words = array_filter(
            array_map( 'mb_strtolower', (array) preg_split( '/\s+/', $this->keyword ) ),
            fn( $w ) => mb_strlen( $w ) > 2
        );

        $h2_total      = 0;
        $h2_with_kw    = 0;
        $h3_total      = 0;
        $h3_with_kw    = 0;
        $h2_without    = [];
        $covered_lvls  = [];

        foreach ( $tree as $h ) {
            $text_lower     = mb_strtolower( $h['text'] );
            $has_kw         = $this->contains_keyword( $text_lower );
            $has_any_kw_wrd = false;
            foreach ( $kw_words as $w ) {
                if ( mb_strpos( $text_lower, $w ) !== false ) {
                    $has_any_kw_wrd = true;
                    break;
                }
            }

            if ( $h['level'] === 2 ) {
                $h2_total++;
                if ( $has_kw ) {
                    $h2_with_kw++;
                }
                if ( ! $has_any_kw_wrd && $h['text'] !== '' ) {
                    $h2_without[] = $h['text'];
                }
            }

            if ( $h['level'] === 3 ) {
                $h3_total++;
                if ( $has_kw ) {
                    $h3_with_kw++;
                }
            }

            if ( $has_kw ) {
                $covered_lvls[ $h['level'] ] = true;
            }
        }

        if ( $h2_total > 0 ) {
            $pct = round( ( $h2_with_kw / $h2_total ) * 100 );
            if ( $pct >= 30 ) {
                $score    = min( 100, $score + 20 );
                $issues[] = [ 'type' => 'success', 'msg' => sprintf( 'Mot-clé présent dans %d%% des H2 (%d/%d).', $pct, $h2_with_kw, $h2_total ) ];
            } elseif ( $h2_with_kw === 0 ) {
                $score   -= 20;
                $issues[] = [ 'type' => 'warning', 'msg' => 'Mot-clé absent de tous les H2 — incluez-le dans au moins un H2.' ];
            } else {
                $issues[] = [ 'type' => 'info', 'msg' => sprintf( 'Mot-clé présent dans %d/%d H2.', $h2_with_kw, $h2_total ) ];
            }
        }

        if ( count( $h2_without ) > 0 ) {
            $score   -= 10;
            $sample   = array_slice( $h2_without, 0, 3 );
            $issues[] = [ 'type' => 'info', 'msg' => sprintf( '%d H2 sans aucun terme du mot-clé : « %s »…', count( $h2_without ), mb_substr( implode( '» · «', $sample ), 0, 80 ) ) ];
        }

        $lvl_count = count( $covered_lvls );
        if ( $lvl_count >= 3 ) {
            $issues[] = [ 'type' => 'success', 'msg' => sprintf( 'Champ lexical distribué sur %d niveaux de titres.', $lvl_count ) ];
        } elseif ( $lvl_count === 0 && count( $tree ) > 0 ) {
            $score   -= 15;
            $issues[] = [ 'type' => 'error', 'msg' => 'Le mot-clé n\'apparaît dans aucun titre de la page.' ];
        }

        $status = $score >= 80 ? 'success' : ( $score >= 50 ? 'warning' : 'error' );
        return [
            'id'             => 'hn_semantic',
            'label'          => 'Analyse sémantique des titres',
            'score'          => max( 0, min( 100, $score ) ),
            'status'         => $status,
            'issues'         => $issues,
            'kw_in_h2'       => $h2_with_kw,
            'kw_in_h3'       => $h3_with_kw,
            'h2_total'       => $h2_total,
            'covered_levels' => array_keys( $covered_lvls ),
        ];
    }

    /* ── Criterion 4 — Longueur et lisibilité ── */
    private function hn_criterion_length( array $tree ): array {
        $score      = 100;
        $issues     = [];
        $too_long   = [];
        $too_short  = [];
        $empty_ones = [];

        foreach ( $tree as $h ) {
            if ( $h['text'] === '' ) {
                $empty_ones[] = $h['tag'];
                $score       -= 15;
            } elseif ( $h['length'] > 70 ) {
                $too_long[] = [ 'tag' => $h['tag'], 'text' => $h['text'], 'len' => $h['length'] ];
            } elseif ( $h['words'] < 3 ) {
                $too_short[] = [ 'tag' => $h['tag'], 'text' => $h['text'], 'words' => $h['words'] ];
            }
        }

        if ( ! empty( $empty_ones ) ) {
            $issues[] = [ 'type' => 'error', 'msg' => sprintf( '%d titre(s) vide(s) : %s.', count( $empty_ones ), implode( ', ', $empty_ones ) ) ];
        }

        if ( ! empty( $too_long ) ) {
            $score   -= min( 30, count( $too_long ) * 10 );
            $sample   = array_slice( $too_long, 0, 3 );
            $msgs     = array_map( fn( $h ) => sprintf( '%s (%d car.)', $h['tag'], $h['len'] ), $sample );
            $issues[] = [ 'type' => 'warning', 'msg' => sprintf( '%d titre(s) trop long(s) (>70 car.) : %s.', count( $too_long ), implode( ', ', $msgs ) ) ];
        }

        if ( ! empty( $too_short ) ) {
            $score   -= min( 20, count( $too_short ) * 5 );
            $issues[] = [ 'type' => 'info', 'msg' => sprintf( '%d titre(s) trop court(s) (<3 mots) — enrichissez leur contenu.', count( $too_short ) ) ];
        }

        if ( empty( $too_long ) && empty( $too_short ) && empty( $empty_ones ) ) {
            $issues[] = [ 'type' => 'success', 'msg' => 'Tous les titres ont une longueur et une lisibilité correctes.' ];
        }

        /* Per-heading detail for UI rendering */
        $detail = array_map( fn( $h ) => [
            'tag'    => $h['tag'],
            'text'   => $h['text'],
            'length' => $h['length'],
            'words'  => $h['words'],
            'status' => ( $h['text'] === '' ) ? 'error'
                      : ( ( $h['length'] > 70 ) ? 'warning'
                      : ( ( $h['words'] < 3 )   ? 'info' : 'success' ) ),
        ], $tree );

        $status = $score >= 80 ? 'success' : ( $score >= 50 ? 'warning' : 'error' );
        return [
            'id'        => 'hn_length',
            'label'     => 'Longueur et lisibilité des titres',
            'score'     => max( 0, min( 100, $score ) ),
            'status'    => $status,
            'issues'    => $issues,
            'too_long'  => $too_long,
            'too_short' => $too_short,
            'empty'     => $empty_ones,
            'detail'    => $detail,
        ];
    }

    /* ── Criterion 5 — Cohérence avec title et meta description ── */
    private function hn_criterion_coherence( string $h1_text, string $title_text, string $meta_text ): array {
        $score  = 80;
        $issues = [];

        if ( empty( $h1_text ) ) {
            return [
                'id'        => 'hn_coherence',
                'label'     => 'Cohérence avec title et meta description',
                'score'     => 0,
                'status'    => 'error',
                'issues'    => [ [ 'type' => 'error', 'msg' => 'Impossible d\'évaluer la cohérence : H1 absent.' ] ],
                'sim_title' => 0,
                'sim_meta'  => 0,
            ];
        }

        $h1_l    = mb_strtolower( $h1_text );
        $title_l = mb_strtolower( $title_text );
        $meta_l  = mb_strtolower( $meta_text );

        $sim_title = $title_text ? $this->text_similarity( $h1_l, $title_l ) : 0;
        $sim_meta  = $meta_text  ? $this->text_similarity( $h1_l, $meta_l )  : 0;

        /* H1 vs Title */
        if ( $title_text ) {
            if ( $sim_title >= 90 ) {
                $score   -= 15;
                $issues[] = [ 'type' => 'warning', 'msg' => sprintf( 'H1 quasi-identique au title (%d%%) — différenciez les formulations.', $sim_title ) ];
            } elseif ( $sim_title >= 35 ) {
                $score    = min( 100, $score + 15 );
                $issues[] = [ 'type' => 'success', 'msg' => sprintf( 'H1 et title cohérents (%d%%) sans être identiques.', $sim_title ) ];
            } else {
                $issues[] = [ 'type' => 'info', 'msg' => sprintf( 'Faible cohérence H1/title (%d%%) — vérifiez qu\'ils portent sur le même sujet.', $sim_title ) ];
            }
        } else {
            $issues[] = [ 'type' => 'info', 'msg' => 'Balise <title> absente, comparaison impossible.' ];
        }

        /* H1 vs Meta description */
        if ( $meta_text ) {
            if ( $sim_meta >= 60 ) {
                $issues[] = [ 'type' => 'success', 'msg' => sprintf( 'H1 et meta description cohérents (%d%%).', $sim_meta ) ];
            } else {
                $issues[] = [ 'type' => 'info', 'msg' => sprintf( 'Cohérence H1/meta description : %d%%.', $sim_meta ) ];
            }
        } else {
            $issues[] = [ 'type' => 'info', 'msg' => 'Meta description absente.' ];
        }

        $meta_preview = mb_strlen( $meta_text ) > 120 ? mb_substr( $meta_text, 0, 120 ) . '…' : $meta_text;

        $status = $score >= 80 ? 'success' : ( $score >= 50 ? 'warning' : 'error' );
        return [
            'id'        => 'hn_coherence',
            'label'     => 'Cohérence avec title et meta description',
            'score'     => max( 0, min( 100, $score ) ),
            'status'    => $status,
            'issues'    => $issues,
            'sim_title' => $sim_title,
            'sim_meta'  => $sim_meta,
            'h1'        => $h1_text,
            'title'     => $title_text,
            'meta'      => $meta_preview,
        ];
    }

    /**
     * Compute word-overlap Jaccard similarity (%) between two strings.
     */
    private function text_similarity( string $a, string $b ): int {
        $words_a = array_filter(
            (array) preg_split( '/\W+/u', $a ),
            fn( $w ) => mb_strlen( $w ) > 2
        );
        $words_b = array_filter(
            (array) preg_split( '/\W+/u', $b ),
            fn( $w ) => mb_strlen( $w ) > 2
        );

        if ( empty( $words_a ) || empty( $words_b ) ) {
            return 0;
        }

        $set_a = array_unique( $words_a );
        $set_b = array_unique( $words_b );

        $intersection = count( array_intersect( $set_a, $set_b ) );
        $union        = count( array_unique( array_merge( $set_a, $set_b ) ) );

        return $union > 0 ? (int) round( ( $intersection / $union ) * 100 ) : 0;
    }

    /* ══════════════════════════════════════════════
     *  END STRUCTURE HN
     * ══════════════════════════════════════════════ */

    /**
     * Approximate pixel width of a string as rendered in Google SERP.
     * Uses a simplified character-width map (Arial 14px approximation).
     */
    private function pixel_width( string $text ): int {
        $width = 0;
        $wide   = [ 'W', 'M', '%', '@' ];
        $medium = [ 'A','B','C','D','E','F','G','H','K','N','O','P','Q','R','S','U','V','X','Y','Z',
                     'w','m','G','D' ];
        $narrow = [ 'i','l','I','!','.',',',';',':','|','\'','"','`','1','(',')','{','}','[',']' ];

        foreach ( mb_str_split( $text ) as $c ) {
            if ( in_array( $c, $wide, true ) ) {
                $width += 11;
            } elseif ( in_array( $c, $narrow, true ) ) {
                $width += 4;
            } elseif ( $c === ' ' ) {
                $width += 4;
            } elseif ( in_array( $c, $medium, true ) ) {
                $width += 9;
            } else {
                $width += 7;
            }
        }

        return $width;
    }
}
