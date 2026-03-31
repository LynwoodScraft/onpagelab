<?php
/**
 * The main template file — fallback
 *
 * @package OnPageLab
 */
get_header(); ?>

<div class="container page-content">
    <div class="content-with-sidebar">

        <div class="main-col">
            <?php if ( have_posts() ) : ?>

                <?php if ( is_home() && ! is_front_page() ) : ?>
                    <header class="archive-header">
                        <h1 class="archive-header__title"><?php esc_html_e( 'Derniers articles', 'onpagelab' ); ?></h1>
                    </header>
                <?php endif; ?>

                <div class="posts-grid">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php get_template_part( 'template-parts/content', get_post_format() ); ?>
                    <?php endwhile; ?>
                </div>

                <?php the_posts_pagination( [
                    'mid_size'  => 2,
                    'prev_text' => '&larr; ' . __( 'Précédent', 'onpagelab' ),
                    'next_text' => __( 'Suivant', 'onpagelab' ) . ' &rarr;',
                ] ); ?>

            <?php else : ?>
                <div class="no-results">
                    <h2><?php esc_html_e( 'Aucun résultat', 'onpagelab' ); ?></h2>
                    <p><?php esc_html_e( 'Votre recherche n\'a retourné aucun résultat. Essayez avec d\'autres mots-clés.', 'onpagelab' ); ?></p>
                    <?php get_search_form(); ?>
                </div>
            <?php endif; ?>
        </div><!-- /.main-col -->

        <?php get_sidebar(); ?>

    </div><!-- /.content-with-sidebar -->
</div><!-- /.container -->

<?php get_footer();
