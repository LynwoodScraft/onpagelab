<?php
/**
 * Template Name: Outil SEO On-Page
 * Template Post Type: page
 *
 * L'outil principal d'analyse SEO on-page
 *
 * @package OnPageLab
 */
get_header(); ?>

<section class="tool-hero section--dark" aria-labelledby="tool-headline">
    <div class="container">
        <div class="tool-hero__content">
            <h1 id="tool-headline" class="tool-hero__title">
                <?php esc_html_e( 'Analyseur SEO on-page', 'onpagelab' ); ?>
            </h1>
            <p class="tool-hero__desc">
                <?php esc_html_e( 'Entrez l\'URL d\'une page pour obtenir un audit SEO complet, technique et sémantique, avec score, problèmes détectés et recommandations.', 'onpagelab' ); ?>
            </p>

            <div class="tool-form-wrap">
                <form class="tool-form" id="tool-form"
                      action="<?php echo esc_url( home_url( '/analysis/' ) ); ?>"
                      method="get"
                      aria-label="<?php esc_attr_e( 'Formulaire d\'analyse SEO', 'onpagelab' ); ?>">
                    <div class="tool-form__row">
                        <div class="tool-form__input-group">
                            <label for="tool-url" class="tool-form__label">
                                <?php esc_html_e( 'URL de la page à analyser', 'onpagelab' ); ?>
                            </label>
                            <div class="tool-form__input-wrap">
                                <span class="tool-form__icon" aria-hidden="true">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                </span>
                                <input
                                    type="url"
                                    id="tool-url"
                                    name="url"
                                    class="tool-form__input"
                                    placeholder="https://votre-site.com/votre-page/"
                                    required
                                    autocomplete="url"
                                >
                            </div>
                        </div>
                        <div class="tool-form__input-group">
                            <label for="tool-keyword" class="tool-form__label">
                                <?php esc_html_e( 'Mot-clé cible (optionnel)', 'onpagelab' ); ?>
                            </label>
                            <div class="tool-form__input-wrap">
                                <span class="tool-form__icon" aria-hidden="true">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                </span>
                                <input
                                    type="text"
                                    id="tool-keyword"
                                    name="keyword"
                                    class="tool-form__input"
                                    placeholder="<?php esc_attr_e( 'ex : audit SEO on-page', 'onpagelab' ); ?>"
                                    autocomplete="off"
                                >
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn--primary btn--xl tool-form__submit" id="tool-submit">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        <span id="tool-submit-text"><?php esc_html_e( 'Lancer l\'analyse', 'onpagelab' ); ?></span>
                    </button>
                </form>
                <p class="tool-form__hint"><?php esc_html_e( '✓ Gratuit · ✓ Sans inscription · ✓ Résultats en moins de 30 secondes', 'onpagelab' ); ?></p>
            </div>
        </div>
    </div>
</section>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <?php if ( get_the_content() ) : ?>
        <div class="container section">
            <div class="entry-content prose container--narrow">
                <?php the_content(); ?>
            </div>
        </div>
    <?php endif; ?>
<?php endwhile; endif; ?>

<section class="tool-features section section--light">
    <div class="container">
        <h2 class="tool-features__title"><?php esc_html_e( 'Ce que l\'analyseur vérifie', 'onpagelab' ); ?></h2>
        <div class="features-grid features-grid--4">
            <?php
            $checks = [
                [ 'icon' => '🏷️', 'title' => __( 'Balise Title', 'onpagelab' ),         'desc' => __( 'Longueur, mot-clé, unicité', 'onpagelab' ) ],
                [ 'icon' => '📝', 'title' => __( 'Meta Description', 'onpagelab' ),      'desc' => __( 'Longueur, contenu, CTR', 'onpagelab' ) ],
                [ 'icon' => '📐', 'title' => __( 'Structure H1–H6', 'onpagelab' ),        'desc' => __( 'Hiérarchie, unicité H1', 'onpagelab' ) ],
                [ 'icon' => '🔑', 'title' => __( 'Densité mots-clés', 'onpagelab' ),      'desc' => __( 'Fréquence et répartition', 'onpagelab' ) ],
                [ 'icon' => '🔗', 'title' => __( 'Liens internes', 'onpagelab' ),          'desc' => __( 'Nombre, ancres, PageRank', 'onpagelab' ) ],
                [ 'icon' => '🌐', 'title' => __( 'Liens externes', 'onpagelab' ),          'desc' => __( 'Attributs, destinations', 'onpagelab' ) ],
                [ 'icon' => '🖼️', 'title' => __( 'Attributs alt', 'onpagelab' ),          'desc' => __( 'Présence, qualité', 'onpagelab' ) ],
                [ 'icon' => '📊', 'title' => __( 'Richesse sémantique', 'onpagelab' ),    'desc' => __( 'Champ lexical, entités', 'onpagelab' ) ],
                [ 'icon' => '🔖', 'title' => __( 'Balise canonique', 'onpagelab' ),       'desc' => __( 'Présence et cohérence', 'onpagelab' ) ],
                [ 'icon' => '📱', 'title' => __( 'Balise viewport', 'onpagelab' ),        'desc' => __( 'Compatibilité mobile', 'onpagelab' ) ],
                [ 'icon' => '🗺️', 'title' => __( 'Open Graph', 'onpagelab' ),             'desc' => __( 'og:title, og:image, og:desc', 'onpagelab' ) ],
                [ 'icon' => '⭐', 'title' => __( 'Schema markup', 'onpagelab' ),          'desc' => __( 'JSON-LD, présence et type', 'onpagelab' ) ],
            ];
            foreach ( $checks as $check ) :
            ?>
                <div class="check-card">
                    <span class="check-card__icon" aria-hidden="true"><?php echo esc_html( $check['icon'] ); ?></span>
                    <h3 class="check-card__title"><?php echo esc_html( $check['title'] ); ?></h3>
                    <p class="check-card__desc"><?php echo esc_html( $check['desc'] ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer();
