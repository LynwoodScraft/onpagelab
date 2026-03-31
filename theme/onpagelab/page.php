<?php
/**
 * The template for displaying all pages
 *
 * @package OnPageLab
 */
get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

    <article id="page-<?php the_ID(); ?>" <?php post_class( 'page-article' ); ?>>

        <!-- Page Hero (masqué si champ personnalisé "hide_title" = 1) -->
        <?php if ( ! get_post_meta( get_the_ID(), 'hide_title', true ) ) : ?>
        <div class="page-hero">
            <div class="container">
                <?php opl_breadcrumbs(); ?>
                <h1 class="page-hero__title"><?php the_title(); ?></h1>
                <?php if ( has_excerpt() ) : ?>
                    <p class="page-hero__intro"><?php the_excerpt(); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Page Content -->
        <div class="container page-content">
            <div class="entry-content prose">
                <?php
                the_content();
                wp_link_pages( [
                    'before' => '<div class="page-links">' . __( 'Pages :', 'onpagelab' ),
                    'after'  => '</div>',
                ] );
                ?>
            </div><!-- /.entry-content -->
        </div><!-- /.page-content -->

    </article><!-- /.page-article -->

<?php endwhile; ?>

<?php get_footer();
