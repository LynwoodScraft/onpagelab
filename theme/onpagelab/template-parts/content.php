<?php
/**
 * Template part for displaying posts in the loop
 *
 * @package OnPageLab
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?> itemscope itemtype="https://schema.org/Article">

    <?php if ( has_post_thumbnail() ) : ?>
    <a href="<?php the_permalink(); ?>" class="post-card__thumb" tabindex="-1" aria-hidden="true">
        <?php the_post_thumbnail( 'opl-thumb-md', [ 'loading' => 'lazy', 'itemprop' => 'image' ] ); ?>
    </a>
    <?php endif; ?>

    <div class="post-card__body">

        <?php opl_category_pills(); ?>

        <h2 class="post-card__title" itemprop="headline">
            <a href="<?php the_permalink(); ?>" rel="bookmark">
                <?php the_title(); ?>
            </a>
        </h2>

        <div class="post-card__meta">
            <span class="post-card__meta-date" itemprop="datePublished" content="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                <?php echo esc_html( opl_post_date() ); ?>
            </span>
            <span class="post-card__meta-reading" aria-label="<?php esc_attr_e( 'Temps de lecture', 'onpagelab' ); ?>">
                <?php echo esc_html( opl_reading_time() ); ?>
            </span>
        </div>

        <p class="post-card__excerpt" itemprop="description">
            <?php echo wp_kses_post( get_the_excerpt() ); ?>
        </p>

        <a href="<?php the_permalink(); ?>" class="post-card__link" aria-label="<?php echo esc_attr( sprintf( __( 'Lire : %s', 'onpagelab' ), get_the_title() ) ); ?>">
            <?php esc_html_e( 'Lire l\'article', 'onpagelab' ); ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
        </a>

    </div><!-- /.post-card__body -->

</article><!-- /.post-card -->
