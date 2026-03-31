<?php
/**
 * Template for single blog posts — OnPageLab
 *
 * @package OnPageLab
 */
get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'article-single' ); ?> itemscope itemtype="https://schema.org/Article">

    <div class="article-hero section--light">
        <div class="container container--narrow">
            <?php opl_breadcrumbs(); ?>

            <div class="article-meta article-meta--top">
                <?php opl_category_pills(); ?>
                <span class="article-meta__reading-time">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <?php echo esc_html( opl_reading_time() ); ?> <?php esc_html_e( 'de lecture', 'onpagelab' ); ?>
                </span>
            </div>

            <h1 class="article-hero__title" itemprop="headline"><?php the_title(); ?></h1>

            <?php if ( has_excerpt() ) : ?>
                <p class="article-hero__intro" itemprop="description"><?php the_excerpt(); ?></p>
            <?php endif; ?>

            <div class="article-hero__author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                <?php
                $author_id = get_the_author_meta( 'ID' );
                echo get_avatar( $author_id, 48, '', '', [ 'class' => 'author-avatar' ] );
                ?>
                <div class="author-info">
                    <span class="author-info__name" itemprop="name"><?php the_author(); ?></span>
                    <span class="author-info__date">
                        <?php esc_html_e( 'Publié le', 'onpagelab' ); ?> <?php echo opl_post_date(); ?>
                        <?php if ( get_the_modified_date() !== get_the_date() ) : ?>
                            &mdash; <?php esc_html_e( 'Mis à jour le', 'onpagelab' ); ?>
                            <time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date() ); ?></time>
                        <?php endif; ?>
                    </span>
                </div>
            </div>

        </div>
    </div>

    <?php if ( has_post_thumbnail() ) : ?>
        <div class="article-featured-image">
            <div class="container container--wide">
                <?php the_post_thumbnail( 'opl-thumb-lg', [
                    'loading'  => 'eager',
                    'itemprop' => 'image',
                    'alt'      => get_the_title(),
                ] ); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="article-layout">

            <aside class="article-toc" id="article-toc" aria-label="<?php esc_attr_e( 'Table des matières', 'onpagelab' ); ?>">
                <div class="article-toc__inner sticky">
                    <h2 class="article-toc__title"><?php esc_html_e( 'Sommaire', 'onpagelab' ); ?></h2>
                    <nav id="toc-nav" aria-label="<?php esc_attr_e( 'Table des matières', 'onpagelab' ); ?>">
                        
                        <div id="toc-list" class="toc-list"></div>
                    </nav>
                </div>
            </aside>

            <div class="article-body">
                <div class="entry-content prose" itemprop="articleBody" id="article-content">
                    <?php the_content(); ?>
                    <?php wp_link_pages(); ?>
                </div>

                <?php
                $tags = get_the_tags();
                if ( $tags ) :
                ?>
                <div class="article-tags">
                    <span class="article-tags__label"><?php esc_html_e( 'Tags :', 'onpagelab' ); ?></span>
                    <?php foreach ( $tags as $tag ) : ?>
                        <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="pill pill--gray">
                            <?php echo esc_html( $tag->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="author-bio" itemscope itemtype="https://schema.org/Person">
                    <?php echo get_avatar( get_the_author_meta( 'ID' ), 80, '', '', [ 'class' => 'author-bio__avatar' ] ); ?>
                    <div class="author-bio__content">
                        <span class="author-bio__label"><?php esc_html_e( 'Rédigé par', 'onpagelab' ); ?></span>
                        <h3 class="author-bio__name" itemprop="name"><?php the_author(); ?></h3>
                        <p class="author-bio__desc" itemprop="description">
                            <?php echo esc_html( get_the_author_meta( 'description' ) ?: __( 'Expert SEO & fondateur d\'OnPageLab. Spécialisé dans l\'optimisation on-page technique et sémantique.', 'onpagelab' ) ); ?>
                        </p>
                    </div>
                </div>

                <div class="article-cta">
                    <div class="article-cta__content">
                        <h3 class="article-cta__title"><?php esc_html_e( 'Passez à l\'action, analysez votre page', 'onpagelab' ); ?></h3>
                        <p class="article-cta__desc"><?php esc_html_e( 'Mettez en pratique ce que vous venez d\'apprendre. Lancez une analyse SEO on-page gratuite en 30 secondes.', 'onpagelab' ); ?></p>
                    </div>
                    <a href="<?php echo esc_url( home_url( '/outil-seo/' ) ); ?>" class="btn btn--primary">
                        <?php esc_html_e( 'Analyser ma page', 'onpagelab' ); ?>
                    </a>
                </div>

            </div>

        </div>
    </div>

</article>

<?php
$categories   = get_the_category();
$category_ids = wp_list_pluck( $categories, 'term_id' );
$related = new WP_Query( [
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post__not_in'   => [ get_the_ID() ],
    'category__in'   => $category_ids,
    'orderby'        => 'rand',
] );

if ( $related->have_posts() ) :
?>
<section class="related-posts section section--light">
    <div class="container">
        <h2 class="related-posts__title"><?php esc_html_e( 'Articles similaires', 'onpagelab' ); ?></h2>
        <div class="posts-grid posts-grid--3">
            <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                <article class="post-card">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>" class="post-card__thumbnail" tabindex="-1"><<?php the_post_thumbnail( 'opl-thumb-sm', [ 'loading' => 'lazy', 'alt' => '' ] ); ?></a>
                    <?php endif; ?>
                    <div class="post-card__body">
                        <h3 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p class="post-card__excerpt"><?php the_excerpt(); ?></p>
                        <a href="<?php the_permalink(); ?>" class="post-card__link"><?php esc_html_e( 'Lire', 'onpagelab' ); ?> &rarr;</a>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php endwhile; ?>

<?php get_footer();
