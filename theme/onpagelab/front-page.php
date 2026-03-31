<?php
/**
 * Front page template — OnPageLab Homepage
 *
 * @package OnPageLab
 */
get_header(); ?>

<section class="hero hero--home" aria-labelledby="hero-headline">
    <div class="container">
        <div class="hero__inner">

            <div class="hero__content">
                
                <div class="hero__badge">
                    <span class="badge badge--green">
                        <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg>
                        <?php esc_html_e( 'Outil 100% gratuit', 'onpagelab' ); ?>
                    </span>
                </div>

                <h1 id="hero-headline" class="hero__title">
                    <?php esc_html_e( 'Analysez vos pages pour un', 'onpagelab' ); ?>
                    <span class="text-gradient"><?php esc_html_e( 'SEO on-page parfait', 'onpagelab' ); ?></span>
                </h1>

                <p class="hero__desc">
                    <?php esc_html_e( 'OnPageLab analyse en profondeur la dimension technique et sémantique de vos pages web. Détectez les problèmes SEO, obtenez des recommandations concrètes et améliorez votre positionnement Google.', 'onpagelab' ); ?>
                </p>

                <div class="hero__analyzer" id="hero-analyzer">
                    <form class="analyzer-form" id="analyzer-form" role="search" aria-label="<?php esc_attr_e( 'Analyser une URL', 'onpagelab' ); ?>">
                        <div class="analyzer-form__input-wrap">
                            <span class="analyzer-form__icon" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </span>
                            <input
                                type="url"
                                id="analyzer-url"
                                name="url"
                                class="analyzer-form__input"
                                placeholder="<?php esc_attr_e( 'https://votre-site.com/votre-page/', 'onpagelab' ); ?>"
                                required
                                aria-label="<?php esc_attr_e( 'URL de la page à analyser', 'onpagelab' ); ?>"
                                autocomplete="url"
                            >
                        </div>
                        <button type="submit" class="btn btn--primary btn--lg analyzer-form__btn" id="analyzer-submit">
                            <span class="btn-text"><?php esc_html_e( 'Analyser maintenant', 'onpagelab' ); ?></span>
                            <span class="btn-loading" aria-hidden="true" hidden>
                                <svg class="spin" width="20" height="20" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="31.4 31.4" stroke-linecap="round"/></svg>
                            </span>
                        </button>
                    </form>
                    <p class="analyzer-form__hint">
                        <?php esc_html_e( '✓ Gratuit &nbsp;·&nbsp; ✓ Sans inscription &nbsp;·&nbsp; ✓ Résultats en 30 secondes', 'onpagelab' ); ?>
                    </p>
                </div>

                <div class="hero__stats">
                    <div class="stat-item">
                        <strong class="stat-item__value">50 000+</strong>
                        <span class="stat-item__label"><?php esc_html_e( 'pages analysées', 'onpagelab' ); ?></span>
                    </div>
                    <div class="stat-item">
                        <strong class="stat-item__value">98%</strong>
                        <span class="stat-item__label"><?php esc_html_e( 'satisfaction', 'onpagelab' ); ?></span>
                    </div>
                    <div class="stat-item">
                        <strong class="stat-item__value">30 sec</strong>
                        <span class="stat-item__label"><?php esc_html_e( 'résultats', 'onpagelab' ); ?></span>
                    </div>
                </div>
            </div>

            <div class="hero__visual" aria-hidden="true">
                <div class="mock-dashboard">
                    <div class="mock-dashboard__header">
                        <div class="mock-dots"><span></span><span></span><span></span></div>
                        <span class="mock-dashboard__url">https://example.com/page/</span>
                    </div>
                    <div class="mock-dashboard__body">
                        
                        <div class="mock-scores">
                            <div class="mock-score mock-score--global">
                                <div class="mock-score__ring mock-score__ring--good">
                                    <svg viewBox="0 0 36 36"><circle cx="18" cy="18" r="15.9" fill="none" stroke="#E2E8F0" stroke-width="2.5"/><circle cx="18" cy="18" r="15.9" fill="none" stroke="#10B981" stroke-width="2.5" stroke-dasharray="82 18" stroke-linecap="round" transform="rotate(-90 18 18)"/></svg>
                                    <span>82</span>
                                </div>
                                <label><?php esc_html_e( 'Score global', 'onpagelab' ); ?></label>
                            </div>
                            <div class="mock-sub-scores">
                                <div class="mock-sub-score">
                                    <div class="progress-bar"><div class="progress-bar__fill" style="width:90%; background:#10B981"></div></div>
                                    <span><?php esc_html_e( 'Balises', 'onpagelab' ); ?>, 90/100</span>
                                </div>
                                <div class="mock-sub-score">
                                    <div class="progress-bar"><div class="progress-bar__fill" style="width:75%; background:#F59E0B"></div></div>
                                    <span><?php esc_html_e( 'Contenu', 'onpagelab' ); ?>, 75/100</span>
                                </div>
                                <div class="mock-sub-score">
                                    <div class="progress-bar"><div class="progress-bar__fill" style="width:68%; background:#F59E0B"></div></div>
                                    <span><?php esc_html_e( 'Maillage', 'onpagelab' ); ?>, 68/100</span>
                                </div>
                                <div class="mock-sub-score">
                                    <div class="progress-bar"><div class="progress-bar__fill" style="width:95%; background:#10B981"></div></div>
                                    <span><?php esc_html_e( 'Structure', 'onpagelab' ); ?>, 95/100</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mock-issues">
                            <div class="mock-issue mock-issue--warning">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L1 21h22L12 2zm0 3.5L20.5 19h-17L12 5.5zm-1 5v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
                                <span><?php esc_html_e( 'Meta description trop courte (89 car.)', 'onpagelab' ); ?></span>
                            </div>
                            <div class="mock-issue mock-issue--error">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>
                                <span><?php esc_html_e( 'Aucun lien interne détecté', 'onpagelab' ); ?></span>
                            </div>
                            <div class="mock-issue mock-issue--success">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                <span><?php esc_html_e( 'H1 optimisé, mot-clé présent', 'onpagelab' ); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="hero__bg-decoration" aria-hidden="true">
        <div class="hero__bg-glow hero__bg-glow--blue"></div>
        <div class="hero__bg-glow hero__bg-glow--purple"></div>
    </div>
</section>

<?php if ( ! get_post_meta( get_option( 'page_on_front' ), 'opl_hide_logos_band', true ) ) : ?>
<section class="logos-band" aria-label="<?php esc_attr_e( 'Ils utilisent OnPageLab', 'onpagelab' ); ?>">
    <div class="container">
        <p class="logos-band__label"><?php esc_html_e( 'Utilisé par des SEOs chez', 'onpagelab' ); ?></p>
        <div class="logos-band__logos">
            
            <span class="logo-placeholder">Agence Alpha</span>
            <span class="logo-placeholder">Studio Beta</span>
            <span class="logo-placeholder">Corp Gamma</span>
            <span class="logo-placeholder">Lab Delta</span>
            <span class="logo-placeholder">Tech Epsilon</span>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="features-overview section" aria-labelledby="features-heading">
    <div class="container">

        <div class="section-header">
            <div class="pill pill--blue"><?php esc_html_e( 'Fonctionnalités', 'onpagelab' ); ?></div>
            <h2 id="features-heading" class="section-header__title">
                <?php esc_html_e( 'Tout ce dont vous avez besoin pour un SEO on-page impeccable', 'onpagelab' ); ?>
            </h2>
            <p class="section-header__desc">
                <?php esc_html_e( 'OnPageLab combine audit technique et analyse sémantique dans un seul outil simple et rapide.', 'onpagelab' ); ?>
            </p>
        </div>

        <div class="features-grid features-grid--3">

            <div class="feature-card">
                <div class="feature-card__icon feature-card__icon--blue">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <h3 class="feature-card__title">
                    <a href="<?php echo esc_url( home_url( '/outil-seo/audit-technique/' ) ); ?>">
                        <?php esc_html_e( 'Audit technique SEO on-page', 'onpagelab' ); ?>
                    </a>
                </h3>
                <p class="feature-card__desc">
                    <?php esc_html_e( 'Vérifiez balises title, meta description, structure Hn, balises canoniques, temps de chargement et 40+ critères techniques.', 'onpagelab' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/outil-seo/audit-technique/' ) ); ?>" class="feature-card__link">
                    <?php esc_html_e( 'En savoir plus', 'onpagelab' ); ?> &rarr;
                </a>
            </div>

            <div class="feature-card">
                <div class="feature-card__icon feature-card__icon--purple">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M4 6h16M4 12h12M4 18h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <h3 class="feature-card__title">
                    <a href="<?php echo esc_url( home_url( '/outil-seo/analyse-semantique/' ) ); ?>">
                        <?php esc_html_e( 'Analyse sémantique du contenu', 'onpagelab' ); ?>
                    </a>
                </h3>
                <p class="feature-card__desc">
                    <?php esc_html_e( 'Mesurez la richesse sémantique, la densité de mots-clés, la couverture du champ lexical et l\'alignement avec l\'intention de recherche.', 'onpagelab' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/outil-seo/analyse-semantique/' ) ); ?>" class="feature-card__link">
                    <?php esc_html_e( 'En savoir plus', 'onpagelab' ); ?> &rarr;
                </a>
            </div>

            <div class="feature-card">
                <div class="feature-card__icon feature-card__icon--green">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <h3 class="feature-card__title">
                    <a href="<?php echo esc_url( home_url( '/outil-seo/maillage-interne/' ) ); ?>">
                        <?php esc_html_e( 'Analyse du maillage interne', 'onpagelab' ); ?>
                    </a>
                </h3>
                <p class="feature-card__desc">
                    <?php esc_html_e( 'Identifiez vos liens internes, détectez les pages orphelines et optimisez le flux PageRank au sein de votre site.', 'onpagelab' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/outil-seo/maillage-interne/' ) ); ?>" class="feature-card__link">
                    <?php esc_html_e( 'En savoir plus', 'onpagelab' ); ?> &rarr;
                </a>
            </div>

            <div class="feature-card">
                <div class="feature-card__icon feature-card__icon--orange">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/><path d="M9 9h6M9 12h6M9 15h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <h3 class="feature-card__title">
                    <a href="<?php echo esc_url( home_url( '/outil-seo/balises-meta/' ) ); ?>">
                        <?php esc_html_e( 'Analyseur de balises meta', 'onpagelab' ); ?>
                    </a>
                </h3>
                <p class="feature-card__desc">
                    <?php esc_html_e( 'Vérifiez instantanément title, meta description, balises Open Graph et Twitter Card pour maximiser votre CTR en SERP.', 'onpagelab' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/outil-seo/balises-meta/' ) ); ?>" class="feature-card__link">
                    <?php esc_html_e( 'En savoir plus', 'onpagelab' ); ?> &rarr;
                </a>
            </div>

            <div class="feature-card">
                <div class="feature-card__icon feature-card__icon--teal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="feature-card__title">
                    <a href="<?php echo esc_url( home_url( '/outil-seo/structure-hn/' ) ); ?>">
                        <?php esc_html_e( 'Densité de mots-clés', 'onpagelab' ); ?>
                    </a>
                </h3>
                <p class="feature-card__desc">
                    <?php esc_html_e( 'Analysez la fréquence et la répartition de vos mots-clés dans l\'ensemble du contenu pour éviter le sur-optimisation ou l\'absence de signal.', 'onpagelab' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/outil-seo/structure-hn/' ) ); ?>" class="feature-card__link">
                    <?php esc_html_e( 'En savoir plus', 'onpagelab' ); ?> &rarr;
                </a>
            </div>

            <div class="feature-card feature-card--highlight">
                <div class="feature-card__icon feature-card__icon--dark">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="feature-card__title">
                    <?php esc_html_e( 'Score SEO global /100', 'onpagelab' ); ?>
                </h3>
                <p class="feature-card__desc">
                    <?php esc_html_e( 'Obtenez un score synthétique sur 100 points qui résume l\'état SEO on-page de votre page, avec des recommandations priorisées.', 'onpagelab' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/outil-seo/' ) ); ?>" class="btn btn--primary btn--sm">
                    <?php esc_html_e( 'Tester l\'outil', 'onpagelab' ); ?>
                </a>
            </div>

        </div>
    </div>
</section>

<section class="how-it-works section section--dark" aria-labelledby="how-heading">
    <div class="container">
        <div class="section-header section-header--light">
            <div class="pill pill--white-outline"><?php esc_html_e( 'Comment ça marche', 'onpagelab' ); ?></div>
            <h2 id="how-heading" class="section-header__title">
                <?php esc_html_e( 'Un audit SEO on-page complet en 3 étapes', 'onpagelab' ); ?>
            </h2>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-card__number">01</div>
                <h3 class="step-card__title"><?php esc_html_e( 'Entrez l\'URL de votre page', 'onpagelab' ); ?></h3>
                <p class="step-card__desc"><?php esc_html_e( 'Collez l\'URL complète de la page que vous souhaitez auditer. Toute URL publiquement accessible est analysable.', 'onpagelab' ); ?></p>
            </div>
            <div class="step-card">
                <div class="step-card__number">02</div>
                <h3 class="step-card__title"><?php esc_html_e( 'L\'analyse se lance automatiquement', 'onpagelab' ); ?></h3>
                <p class="step-card__desc"><?php esc_html_e( 'OnPageLab crawle votre page, extrait le HTML, et analyse plus de 40 critères SEO techniques et sémantiques en temps réel.', 'onpagelab' ); ?></p>
            </div>
            <div class="step-card">
                <div class="step-card__number">03</div>
                <h3 class="step-card__title"><?php esc_html_e( 'Obtenez votre rapport complet', 'onpagelab' ); ?></h3>
                <p class="step-card__desc"><?php esc_html_e( 'Recevez votre score SEO /100 avec une liste priorisée d\'améliorations concrètes à mettre en œuvre immédiatement.', 'onpagelab' ); ?></p>
            </div>
        </div>

        <div class="how-it-works__cta">
            <a href="<?php echo esc_url( home_url( '/outil-seo/' ) ); ?>" class="btn btn--white btn--lg">
                <?php esc_html_e( 'Lancer mon premier audit', 'onpagelab' ); ?>
            </a>
        </div>
    </div>
</section>

<section class="blog-preview section" aria-labelledby="blog-heading">
    <div class="container">
        <div class="section-header">
            <div class="pill pill--blue"><?php esc_html_e( 'Blog SEO', 'onpagelab' ); ?></div>
            <h2 id="blog-heading" class="section-header__title">
                <?php esc_html_e( 'Guides & ressources SEO on-page', 'onpagelab' ); ?>
            </h2>
            <p class="section-header__desc">
                <?php esc_html_e( 'Des guides pratiques, des méthodes et des conseils pour maîtriser le SEO on-page de A à Z.', 'onpagelab' ); ?>
            </p>
        </div>

        <?php
        $blog_posts = new WP_Query( [
            'post_type'      => 'post',
            'posts_per_page' => 3,
            'post_status'    => 'publish',
        ] );

        if ( $blog_posts->have_posts() ) :
        ?>
        <div class="posts-grid posts-grid--3">
            <?php while ( $blog_posts->have_posts() ) : $blog_posts->the_post(); ?>
                <article class="post-card" id="post-<?php the_ID(); ?>">
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
                        <h3 class="post-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <p class="post-card__excerpt"><?php the_excerpt(); ?></p>
                        <div class="post-card__footer">
                            <?php echo opl_post_date(); ?>
                            <a href="<?php the_permalink(); ?>" class="post-card__link" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
                                <?php esc_html_e( 'Lire l\'article', 'onpagelab' ); ?> &rarr;
                            </a>
                        </div>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php endif; ?>

        <div class="section-footer">
            <a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) ); ?>" class="btn btn--outline btn--md">
                <?php esc_html_e( 'Tous les articles SEO', 'onpagelab' ); ?>
            </a>
        </div>
    </div>
</section>

<section class="faq-section section section--light" aria-labelledby="faq-heading">
    <div class="container container--narrow">
        <div class="section-header">
            <h2 id="faq-heading" class="section-header__title">
                <?php esc_html_e( 'Questions fréquentes sur l\'analyse SEO on-page', 'onpagelab' ); ?>
            </h2>
        </div>

        <div class="faq-list" itemscope itemtype="https://schema.org/FAQPage">

            <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                <button class="faq-item__question" aria-expanded="false" itemprop="name">
                    <?php esc_html_e( 'Qu\'est-ce que le SEO on-page ?', 'onpagelab' ); ?>
                    <svg class="faq-item__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div itemprop="text">
                        <p><?php esc_html_e( 'Le SEO on-page désigne l\'ensemble des optimisations réalisées directement sur une page web pour améliorer son positionnement dans les moteurs de recherche. Il englobe l\'optimisation des balises meta (title, description), la structure des titres (H1, H2, H3), la qualité du contenu, la densité de mots-clés, le maillage interne et les performances techniques de la page.', 'onpagelab' ); ?></p>
                    </div>
                </div>
            </div>

            <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                <button class="faq-item__question" aria-expanded="false" itemprop="name">
                    <?php esc_html_e( 'Quelle est la différence entre SEO on-page et off-page ?', 'onpagelab' ); ?>
                    <svg class="faq-item__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div itemprop="text">
                        <p><?php esc_html_e( 'Le SEO on-page concerne les éléments que vous contrôlez directement sur votre site (contenu, balises, structure technique). Le SEO off-page regroupe les facteurs externes comme les backlinks, les mentions de marque et les signaux sociaux. OnPageLab se concentre sur l\'optimisation on-page, qui est la base indispensable d\'une stratégie SEO solide.', 'onpagelab' ); ?></p>
                    </div>
                </div>
            </div>

            <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                <button class="faq-item__question" aria-expanded="false" itemprop="name">
                    <?php esc_html_e( 'OnPageLab est-il vraiment gratuit ?', 'onpagelab' ); ?>
                    <svg class="faq-item__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="faq-item__answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div itemprop="text">
                        <p><?php esc_html_e( 'Oui, OnPageLab propose une analyse SEO on-page complète entièrement gratuite, sans inscription requise. Vous pouvez analyser autant de pages que vous le souhaitez et obtenir un rapport détaillé avec score, problèmes détectés et recommandations priorisées.', 'onpagelab' ); ?></p>
                    </div>
                </div>
            </div>

        </div>

        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "<?php esc_html_e( 'Qu\'est-ce que le SEO on-page ?', 'onpagelab' ); ?>",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "<?php esc_html_e( 'Le SEO on-page désigne l\'ensemble des optimisations réalisées directement sur une page web pour améliorer son positionnement dans les moteurs de recherche.', 'onpagelab' ); ?>"
                    }
                }
            ]
        }
        </script>

    </div>
</section>

<?php get_footer();
