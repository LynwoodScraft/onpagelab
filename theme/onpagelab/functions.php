<?php
/**
 * OnPageLab Theme Functions
 *
 * @package OnPageLab
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Force l'activation des Application Passwords même si le site
// utilise déjà Basic Auth (ex: .htpasswd sur environnement staging)
add_filter( 'wp_is_application_passwords_available', '__return_true' );

// ============================================================
// CONSTANTS
// ============================================================
define( 'OPL_VERSION',   '1.1.0' );
define( 'OPL_THEME_DIR', get_template_directory() );
define( 'OPL_THEME_URI', get_template_directory_uri() );

// ============================================================
// THEME SETUP
// ============================================================
function opl_setup() {
    // Translation support
    load_theme_textdomain( 'onpagelab', OPL_THEME_DIR . '/languages' );

    // Add default posts and comments RSS feed links to head
    add_theme_support( 'automatic-feed-links' );

    // Let WordPress manage the document title
    add_theme_support( 'title-tag' );

    // Enable support for Post Thumbnails
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'opl-thumb-sm',  400, 250, true );
    add_image_size( 'opl-thumb-md',  800, 450, true );
    add_image_size( 'opl-thumb-lg', 1200, 675, true );
    add_image_size( 'opl-square',    500, 500, true );

    // Register nav menus
    register_nav_menus( [
        'primary'   => __( 'Menu principal', 'onpagelab' ),
        'footer-1'  => __( 'Footer, Produit', 'onpagelab' ),
        'footer-2'  => __( 'Footer, Ressources', 'onpagelab' ),
        'footer-3'  => __( 'Footer, Légal', 'onpagelab' ),
    ] );

    // HTML5 support
    add_theme_support( 'html5', [
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script',
    ] );

    // Custom logo
    add_theme_support( 'custom-logo', [
        'height'      => 40,
        'width'       => 160,
        'flex-height' => true,
        'flex-width'  => true,
    ] );

    // Gutenberg wide/full alignment
    add_theme_support( 'align-wide' );

    // Responsive embeds
    add_theme_support( 'responsive-embeds' );

    // Editor styles
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor.css' );

    // Block editor color palette
    add_theme_support( 'editor-color-palette', [
        [ 'name' => 'Primary Blue',  'slug' => 'primary',  'color' => '#2563EB' ],
        [ 'name' => 'Accent Purple', 'slug' => 'accent',   'color' => '#7C3AED' ],
        [ 'name' => 'Dark',          'slug' => 'dark',     'color' => '#0F172A' ],
        [ 'name' => 'Success',       'slug' => 'success',  'color' => '#10B981' ],
    ] );

    // Excerpt length
    add_filter( 'excerpt_length', function() { return 25; } );
    add_filter( 'excerpt_more',   function() { return '&hellip;'; } );
}
add_action( 'after_setup_theme', 'opl_setup' );

// ============================================================
// ENQUEUE SCRIPTS & STYLES
// ============================================================
function opl_enqueue_assets() {
    // Google Fonts — Inter
    wp_enqueue_style(
        'opl-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap',
        [],
        null
    );

    // Main stylesheet
    wp_enqueue_style(
        'opl-style',
        get_stylesheet_uri(),
        [ 'opl-google-fonts' ],
        OPL_VERSION
    );

    // Main CSS
    wp_enqueue_style(
        'opl-main',
        OPL_THEME_URI . '/assets/css/main.css',
        [ 'opl-style' ],
        OPL_VERSION
    );

    // Inline override — force btn--primary text white regardless of WP global link color
    wp_add_inline_style( 'opl-main', '
        html body .btn--primary,
        html body a.btn--primary,
        html body a.btn--primary:link,
        html body a.btn--primary:visited,
        html body a.btn--primary:hover,
        html body a.btn--primary:focus,
        html body a.btn--primary:active {
            color: #ffffff !important;
            text-decoration: none !important;
        }
    ' );

    // Main JS
    wp_enqueue_script(
        'opl-main',
        OPL_THEME_URI . '/assets/js/main.js',
        [],
        OPL_VERSION,
        true
    );

    // Localize script
    wp_localize_script( 'opl-main', 'OPL', [
        'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'opl_nonce' ),
        'siteUrl'  => get_site_url(),
        'themeUrl' => OPL_THEME_URI,
        'lang'     => get_locale(),
        'i18n'     => [
            'analyzing'  => __( 'Analyse en cours…', 'onpagelab' ),
            'enterUrl'   => __( 'Entrez une URL valide', 'onpagelab' ),
            'errorMsg'   => __( 'Une erreur est survenue. Veuillez réessayer.', 'onpagelab' ),
        ],
    ] );

    // Comments reply script
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'opl_enqueue_assets' );

// ============================================================
// WIDGETS / SIDEBARS
// ============================================================
function opl_widgets_init() {
    register_sidebar( [
        'name'          => __( 'Blog, Sidebar', 'onpagelab' ),
        'id'            => 'sidebar-blog',
        'description'   => __( 'Widgets dans la sidebar du blog', 'onpagelab' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget__title">',
        'after_title'   => '</h3>',
    ] );

    register_sidebar( [
        'name'          => __( 'Footer, Colonne 1', 'onpagelab' ),
        'id'            => 'footer-1',
        'before_widget' => '<div class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget__title">',
        'after_title'   => '</h4>',
    ] );
}
add_action( 'widgets_init', 'opl_widgets_init' );

// ============================================================
// CUSTOMIZER — OPTIONS FOOTER
// ============================================================
function opl_customize_register( WP_Customize_Manager $wp_customize ) {

    // ── Panel principal ─────────────────────────────────────
    $wp_customize->add_panel( 'opl_footer_panel', [
        'title'    => __( 'Footer', 'onpagelab' ),
        'priority' => 160,
    ] );

    // ── Section 1 : Bandeau CTA ────────────────────────────
    $wp_customize->add_section( 'opl_footer_cta', [
        'title'    => __( 'Bandeau CTA', 'onpagelab' ),
        'panel'    => 'opl_footer_panel',
        'priority' => 10,
    ] );

    foreach ( [
        'footer_cta_title'    => [ 'text',     __( 'Titre',           'onpagelab' ), __( 'Prêt à optimiser vos pages ?', 'onpagelab' ) ],
        'footer_cta_desc'     => [ 'textarea', __( 'Description',     'onpagelab' ), __( 'Lancez une analyse SEO on-page gratuite en moins de 30 secondes.', 'onpagelab' ) ],
        'footer_cta_btn_text' => [ 'text',     __( 'Texte du bouton', 'onpagelab' ), __( 'Analyser une page gratuitement', 'onpagelab' ) ],
    ] as $id => [ $type, $label, $default ] ) {
        $wp_customize->add_setting( $id, [
            'default'           => $default,
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $id, [
            'label'   => $label,
            'section' => 'opl_footer_cta',
            'type'    => $type,
        ] );
    }

    $wp_customize->add_setting( 'footer_cta_btn_url', [
        'default'           => '/outil-seo/',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'footer_cta_btn_url', [
        'label'   => __( 'URL du bouton', 'onpagelab' ),
        'section' => 'opl_footer_cta',
        'type'    => 'url',
    ] );

    // ── Section 2 : Marque & Réseaux sociaux ───────────────
    $wp_customize->add_section( 'opl_footer_brand', [
        'title'    => __( 'Marque & Réseaux sociaux', 'onpagelab' ),
        'panel'    => 'opl_footer_panel',
        'priority' => 20,
    ] );

    $wp_customize->add_setting( 'footer_tagline', [
        'default'           => __( "L'analyse SEO on-page qui va à l'essentiel.", 'onpagelab' ),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'footer_tagline', [
        'label'   => __( 'Tagline', 'onpagelab' ),
        'section' => 'opl_footer_brand',
        'type'    => 'text',
    ] );

    foreach ( [
        'footer_social_twitter'  => [ __( 'URL Twitter / X', 'onpagelab' ), 'https://twitter.com/onpagelab' ],
        'footer_social_linkedin' => [ __( 'URL LinkedIn',    'onpagelab' ), 'https://www.linkedin.com/company/onpagelab' ],
    ] as $id => [ $label, $default ] ) {
        $wp_customize->add_setting( $id, [
            'default'           => $default,
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ] );
        $wp_customize->add_control( $id, [
            'label'   => $label,
            'section' => 'opl_footer_brand',
            'type'    => 'url',
        ] );
    }

    // ── Section 3 : Barre de bas de page ───────────────────
    $wp_customize->add_section( 'opl_footer_bottom', [
        'title'    => __( 'Barre de bas de page', 'onpagelab' ),
        'panel'    => 'opl_footer_panel',
        'priority' => 30,
    ] );

    $wp_customize->add_setting( 'footer_copyright', [
        'default'           => __( 'OnPageLab. Tous droits réservés.', 'onpagelab' ),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'footer_copyright', [
        'label'       => __( 'Texte copyright (sans l\'année)', 'onpagelab' ),
        'description' => __( 'L\'année courante est ajoutée automatiquement.', 'onpagelab' ),
        'section'     => 'opl_footer_bottom',
        'type'        => 'text',
    ] );

    $wp_customize->add_setting( 'footer_made_with', [
        'default'           => __( 'Fait avec ♥ pour les SEOs', 'onpagelab' ),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'footer_made_with', [
        'label'   => __( 'Texte côté droit', 'onpagelab' ),
        'section' => 'opl_footer_bottom',
        'type'    => 'text',
    ] );
}
add_action( 'customize_register', 'opl_customize_register' );

// ============================================================
// CUSTOM POST TYPES
// ============================================================

// CPT : Feature (pages Features LP)
function opl_register_cpt_feature() {
    register_post_type( 'opl_feature', [
        'labels' => [
            'name'               => __( 'Fonctionnalités', 'onpagelab' ),
            'singular_name'      => __( 'Fonctionnalité', 'onpagelab' ),
            'add_new_item'       => __( 'Ajouter une fonctionnalité', 'onpagelab' ),
            'edit_item'          => __( 'Modifier la fonctionnalité', 'onpagelab' ),
        ],
        'public'      => true,
        'has_archive' => false,
        'rewrite'     => [ 'slug' => 'features' ],
        'supports'    => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
        'menu_icon'   => 'dashicons-star-filled',
        'show_in_rest' => true,
    ] );
}
add_action( 'init', 'opl_register_cpt_feature' );

// CPT : Testimonial
function opl_register_cpt_testimonial() {
    register_post_type( 'opl_testimonial', [
        'labels' => [
            'name'          => __( 'Témoignages', 'onpagelab' ),
            'singular_name' => __( 'Témoignage', 'onpagelab' ),
        ],
        'public'       => false,
        'show_ui'      => true,
        'supports'     => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
        'menu_icon'    => 'dashicons-format-quote',
        'show_in_rest' => true,
    ] );
}
add_action( 'init', 'opl_register_cpt_testimonial' );

// ============================================================
// SEO — SCHEMA MARKUP HELPERS
// ============================================================

/**
 * Output Schema Organization for the site
 */
function opl_schema_organization() {
    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'SoftwareApplication',
        'name'        => 'OnPageLab',
        'url'         => home_url(),
        'applicationCategory' => 'SEO Tool',
        'operatingSystem'     => 'Web',
        'description' => __( 'Outil d\'analyse SEO on-page technique et sémantique', 'onpagelab' ),
        'offers' => [
            '@type'         => 'Offer',
            'price'         => '0',
            'priceCurrency' => 'EUR',
        ],
    ];
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'opl_schema_organization' );

/**
 * Output Article schema for blog posts
 */
function opl_schema_article() {
    if ( ! is_single() ) return;

    global $post;
    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'Article',
        'headline'        => get_the_title(),
        'datePublished'   => get_the_date( 'c' ),
        'dateModified'    => get_the_modified_date( 'c' ),
        'author'          => [
            '@type' => 'Person',
            'name'  => get_the_author_meta( 'display_name' ),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name'  => get_bloginfo( 'name' ),
            'url'   => home_url(),
        ],
        'mainEntityOfPage' => get_permalink(),
    ];

    if ( has_post_thumbnail() ) {
        $img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'opl-thumb-lg' );
        if ( $img ) {
            $schema['image'] = $img[0];
        }
    }

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'opl_schema_article' );

// ============================================================
// PERFORMANCE
// ============================================================

// Remove unnecessary head tags
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

// Disable emojis
function opl_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'opl_disable_emojis' );

// Add preconnect for Google Fonts
function opl_preconnect_fonts() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'opl_preconnect_fonts', 1 );

// ============================================================
// AJAX — SEO ANALYSIS ENDPOINT
// ============================================================

/**
 * AJAX handler for on-page SEO analysis.
 * Calls OPL_Analyzer to crawl, parse and score the target URL.
 */
function opl_ajax_analyze() {
    check_ajax_referer( 'opl_nonce', 'nonce' );

    $url     = isset( $_POST['url'] )     ? esc_url_raw( wp_unslash( $_POST['url'] ) )           : '';
    $keyword = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';

    if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
        wp_send_json_error( [ 'message' => __( 'URL invalide. Vérifiez le format (https://…).', 'onpagelab' ) ] );
    }

    require_once get_template_directory() . '/inc/class-opl-analyzer.php';

    $result = OPL_Analyzer::analyze( $url, $keyword );

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( [ 'message' => $result->get_error_message() ] );
    }

    wp_send_json_success( $result );
}
add_action( 'wp_ajax_opl_analyze',        'opl_ajax_analyze' );
add_action( 'wp_ajax_nopriv_opl_analyze', 'opl_ajax_analyze' );

// ============================================================
// CUSTOM META FIELDS (post/page)
// ============================================================
function opl_register_meta() {
    $meta_fields = [
        'opl_seo_score'          => 'integer',
        'opl_feature_icon'       => 'string',
        'opl_feature_color'      => 'string',
        'opl_lp_cta_text'        => 'string',
        'opl_lp_cta_url'         => 'string',
        'opl_testimonial_author' => 'string',
        'opl_testimonial_role'   => 'string',
        'opl_testimonial_rating' => 'integer',
    ];

    foreach ( $meta_fields as $key => $type ) {
        register_post_meta( '', $key, [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => $type,
        ] );
    }
}
add_action( 'init', 'opl_register_meta' );

// ============================================================
// UTILITY FUNCTIONS
// ============================================================

/**
 * Get reading time for a post
 */
function opl_reading_time( $post_id = null ) {
    $content    = get_post_field( 'post_content', $post_id );
    $word_count = str_word_count( wp_strip_all_tags( $content ) );
    $minutes    = ceil( $word_count / 200 );
    return sprintf(
        _n( '%d min', '%d min', $minutes, 'onpagelab' ),
        $minutes
    );
}

/**
 * Get the score badge color class
 */
function opl_score_class( int $score ): string {
    if ( $score >= 80 ) return 'score--good';
    if ( $score >= 50 ) return 'score--average';
    return 'score--poor';
}

/**
 * Get formatted post date with schema
 */
function opl_post_date( $post_id = null, $format = '' ) {
    if ( ! $format ) $format = get_option( 'date_format' );
    $date     = get_the_date( $format, $post_id );
    $datetime = get_the_date( 'c', $post_id );
    return '<time class="post__date" datetime="' . esc_attr( $datetime ) . '">' . esc_html( $date ) . '</time>';
}

/**
 * Render category pills for a post
 */
function opl_category_pills( $post_id = null ) {
    $cats = get_the_category( $post_id );
    if ( ! $cats ) return;
    foreach ( $cats as $cat ) {
        printf(
            '<a href="%s" class="pill pill--primary">%s</a>',
            esc_url( get_category_link( $cat->term_id ) ),
            esc_html( $cat->name )
        );
    }
}

/**
 * Breadcrumbs (SEO-friendly with schema)
 *
 * Niveaux intermédiaires gérés de deux façons (cumulables) :
 *  1. Hiérarchie de pages WordPress native : définir la page parente
 *     dans WP Admin → Page Attributes → Parent Page.
 *  2. Champ personnalisé "opl_bc_extra" : ajouter des niveaux manuels,
 *     un par ligne au format  Label|https://url
 *     Ex : Fonctionnalités|/features/
 */
function opl_breadcrumbs() {
    if ( is_front_page() ) return;

    $items   = [];
    $items[] = [ 'name' => __( 'Accueil', 'onpagelab' ), 'url' => home_url( '/' ) ];

    if ( is_page() ) {

        // 1. Ancêtres automatiques depuis la hiérarchie de pages WP
        $ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
        foreach ( $ancestors as $ancestor_id ) {
            $items[] = [
                'name' => get_the_title( $ancestor_id ),
                'url'  => get_permalink( $ancestor_id ),
            ];
        }

        // 2. Niveaux supplémentaires via champ personnalisé "opl_bc_extra"
        //    Format : une entrée par ligne → "Label|https://url"
        $extra = get_post_meta( get_the_ID(), 'opl_bc_extra', true );
        if ( $extra ) {
            foreach ( array_filter( array_map( 'trim', explode( "\n", $extra ) ) ) as $line ) {
                $parts = array_map( 'trim', explode( '|', $line, 2 ) );
                if ( 2 === count( $parts ) && $parts[0] !== '' ) {
                    $items[] = [ 'name' => $parts[0], 'url' => $parts[1] ];
                }
            }
        }

        $items[] = [ 'name' => get_the_title(), 'url' => '' ];

    } elseif ( is_category() || is_single() ) {

        $cat = get_the_category();
        if ( $cat ) {
            $items[] = [ 'name' => $cat[0]->name, 'url' => get_category_link( $cat[0]->term_id ) ];
        }
        if ( is_single() ) {
            $items[] = [ 'name' => get_the_title(), 'url' => '' ];
        } else {
            $items[ count( $items ) - 1 ]['url'] = '';
        }
    }

    $schema_items = [];
    echo '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Fil d\'Ariane', 'onpagelab' ) . '"><ol class="breadcrumb__list">';

    foreach ( $items as $i => $item ) {
        $is_last      = ( $i === count( $items ) - 1 );
        $schema_items[] = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $item['name'],
            'item'     => $item['url'] ?: get_permalink(),
        ];
        if ( $is_last ) {
            echo '<li class="breadcrumb__item breadcrumb__item--current" aria-current="page"><span>' . esc_html( $item['name'] ) . '</span></li>';
        } else {
            echo '<li class="breadcrumb__item"><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['name'] ) . '</a></li>';
        }
    }

    echo '</ol></nav>';

    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $schema_items,
    ];
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}
