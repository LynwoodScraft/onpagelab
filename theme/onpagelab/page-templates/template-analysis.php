<?php
/**
 * Template Name: Résultats d'analyse SEO
 * Template Post Type: page
 *
 * Page dédiée à l'affichage des résultats d'analyse.
 * Reçoit ?url=...&keyword=... en paramètres GET et déclenche l'AJAX automatiquement.
 *
 * @package OnPageLab
 */
get_header(); ?>

<section class="analysis-bar section--dark" id="analysis-app">
    <div class="container">
        <form class="analysis-bar__form" id="analysis-form" aria-label="<?php esc_attr_e( 'Relancer une analyse', 'onpagelab' ); ?>">
            <div class="analysis-bar__inputs">
                <div class="tool-form__input-wrap">
                    <span class="tool-form__icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </span>
                    <input
                        type="url"
                        id="analysis-url"
                        name="url"
                        class="tool-form__input analysis-bar__url"
                        placeholder="https://example.com/page/"
                        required
                        autocomplete="url"
                    >
                </div>
                <div class="tool-form__input-wrap">
                    <span class="tool-form__icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </span>
                    <input
                        type="text"
                        id="analysis-keyword"
                        name="keyword"
                        class="tool-form__input analysis-bar__keyword"
                        placeholder="<?php esc_attr_e( 'Mot-clé (optionnel)', 'onpagelab' ); ?>"
                        autocomplete="off"
                    >
                </div>
            </div>
            <button type="submit" class="btn btn--primary" id="analysis-submit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                <?php esc_html_e( 'Analyser', 'onpagelab' ); ?>
            </button>
        </form>
    </div>
</section>

<div id="analysis-empty" class="analysis-empty" hidden>
    <div class="container container--narrow">
        <div class="analysis-empty__inner">
            <span class="analysis-empty__icon">🔍</span>
            <h2><?php esc_html_e( 'Entrez une URL pour lancer l\'analyse', 'onpagelab' ); ?></h2>
            <p><?php esc_html_e( 'Renseignez l\'URL de la page et, si vous le souhaitez, votre mot-clé cible dans le formulaire ci-dessus.', 'onpagelab' ); ?></p>
        </div>
    </div>
</div>

<div class="tool-loading" id="tool-loading" hidden aria-live="polite">
    <div class="container">
        <div class="tool-loading__inner">
            <div class="tool-loading__spinner" aria-hidden="true">
                <svg class="spin" width="48" height="48" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="#E2E8F0" stroke-width="3"/>
                    <circle cx="12" cy="12" r="10" stroke="#2563EB" stroke-width="3" stroke-dasharray="31.4 31.4" stroke-linecap="round"/>
                </svg>
            </div>
            <h2 class="tool-loading__title"><?php esc_html_e( 'Analyse en cours…', 'onpagelab' ); ?></h2>
            <div class="tool-loading__steps" id="loading-steps">
                <div class="loading-step loading-step--active" data-step="fetch"><?php esc_html_e( '⏳ Récupération de la page…', 'onpagelab' ); ?></div>
                <div class="loading-step" data-step="parse"><?php esc_html_e( '🔍 Analyse des balises HTML…', 'onpagelab' ); ?></div>
                <div class="loading-step" data-step="content"><?php esc_html_e( '📝 Analyse du contenu…', 'onpagelab' ); ?></div>
                <div class="loading-step" data-step="links"><?php esc_html_e( '🔗 Vérification du maillage…', 'onpagelab' ); ?></div>
                <div class="loading-step" data-step="score"><?php esc_html_e( '📊 Calcul du score SEO…', 'onpagelab' ); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="tool-results" id="tool-results" hidden>
    <div class="container">

        <div class="results-header">
            <div class="results-header__url" id="result-url"></div>
            <div class="results-header__actions">
                <button class="btn btn--outline btn--sm" id="btn-export" aria-label="<?php esc_attr_e( 'Exporter le rapport', 'onpagelab' ); ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <?php esc_html_e( 'Exporter PDF', 'onpagelab' ); ?>
                </button>
            </div>
        </div>

        <div class="results-score-band">
            <div class="score-circle" id="score-global">
                <svg viewBox="0 0 100 100" role="img" aria-label="<?php esc_attr_e( 'Score SEO global', 'onpagelab' ); ?>">
                    <circle cx="50" cy="50" r="45" fill="none" stroke="#E2E8F0" stroke-width="8"/>
                    <circle cx="50" cy="50" r="45" fill="none" stroke="#10B981" stroke-width="8"
                            stroke-dasharray="0 283" stroke-linecap="round"
                            transform="rotate(-90 50 50)" id="score-arc"/>
                </svg>
                <div class="score-circle__value">
                    <span id="score-value">—</span>
                    <small>/100</small>
                </div>
            </div>
            <div class="score-sub-grid" id="score-sub-grid"></div>
        </div>

        <div class="results-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Sections du rapport', 'onpagelab' ); ?>">
            <button role="tab" class="results-tab results-tab--active" data-tab="issues" aria-selected="true" id="tab-issues">
                <?php esc_html_e( 'Problèmes', 'onpagelab' ); ?>
                <span class="badge badge--error" id="issues-count">0</span>
            </button>
            <button role="tab" class="results-tab" data-tab="technical" aria-selected="false" id="tab-technical">
                <?php esc_html_e( 'Technique', 'onpagelab' ); ?>
            </button>
            <button role="tab" class="results-tab" data-tab="semantic" aria-selected="false" id="tab-semantic">
                <?php esc_html_e( 'Sémantique', 'onpagelab' ); ?>
            </button>
            <button role="tab" class="results-tab" data-tab="links" aria-selected="false" id="tab-links">
                <?php esc_html_e( 'Maillage', 'onpagelab' ); ?>
            </button>
            <button role="tab" class="results-tab results-tab--hn" data-tab="hn" aria-selected="false" id="tab-hn">
                <?php esc_html_e( 'Structure Hn', 'onpagelab' ); ?>
            </button>
        </div>

        <div class="results-panel" id="panel-issues" role="tabpanel" aria-labelledby="tab-issues">
            <div class="issues-list" id="issues-list"></div>
        </div>
        <div class="results-panel" id="panel-technical" role="tabpanel" aria-labelledby="tab-technical" hidden>
            <div class="technical-grid" id="technical-grid"></div>
        </div>
        <div class="results-panel" id="panel-semantic" role="tabpanel" aria-labelledby="tab-semantic" hidden>
            <div class="semantic-content" id="semantic-content"></div>
        </div>
        <div class="results-panel" id="panel-links" role="tabpanel" aria-labelledby="tab-links" hidden>
            <div class="links-content" id="links-content"></div>
        </div>
        <div class="results-panel" id="panel-hn" role="tabpanel" aria-labelledby="tab-hn" hidden>
            <div class="hn-content" id="hn-content"></div>
        </div>

    </div>
</div>

<div class="tool-error" id="tool-error" hidden role="alert">
    <div class="container container--narrow">
        <div class="tool-error__inner">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="tool-error__icon" aria-hidden="true">
                <circle cx="12" cy="12" r="10" stroke="#EF4444" stroke-width="2"/>
                <path d="M12 8v4M12 16h.01" stroke="#EF4444" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <h2 class="tool-error__title"><?php esc_html_e( 'Analyse impossible', 'onpagelab' ); ?></h2>
            <p class="tool-error__desc" id="error-message"></p>
            <button class="btn btn--primary" id="btn-retry"><?php esc_html_e( 'Réessayer', 'onpagelab' ); ?></button>
        </div>
    </div>
</div>

<?php get_footer();
