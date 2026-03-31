<?php
/**
 * Template part for displaying single post content
 *
 * @package OnPageLab
 */
?>
<div class="entry-content prose" id="article-content" itemprop="articleBody">
    <?php the_content(); ?>
</div><!-- /.entry-content -->

<?php
// Page links for paginated content
wp_link_pages( [
    'before'      => '<nav class="page-links" aria-label="' . esc_attr__( 'Pages', 'onpagelab' ) . '">',
    'after'       => '</nav>',
    'link_before' => '<span>',
    'link_after'  => '</span>',
] );
?>
