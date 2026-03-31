<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main-content">
    <?php esc_html_e( 'Aller au contenu principal', 'onpagelab' ); ?>
</a>

<header class="site-header" id="site-header" role="banner">
    <div class="site-header__inner container">

        <div class="site-header__brand">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__logo-text" rel="home" aria-label="<?php bloginfo( 'name' ); ?>">
                    <span class="logo-icon" aria-hidden="true">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="32" height="32" rx="8" fill="#2563EB"/>
                            <path d="M8 10H24M8 16H20M8 22H16" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                            <circle cx="25" cy="22" r="4" fill="#10B981"/>
                            <path d="M23.5 22L24.5 23L26.5 21" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="logo-name">OnPage<strong>Lab</strong></span>
                </a>
            <?php endif; ?>
        </div>

        <nav class="site-header__nav" id="primary-nav" role="navigation" aria-label="<?php esc_attr_e( 'Navigation principale', 'onpagelab' ); ?>">
            <?php
            wp_nav_menu( [
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'menu_class'     => 'nav-menu nav-menu--primary',
                'container'      => false,
                'fallback_cb'    => false,
                'items_wrap'     => '<ul id="%1$s" class="%2$s" role="menubar">%3$s</ul>',
            ] );
            ?>
        </nav>

        <div class="site-header__actions">
            
            <?php if ( function_exists( 'pll_the_languages' ) ) : ?>
                <div class="lang-switcher" aria-label="<?php esc_attr_e( 'Changer de langue', 'onpagelab' ); ?>">
                    <?php pll_the_languages( [ 'show_flags' => 1, 'show_names' => 1 ] ); ?>
                </div>
            <?php endif; ?>

            <a href="<?php echo esc_url( home_url( '/outil-seo/' ) ); ?>" class="btn btn--primary btn--sm">
                <?php esc_html_e( 'Analyser une page', 'onpagelab' ); ?>
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8H13M9 4L13 8L9 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>

        <button class="site-header__hamburger" id="mobile-menu-toggle" aria-expanded="false" aria-controls="primary-nav" aria-label="<?php esc_attr_e( 'Ouvrir le menu', 'onpagelab' ); ?>">
            <span class="hamburger-bar"></span>
            <span class="hamburger-bar"></span>
            <span class="hamburger-bar"></span>
        </button>

    </div>
</header>

<?php if ( get_theme_mod( 'opl_announcement', '' ) ) : ?>
<div class="announcement-bar" role="banner">
    <div class="container">
        <p><?php echo wp_kses_post( get_theme_mod( 'opl_announcement' ) ); ?></p>
        <button class="announcement-bar__close" aria-label="<?php esc_attr_e( 'Fermer', 'onpagelab' ); ?>">&times;</button>
    </div>
</div>
<?php endif; ?>

<div id="page" class="site">
    <main id="main-content" class="site-content" role="main">
