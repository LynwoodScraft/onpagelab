<?php
/**
 * Blog archive template — OnPageLab
 *
 * @package OnPageLab
 */
get_header(); ?>

<div class="archive-hero section--light">
    <div class="container">
        <div class="archive-hero__inner">
            <div class="pill pill--blue"><?php esc_html_e( 'Blog SEO', 'onpagelab' ); ?></div>
            <h1 class="archive-hero__title">
                <?php
                if ( is_category() ) {
                    echo esc_html( single_cat_title( '', false ) );
                } elseif ( is_tag() ) {
                    printf( esc_html__( 'Tag : %s', 'onpagelab' ), single_tag_title( '', false ) );
                } elseif ( is_author() ) {
                    printf( esc_html__( 'Articles de %s', 'onpagelab' ), get_the_author() );
                } else {
                    esc_html_e( 'Guides & Ressources SEO on-page', 'onpagelab' );
                }
                ?>
            </h1>
            <?php if ( is_category() && category_description() ) : ?>
                <p class="archive-hero__desc"><?php echo esc_html( category_description() ); ?></p>
            <?php else : ?>
                <p class="archive-hero__desc">
                    <?php esc_html_e( 'Guides pratiques, méthodes et conseils pour maîtriser le SEO on-page, technique, sémantique et stratégique.', 'onpagelab' ); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="archive-filters" role="navigation" aria-label="<?php esc_attr_e( 'Filtres par catégorie', 'onpagelab' ); ?>">
            <a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="pill pill--filter <?php echo ( ! is_category() ) ? 'pill--active' : ''; ?>">
                <?php esc_html_e( 'Tous', 'onpagelab' ); ?>
            </a>
            <?php
            $cats = get_categories( [ 'hide_empty' => true ] );
            foreach ( $cats as $cat ) :
            ?>
                <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
                   class="pill pill--filter <?php echo ( is_category( $cat->term_id ) ) ? 'pill--active' : ''; ?>">
                    <?php echo esc_html( $cat->name ); ?>
                </a>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<div class="section">
    <div class="container">

        <?php if ( have_posts() ) : ?>
            <div class="posts-grid posts-grid--3" id="posts-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    <article <?php post_class( 'post-card' ); ?> id="post-<?php the_ID(); ?>">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="post-card__thumbnail" tabindex="-1" aria-hidden="true">
                                <?php the_post_thumbnail( 'opl-thumb-sm', [ 'loading' => 'lazy', 'alt' => '' ] ); ?>
                            </a>
                        <?php endif; ?>
                        <div class="post-card__body">
                            <div class="post-card__meta">
                                <?php opl_category_pills(); ?>
                                <span class="post-card__reading-time"><?php echo esc_html( opl_reading_time() ); ?></span>
                            </div>
                            <h2 class="post-card__title post-card__title--h2">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <p class="post-card__excerpt"><?php the_excerpt(); ?></p>
                            <div class="post-card__footer">
                                <?php echo opl_post_date(); ?>
                                <a href="<?php the_permalink(); ?>" class="post-card__link" aria-label="<?php echo esc_attr( sprintf( __( 'Lire %s', 'onpagelab' ), get_the_title() ) ); ?>">
                                    <?php esc_html_e( 'Lire', 'onpagelab' ); ?> &rarr;
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="pagination-wrap">
                <?php the_posts_pagination( [
                    'mid_size'           => 2,
                    'prev_text'          => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M15 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> ' . __( 'Précédent', 'onpagelab' ),
                    'next_text'          => __( 'Suivant', 'onpagelab' ) . ' <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M9 18l7-7-7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                    'screen_reader_text' => __( 'Navigation entre les pages', 'onpagelab' ),
                ] ); ?>
            </div>

        <?php else : ?>
            <div class="no-results">
                <h2><?php esc_html_e( 'Aucun article trouvé', 'onpagelab' ); ?></h2>
                <p><?php esc_html_e( 'Il n\'y a pas encore d\'articles dans cette catégorie.', 'onpagelab' ); ?></p>
                <a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="btn btn--primary">
                    <?php esc_html_e( 'Voir tous les articles', 'onpagelab' ); ?>
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>

<div class="archive-cta section section--dark">
    <div class="container container--narrow text-center">
        <h2><?php esc_html_e( 'Prêt à analyser vos pages ?', 'onpagelab' ); ?></h2>
        <p><?php esc_html_e( 'Mettez en pratique ce que vous apprenez dans nos guides. L\'outil est gratuit et sans inscription.', 'onpagelab' ); ?></p>
        <a href="<?php echo esc_url( home_url( '/outil-seo/' ) ); ?>" class="btn btn--white btn--lg">
            <?php esc_html_e( 'Lancer une analyse SEO', 'onpagelab' ); ?>
        </a>
    </div>
</div>

<?php get_footer();
