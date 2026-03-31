<?php
/**
 * Sidebar template — OnPageLab
 *
 * @package OnPageLab
 */
?>
<aside class="sidebar" id="sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Sidebar', 'onpagelab' ); ?>">

    <!-- CTA Sidebar -->
    <div class="sidebar-widget sidebar-cta">
        <div class="sidebar-cta__inner">
            <div class="sidebar-cta__icon" aria-hidden="true">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
            </div>
            <h3 class="sidebar-cta__title">
                <?php esc_html_e( 'Analysez votre page gratuitement', 'onpagelab' ); ?>
            </h3>
            <p class="sidebar-cta__desc">
                <?php esc_html_e( 'Obtenez un score SEO on-page complet en 30 secondes.', 'onpagelab' ); ?>
            </p>
            <a href="<?php echo esc_url( home_url( '/outil-seo/' ) ); ?>" class="btn btn--primary btn--block">
                <?php esc_html_e( 'Lancer l\'analyse', 'onpagelab' ); ?>
            </a>
        </div>
    </div>

    <!-- Newsletter widget -->
    <div class="sidebar-widget sidebar-newsletter">
        <h3 class="sidebar-widget__title">
            <?php esc_html_e( 'Newsletter SEO', 'onpagelab' ); ?>
        </h3>
        <p class="sidebar-widget__desc">
            <?php esc_html_e( 'Conseils SEO on-page chaque semaine. Gratuit.', 'onpagelab' ); ?>
        </p>
        <form class="sidebar-newsletter__form" method="post" novalidate>
            <input
                type="email"
                name="newsletter_email"
                placeholder="<?php esc_attr_e( 'votre@email.com', 'onpagelab' ); ?>"
                required
                class="sidebar-newsletter__input"
                aria-label="<?php esc_attr_e( 'Votre adresse e-mail', 'onpagelab' ); ?>"
            />
            <button type="submit" class="btn btn--primary btn--block">
                <?php esc_html_e( 'Je m\'abonne', 'onpagelab' ); ?>
            </button>
        </form>
    </div>

    <!-- Recent posts -->
    <?php
    $recent_posts = wp_get_recent_posts( [
        'numberposts' => 4,
        'post_status' => 'publish',
    ] );
    if ( $recent_posts ) :
    ?>
    <div class="sidebar-widget sidebar-recent-posts">
        <h3 class="sidebar-widget__title">
            <?php esc_html_e( 'Articles récents', 'onpagelab' ); ?>
        </h3>
        <ul class="sidebar-recent-posts__list">
            <?php foreach ( $recent_posts as $recent ) : ?>
            <li class="sidebar-recent-posts__item">
                <?php if ( has_post_thumbnail( $recent['ID'] ) ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $recent['ID'] ) ); ?>" class="sidebar-recent-posts__thumb" tabindex="-1" aria-hidden="true">
                        <?php echo get_the_post_thumbnail( $recent['ID'], 'opl-thumb-sm' ); ?>
                    </a>
                <?php endif; ?>
                <div class="sidebar-recent-posts__meta">
                    <a href="<?php echo esc_url( get_permalink( $recent['ID'] ) ); ?>" class="sidebar-recent-posts__title">
                        <?php echo esc_html( $recent['post_title'] ); ?>
                    </a>
                    <span class="sidebar-recent-posts__date">
                        <?php echo esc_html( get_the_date( 'd/m/Y', $recent['ID'] ) ); ?>
                    </span>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    wp_reset_query();
    endif;
    ?>

    <!-- Category cloud -->
    <div class="sidebar-widget sidebar-categories">
        <h3 class="sidebar-widget__title">
            <?php esc_html_e( 'Catégories', 'onpagelab' ); ?>
        </h3>
        <?php
        $categories = get_categories( [ 'hide_empty' => true ] );
        if ( $categories ) :
        ?>
        <ul class="sidebar-categories__list">
            <?php foreach ( $categories as $cat ) : ?>
            <li class="sidebar-categories__item">
                <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="sidebar-categories__link">
                    <?php echo esc_html( $cat->name ); ?>
                    <span class="sidebar-categories__count"><?php echo esc_html( $cat->count ); ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>

    <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
        <?php dynamic_sidebar( 'sidebar-1' ); ?>
    <?php endif; ?>

</aside><!-- /.sidebar -->
