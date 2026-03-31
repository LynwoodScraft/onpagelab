<?php
/**
 * 404 page — OnPageLab
 *
 * @package OnPageLab
 */
get_header(); ?>

<section class="error-404 section" aria-labelledby="error-heading">
    <div class="container container--narrow text-center">
        <div class="error-404__visual" aria-hidden="true">
            <span class="error-404__code">404</span>
        </div>
        <h1 id="error-heading"><?php esc_html_e( 'Page introuvable', 'onpagelab' ); ?></h1>
        <p class="error-404__desc">
            <?php esc_html_e( 'La page que vous cherchez n\'existe pas ou a été déplacée. Essayez l\'une de ces options :', 'onpagelab' ); ?>
        </p>
        <div class="error-404__actions">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">
                <?php esc_html_e( 'Retour à l\'accueil', 'onpagelab' ); ?>
            </a>
            <a href="<?php echo esc_url( home_url( '/outil-seo/' ) ); ?>" class="btn btn--outline">
                <?php esc_html_e( 'Lancer une analyse SEO', 'onpagelab' ); ?>
            </a>
            <a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="btn btn--outline">
                <?php esc_html_e( 'Voir le blog', 'onpagelab' ); ?>
            </a>
        </div>
        <?php get_search_form(); ?>
    </div>
</section>

<style>
.error-404 { padding: 6rem 0; }
.error-404__code { font-size: 8rem; font-weight: 900; color: var(--opl-primary); opacity: .15; line-height: 1; display: block; }
.error-404 h1 { font-size: 2.25rem; margin: 1.5rem 0 1rem; }
.error-404__desc { color: var(--opl-text-secondary); font-size: 1.125rem; margin-bottom: 2rem; }
.error-404__actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-bottom: 2rem; }
</style>

<?php get_footer();
