    </main>
</div>

<footer class="site-footer" id="site-footer" role="contentinfo">

    <div class="footer-cta">
        <div class="container">
            <div class="footer-cta__inner">
                <div class="footer-cta__content">
                    <h2 class="footer-cta__title">
                        <?php echo esc_html( get_theme_mod( 'footer_cta_title', __( 'Prêt à optimiser vos pages ?', 'onpagelab' ) ) ); ?>
                    </h2>
                    <p class="footer-cta__desc">
                        <?php echo esc_html( get_theme_mod( 'footer_cta_desc', __( 'Lancez une analyse SEO on-page gratuite en moins de 30 secondes.', 'onpagelab' ) ) ); ?>
                    </p>
                </div>
                <a href="<?php echo esc_url( get_theme_mod( 'footer_cta_btn_url', home_url( '/outil-seo/' ) ) ); ?>"
                   class="btn btn--white btn--lg">
                    <?php echo esc_html( get_theme_mod( 'footer_cta_btn_text', __( 'Analyser une page gratuitement', 'onpagelab' ) ) ); ?>
                </a>
            </div>
        </div>
    </div>

    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">

                <div class="footer-brand">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-brand__logo" rel="home">
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="32" height="32" rx="8" fill="#2563EB"/>
                            <path d="M8 10H24M8 16H20M8 22H16" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                            <circle cx="25" cy="22" r="4" fill="#10B981"/>
                        </svg>
                        <span class="footer-brand__name">OnPage<strong>Lab</strong></span>
                    </a>
                    <p class="footer-brand__tagline">
                        <?php echo esc_html( get_theme_mod( 'footer_tagline', __( "L'analyse SEO on-page qui va à l'essentiel.", 'onpagelab' ) ) ); ?>
                    </p>

                    <div class="footer-social">
                        <?php
                        $twitter_url  = get_theme_mod( 'footer_social_twitter',  'https://twitter.com/onpagelab' );
                        $linkedin_url = get_theme_mod( 'footer_social_linkedin', 'https://www.linkedin.com/company/onpagelab' );
                        ?>
                        <?php if ( $twitter_url ) : ?>
                        <a href="<?php echo esc_url( $twitter_url ); ?>" class="footer-social__link" target="_blank" rel="noopener" aria-label="Twitter / X">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.737-8.852L1.575 2.25H8.08l4.237 5.601L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/></svg>
                        </a>
                        <?php endif; ?>
                        <?php if ( $linkedin_url ) : ?>
                        <a href="<?php echo esc_url( $linkedin_url ); ?>" class="footer-social__link" target="_blank" rel="noopener" aria-label="LinkedIn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="footer-nav-col">
                    <h3 class="footer-nav__title"><?php esc_html_e( 'Produit', 'onpagelab' ); ?></h3>
                    <?php
                    wp_nav_menu( [
                        'theme_location' => 'footer-1',
                        'menu_class'     => 'footer-nav__list',
                        'container'      => false,
                        'fallback_cb'    => function() {
                            echo '<ul class="footer-nav__list">
                                <li><a href="/outil-seo/">Analyseur SEO</a></li>
                                <li><a href="/outil-seo/audit-technique/">Audit technique</a></li>
                                <li><a href="/outil-seo/analyse-semantique/">Analyse sémantique</a></li>
                                <li><a href="/outil-seo/balises-meta/">Balises meta</a></li>
                                <li><a href="/outil-seo/maillage-interne/">Maillage interne</a></li>
                                <li><a href="/outil-seo/structure-hn/">Structure Hn</a></li>
                                <li><a href="/tarifs/">Tarifs</a></li>
                            </ul>';
                        },
                    ] );
                    ?>
                </div>

                <div class="footer-nav-col">
                    <h3 class="footer-nav__title"><?php esc_html_e( 'Ressources', 'onpagelab' ); ?></h3>
                    <?php
                    wp_nav_menu( [
                        'theme_location' => 'footer-2',
                        'menu_class'     => 'footer-nav__list',
                        'container'      => false,
                        'fallback_cb'    => function() {
                            echo '<ul class="footer-nav__list">
                                <li><a href="/blog/">Blog SEO</a></li>
                                <li><a href="/blog/seo-on-page-guide-complet/">Guide SEO on-page</a></li>
                                <li><a href="/blog/audit-seo-on-page/">Comment faire un audit SEO</a></li>
                                <li><a href="/blog/optimisation-balise-title/">Optimiser la balise title</a></li>
                            </ul>';
                        },
                    ] );
                    ?>
                </div>

                <div class="footer-nav-col">
                    <h3 class="footer-nav__title"><?php esc_html_e( 'Légal', 'onpagelab' ); ?></h3>
                    <?php
                    wp_nav_menu( [
                        'theme_location' => 'footer-3',
                        'menu_class'     => 'footer-nav__list',
                        'container'      => false,
                        'fallback_cb'    => function() {
                            echo '<ul class="footer-nav__list">
                                <li><a href="/a-propos/">À propos</a></li>
                                <li><a href="/mentions-legales/">Mentions légales</a></li>
                                <li><a href="/politique-de-confidentialite/">Confidentialité</a></li>
                                <li><a href="/contact/">Contact</a></li>
                            </ul>';
                        },
                    ] );
                    ?>
                </div>

            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom__inner">
                <p class="footer-bottom__copy">
                    &copy; <?php echo esc_html( date( 'Y' ) ); ?>
                    <?php echo esc_html( get_theme_mod( 'footer_copyright', __( 'OnPageLab. Tous droits réservés.', 'onpagelab' ) ) ); ?>
                </p>
                <p class="footer-bottom__made">
                    <?php echo esc_html( get_theme_mod( 'footer_made_with', __( 'Fait avec ♥ pour les SEOs', 'onpagelab' ) ) ); ?>
                </p>
            </div>
        </div>
    </div>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "OnPageLab",
        "url": "<?php echo esc_url( home_url() ); ?>",
        "logo": "<?php echo esc_url( OPL_THEME_URI ); ?>/assets/images/logo.svg",
        "sameAs": [
            <?php
            $socials = array_filter( [
                get_theme_mod( 'footer_social_twitter',  'https://twitter.com/onpagelab' ),
                get_theme_mod( 'footer_social_linkedin', 'https://www.linkedin.com/company/onpagelab' ),
            ] );
            echo implode( ",\n            ", array_map( function( $url ) {
                return '"' . esc_url( $url ) . '"';
            }, $socials ) );
            ?>
        ]
    }
    </script>

</footer>

<?php wp_footer(); ?>
</body>
</html>
