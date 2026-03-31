<?php
/**
 * Template Name: Landing Page Feature
 * Template Post Type: page
 *
 * Template for individual feature landing pages
 *
 * @package OnPageLab
 */

$feature_icon   = get_post_meta( get_the_ID(), 'opl_feature_icon', true ) ?: '🔍';
$feature_color  = get_post_meta( get_the_ID(), 'opl_feature_color', true ) ?: 'blue';
$lp_cta_text    = get_post_meta( get_the_ID(), 'opl_lp_cta_text', true ) ?: __( 'Tester gratuitement', 'onpagelab' );
$lp_cta_url     = get_post_meta( get_the_ID(), 'opl_lp_cta_url', true ) ?: home_url( '/outil-seo/' );

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<section class="lp-hero section--gradient-<?php echo esc_attr( $feature_color ); ?>" aria-labelledby="lp-headline">
    <div class="container">
        <div class="lp-hero__inner">
            <div class="lp-hero__content">
                <?php opl_breadcrumbs(); ?>
                <div class="pill pill--white-outline"><?php esc_html_e( 'Fonctionnalité', 'onpagelab' ); ?></div>
                <h1 id="lp-headline" class="lp-hero__title"><?php the_title(); ?></h1>
                <?php if ( has_excerpt() ) : ?>
                    <p class="lp-hero__desc"><?php the_excerpt(); ?></p>
                <?php endif; ?>
                <div class="lp-hero__ctas">
                    <a href="<?php echo esc_url( $lp_cta_url ); ?>" class="btn btn--primary btn--lg">
                        <?php echo esc_html( $lp_cta_text ); ?>
                    </a>
                    <a href="#how-it-works" class="btn btn--ghost btn--lg">
                        <?php esc_html_e( 'Comment ça marche', 'onpagelab' ); ?> &darr;
                    </a>
                </div>
                
                <div class="lp-hero__trust">
                    <span>✓ <?php esc_html_e( 'Gratuit', 'onpagelab' ); ?></span>
                    <span>✓ <?php esc_html_e( 'Sans inscription', 'onpagelab' ); ?></span>
                    <span>✓ <?php esc_html_e( 'Instantané', 'onpagelab' ); ?></span>
                </div>
            </div>
            
            <div class="lp-hero__visual">
                <?php if ( has_post_thumbnail() ) : ?>
                    <figure class="lp-hero__image">
                        <?php the_post_thumbnail( 'opl-thumb-md', [
                            'class'   => 'lp-hero__img',
                            'loading' => 'eager',
                            'alt'     => get_the_title(),
                        ] ); ?>
                    </figure>
                <?php else : ?>
                    <div class="feature-illustration feature-illustration--<?php echo esc_attr( $feature_color ); ?>" aria-hidden="true">
                        <span class="feature-illustration__icon"><?php echo esc_html( $feature_icon ); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="lp-benefits section">
    <div class="container">
        <div class="entry-content prose container--wide lp-content">
            <?php the_content(); ?>
        </div>
    </div>
</section>

<section class="lp-how section section--light" id="how-it-works">
    <div class="container container--narrow">
        <h2 class="section-header__title text-center"><?php esc_html_e( 'Comment utiliser cette fonctionnalité', 'onpagelab' ); ?></h2>
        <div class="steps-list">
            <div class="step-item">
                <div class="step-item__number">1</div>
                <div class="step-item__content">
                    <h3><?php esc_html_e( 'Accédez à l\'outil', 'onpagelab' ); ?></h3>
                    <p><?php esc_html_e( 'Rendez-vous sur l\'analyseur SEO OnPageLab, entièrement gratuit et accessible sans inscription.', 'onpagelab' ); ?></p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-item__number">2</div>
                <div class="step-item__content">
                    <h3><?php esc_html_e( 'Entrez l\'URL de votre page', 'onpagelab' ); ?></h3>
                    <p><?php esc_html_e( 'Collez l\'URL complète de la page que vous souhaitez analyser. Toute page publique est compatible.', 'onpagelab' ); ?></p>
                </div>
            </div>
            <div class="step-item">
                <div class="step-item__number">3</div>
                <div class="step-item__content">
                    <h3><?php esc_html_e( 'Consultez les résultats', 'onpagelab' ); ?></h3>
                    <p><?php esc_html_e( 'Obtenez en quelques secondes les résultats complets avec score, problèmes détectés et recommandations d\'optimisation priorisées.', 'onpagelab' ); ?></p>
                </div>
            </div>
        </div>
        <div class="text-center" style="margin-top:2rem;">
            <a href="<?php echo esc_url( $lp_cta_url ); ?>" class="btn btn--primary btn--lg">
                <?php echo esc_html( $lp_cta_text ); ?>
            </a>
        </div>
    </div>
</section>

<?php
$testimonials = new WP_Query( [
    'post_type'      => 'opl_testimonial',
    'posts_per_page' => 3,
    'orderby'        => 'rand',
] );
if ( $testimonials->have_posts() ) :
?>
<section class="testimonials section">
    <div class="container">
        <h2 class="section-header__title text-center"><?php esc_html_e( 'Ce que disent nos utilisateurs', 'onpagelab' ); ?></h2>
        <div class="testimonials-grid">
            <?php while ( $testimonials->have_posts() ) : $testimonials->the_post(); ?>
                <div class="testimonial-card" itemscope itemtype="https://schema.org/Review">
                    <div class="testimonial-card__stars" aria-label="<?php esc_attr_e( '5 étoiles', 'onpagelab' ); ?>">★★★★★</div>
                    <blockquote class="testimonial-card__quote" itemprop="reviewBody">
                        "<?php echo wp_kses_post( get_the_content() ); ?>"
                    </blockquote>
                    <div class="testimonial-card__author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                        <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'opl-square', [ 'class' => 'testimonial-avatar', 'loading' => 'lazy' ] ); ?>
                        <div>
                            <strong itemprop="name"><?php the_title(); ?></strong>
                            <span><?php echo esc_html( get_post_meta( get_the_ID(), 'opl_testimonial_role', true ) ); ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="related-features section section--light">
    <div class="container">
        <h2 class="section-header__title text-center"><?php esc_html_e( 'Autres fonctionnalités', 'onpagelab' ); ?></h2>
        <?php
        $features = new WP_Query( [
            'post_type'      => 'page',
            'posts_per_page' => 4,
            'post__not_in'   => [ get_the_ID() ],
            'meta_query'     => [ [ 'key' => '_wp_page_template', 'value' => 'page-templates/template-feature.php' ] ],
        ] );
        if ( $features->have_posts() ) :
        ?>
        <div class="features-grid features-grid--4">
            <?php while ( $features->have_posts() ) : $features->the_post(); ?>
                <a href="<?php the_permalink(); ?>" class="feature-pill">
                    <span><?php echo esc_html( get_post_meta( get_the_ID(), 'opl_feature_icon', true ) ?: '🔍' ); ?></span>
                    <?php the_title(); ?> &rarr;
                </a>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="lp-final-cta section section--dark text-center">
    <div class="container container--narrow">
        <h2><?php the_title(); ?>, <?php esc_html_e( 'Gratuit, sans inscription', 'onpagelab' ); ?></h2>
        <p><?php esc_html_e( 'Rejoignez les milliers de SEOs qui utilisent OnPageLab pour optimiser leurs pages.', 'onpagelab' ); ?></p>
        <a href="<?php echo esc_url( $lp_cta_url ); ?>" class="btn btn--white btn--lg">
            <?php echo esc_html( $lp_cta_text ); ?>
        </a>
    </div>
</section>

<?php endwhile; ?>

<?php get_footer();
