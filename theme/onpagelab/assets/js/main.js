/**
 * OnPageLab — Main JavaScript
 * @version 1.0.0
 */

(function () {
  'use strict';

  // ============================================================
  // UTILITIES
  // ============================================================
  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];

  // ============================================================
  // STICKY HEADER
  // ============================================================
  const header = $('#site-header');
  if (header) {
    const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 20);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // ============================================================
  // MOBILE MENU
  // ============================================================
  const hamburger = $('#mobile-menu-toggle');
  const nav       = $('#primary-nav');
  if (hamburger && nav) {
    hamburger.addEventListener('click', () => {
      const isOpen = hamburger.getAttribute('aria-expanded') === 'true';
      hamburger.setAttribute('aria-expanded', String(!isOpen));
      nav.classList.toggle('is-open', !isOpen);
      hamburger.setAttribute('aria-label', isOpen ? 'Ouvrir le menu' : 'Fermer le menu');
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!header.contains(e.target)) {
        hamburger.setAttribute('aria-expanded', 'false');
        nav.classList.remove('is-open');
      }
    });
  }

  // ============================================================
  // DROPDOWN NAVIGATION — Toggle mobile + accessibilité clavier
  // ============================================================
  $$('.nav-menu--primary .menu-item-has-children').forEach(item => {
    // Injecter un bouton toggle (visible uniquement sur mobile via CSS)
    const parentLink = item.querySelector(':scope > a');
    const toggleBtn  = document.createElement('button');
    toggleBtn.className = 'dropdown-toggle';
    toggleBtn.setAttribute('aria-expanded', 'false');
    toggleBtn.setAttribute('aria-label', 'Ouvrir le sous-menu');
    toggleBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    parentLink.insertAdjacentElement('afterend', toggleBtn);

    // Clic sur le bouton toggle (mobile)
    toggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = item.classList.toggle('is-open');
      toggleBtn.setAttribute('aria-expanded', String(isOpen));
      toggleBtn.setAttribute('aria-label', isOpen ? 'Fermer le sous-menu' : 'Ouvrir le sous-menu');
    });

    // Fermer le dropdown desktop au clic en dehors
    document.addEventListener('click', (e) => {
      if (!item.contains(e.target)) {
        item.classList.remove('is-open');
        toggleBtn.setAttribute('aria-expanded', 'false');
      }
    });

    // Fermer au appui sur Escape
    item.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        item.classList.remove('is-open');
        toggleBtn.setAttribute('aria-expanded', 'false');
        parentLink.focus();
      }
    });
  });

  // ============================================================
  // ANNOUNCEMENT BAR CLOSE
  // ============================================================
  const announcementClose = $('.announcement-bar__close');
  if (announcementClose) {
    announcementClose.addEventListener('click', () => {
      announcementClose.closest('.announcement-bar').remove();
    });
  }

  // ============================================================
  // FAQ ACCORDION
  // ============================================================
  $$('.faq-item__question').forEach(btn => {
    btn.addEventListener('click', () => {
      const answer  = btn.nextElementSibling;
      const isOpen  = btn.getAttribute('aria-expanded') === 'true';

      // Close all
      $$('.faq-item__question').forEach(b => {
        b.setAttribute('aria-expanded', 'false');
        b.nextElementSibling.classList.remove('is-open');
      });

      // Toggle clicked
      if (!isOpen) {
        btn.setAttribute('aria-expanded', 'true');
        answer.classList.add('is-open');
      }
    });
  });

  // ============================================================
  // TABLE OF CONTENTS — Auto-generate from article headings
  // ============================================================
  const tocList = $('#toc-list');
  const article  = $('#article-content');
  if (tocList && article) {
    const headings = $$('h2, h3', article);
    if (headings.length > 2) {
      const ul = document.createElement('ul');
      ul.className = 'toc-list';
      headings.forEach((h, i) => {
        if (!h.id) h.id = `heading-${i}`;
        const li   = document.createElement('li');
        li.className = h.tagName === 'H3' ? 'h3-level' : '';
        li.innerHTML = `<a href="#${h.id}">${h.textContent}</a>`;
        ul.appendChild(li);
      });
      tocList.appendChild(ul);
    } else {
      $('#article-toc')?.setAttribute('hidden', 'true');
    }

    // Highlight active ToC item on scroll
    const tocLinks = $$('.toc-list a', tocList);
    if (tocLinks.length && 'IntersectionObserver' in window) {
      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            tocLinks.forEach(l => l.classList.remove('active'));
            const active = tocList.querySelector(`a[href="#${entry.target.id}"]`);
            if (active) active.classList.add('active');
          }
        });
      }, { rootMargin: '-20% 0px -70% 0px' });
      headings.forEach(h => observer.observe(h));
    }
  }

  // ============================================================
  // RESULTS TABS
  // ============================================================
  const tabs = $$('.results-tab');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const target = tab.dataset.tab;
      tabs.forEach(t => {
        t.classList.remove('results-tab--active');
        t.setAttribute('aria-selected', 'false');
      });
      tab.classList.add('results-tab--active');
      tab.setAttribute('aria-selected', 'true');

      $$('.results-panel').forEach(p => p.hidden = true);
      const panel = $(`#panel-${target}`);
      if (panel) panel.hidden = false;
    });
  });

  // ============================================================
  // HELPER — Redirect to /analysis/
  // ============================================================
  function redirectToAnalysis(url, keyword) {
    const siteUrl = (window.OPL && window.OPL.siteUrl)
      ? window.OPL.siteUrl.replace(/\/$/, '')
      : window.location.origin;
    let dest = siteUrl + '/analysis/?url=' + encodeURIComponent(url);
    if (keyword) dest += '&keyword=' + encodeURIComponent(keyword);
    window.location.href = dest;
  }

  // ============================================================
  // HERO ANALYZER FORM (homepage) → redirect to /analysis/
  // ============================================================
  const heroForm  = $('#analyzer-form');
  const heroInput = $('#analyzer-url');
  if (heroForm && heroInput) {
    heroForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const url = heroInput.value.trim();
      if (!url) return;
      redirectToAnalysis(url, '');
    });
  }

  // ============================================================
  // TOOL PAGE FAQ ACCORDION (/outil-seo/)
  // ============================================================
  $$('.tool-faq__question').forEach(btn => {
    btn.addEventListener('click', () => {
      const item   = btn.closest('.tool-faq__item');
      const answer = btn.nextElementSibling;
      const isOpen = btn.getAttribute('aria-expanded') === 'true';

      // Close all items
      $$('.tool-faq__item').forEach(el => {
        el.classList.remove('is-open');
        el.querySelector('.tool-faq__question').setAttribute('aria-expanded', 'false');
        const a = el.querySelector('.tool-faq__answer');
        if (a) a.setAttribute('hidden', '');
      });

      // Open clicked item if it was closed
      if (!isOpen) {
        item.classList.add('is-open');
        btn.setAttribute('aria-expanded', 'true');
        answer.removeAttribute('hidden');
      }
    });
  });

  // ============================================================
  // TOOL FORM (/outil-seo/) → redirect to /analysis/
  // ============================================================
  const toolForm = $('#tool-form');
  if (toolForm) {
    toolForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const url     = $('#tool-url')?.value.trim();
      const keyword = $('#tool-keyword')?.value.trim() || '';
      if (!url) return;
      redirectToAnalysis(url, keyword);
    });
  }

  // ============================================================
  // ANALYSIS PAGE (/analysis/) — auto-trigger + re-analysis form
  // ============================================================
  const analysisApp  = $('#analysis-app');
  const analysisForm = $('#analysis-form');
  const toolLoading  = $('#tool-loading');
  const toolResults  = $('#tool-results');
  const toolError    = $('#tool-error');
  const analysisEmpty = $('#analysis-empty');

  if (analysisApp) {

    const params     = new URLSearchParams(window.location.search);
    const initUrl    = params.get('url')     || '';
    const initKw     = params.get('keyword') || '';

    // Pre-fill compact form
    const urlInput = $('#analysis-url');
    const kwInput  = $('#analysis-keyword');
    if (urlInput && initUrl) urlInput.value = initUrl;
    if (kwInput  && initKw)  kwInput.value  = initKw;

    // Compact form submit → re-redirect
    if (analysisForm) {
      analysisForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const url = urlInput?.value.trim();
        const kw  = kwInput?.value.trim() || '';
        if (!url) return;
        redirectToAnalysis(url, kw);
      });
    }

    // Retry button
    $('#btn-retry')?.addEventListener('click', () => {
      if (toolError)   toolError.hidden = true;
      if (analysisEmpty) analysisEmpty.hidden = false;
    });

    // Auto-trigger if URL param present
    if (initUrl) {
      if (analysisEmpty) analysisEmpty.hidden = true;
      if (toolLoading)   toolLoading.hidden   = false;
      if (toolResults)   toolResults.hidden   = true;
      if (toolError)     toolError.hidden     = true;

      animateLoadingSteps();

      analyzeUrl(initUrl, initKw)
        .then(data => {
          renderResults(data);
          if (toolLoading) toolLoading.hidden = true;
          if (toolResults) { toolResults.hidden = false; window.scrollTo({ top: 0 }); }
        })
        .catch(err => {
          if (toolLoading) toolLoading.hidden = true;
          showError(err.message || 'Une erreur est survenue.');
        });
    } else {
      // No params → show empty state
      if (analysisEmpty) analysisEmpty.hidden = false;
    }
  }

  // ============================================================
  // EXPORT PDF — standalone listener (indépendant du bloc analysisApp)
  // ============================================================
  const btnExport = document.getElementById('btn-export');
  if (btnExport) {
    btnExport.addEventListener('click', function () {
      // Afficher tous les panneaux pour l'impression
      var panels = document.querySelectorAll('.results-panel');
      panels.forEach(function (p) { p.removeAttribute('hidden'); });
      document.body.classList.add('is-printing');

      // Ouvrir la boîte d'impression
      window.print();

      // Restaurer l'état après fermeture de la boîte
      function restorePanels() {
        document.body.classList.remove('is-printing');
        // Tout masquer
        panels.forEach(function (p) { p.setAttribute('hidden', ''); });
        // Réafficher l'onglet actif
        var activeTab = document.querySelector('.results-tab--active');
        if (activeTab) {
          var panel = document.getElementById('panel-' + activeTab.dataset.tab);
          if (panel) panel.removeAttribute('hidden');
        }
        window.removeEventListener('afterprint', restorePanels);
      }
      window.addEventListener('afterprint', restorePanels);
    });
  }

  // ============================================================
  // LOADING STEP ANIMATION
  // ============================================================
  function animateLoadingSteps() {
    const steps = $$('.loading-step');
    let i = 0;
    steps.forEach(s => s.classList.remove('loading-step--active', 'loading-step--done'));
    steps[0]?.classList.add('loading-step--active');

    const interval = setInterval(() => {
      if (i < steps.length) {
        steps[i]?.classList.remove('loading-step--active');
        steps[i]?.classList.add('loading-step--done');
        i++;
        steps[i]?.classList.add('loading-step--active');
      } else {
        clearInterval(interval);
      }
    }, 1200);
  }

  // ============================================================
  // AJAX ANALYSIS
  // ============================================================
  async function analyzeUrl(url, keyword = '') {
    // Use WordPress AJAX endpoint
    const formData = new FormData();
    formData.append('action', 'opl_analyze');
    formData.append('url', url);
    formData.append('keyword', keyword);
    formData.append('nonce', window.OPL?.nonce || '');

    const res = await fetch(window.OPL?.ajaxUrl || '/wp-admin/admin-ajax.php', {
      method: 'POST',
      body: formData,
      credentials: 'same-origin',
    });

    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const json = await res.json();
    if (!json.success) throw new Error(json.data?.message || 'Analyse échouée');
    return json.data;
  }

  // ============================================================
  // RENDER RESULTS
  // ============================================================
  function renderResults(data) {
    // URL
    const urlEl = $('#result-url');
    if (urlEl) urlEl.textContent = data.url || '';

    // Global score
    const scoreVal = data.score || 0;
    const scoreEl  = $('#score-value');
    if (scoreEl) {
      animateCounter(scoreEl, 0, scoreVal, 1000);
    }

    // Score arc (SVG)
    const arc = $('#score-arc');
    if (arc) {
      const circumference = 2 * Math.PI * 45;
      const dash = (scoreVal / 100) * circumference;
      arc.setAttribute('stroke-dasharray', `${dash} ${circumference - dash}`);
      arc.setAttribute('stroke', scoreVal >= 80 ? '#10B981' : scoreVal >= 50 ? '#F59E0B' : '#EF4444');
    }

    // Sub scores
    const subGrid = $('#score-sub-grid');
    if (subGrid && data.subscores) {
      subGrid.innerHTML = data.subscores.map(s => `
        <div class="score-sub-item ${scoreClass(s.score)}">
          <div class="score-sub-item__label">${escHtml(s.label)}</div>
          <div class="score-sub-item__value">${s.score}<small>/100</small></div>
        </div>
      `).join('');
    }

    // Issues tab
    const issuesList  = $('#issues-list');
    const issuesCount = $('#issues-count');
    if (issuesList && data.issues) {
      const errors    = data.issues.filter(i => i.type === 'error');
      const warnings  = data.issues.filter(i => i.type === 'warning');
      const successes = data.issues.filter(i => i.type === 'success');
      if (issuesCount) issuesCount.textContent = errors.length + warnings.length;

      issuesList.innerHTML = [
        ...errors.map(i => renderIssue(i, 'error')),
        ...warnings.map(i => renderIssue(i, 'warning')),
        ...successes.map(i => renderIssue(i, 'success')),
      ].join('') || '<p>Aucun problème détecté.</p>';
    }

    // Technical tab
    const techGrid = $('#technical-grid');
    if (techGrid && data.technical) {
      techGrid.innerHTML = renderTechnicalTab(data.technical);
    }

    // Semantic tab
    const semContent = $('#semantic-content');
    if (semContent) {
      if (data.semantic && data.semantic.length) {
        semContent.innerHTML = renderSemanticTab(data.semantic);
      } else {
        semContent.innerHTML = `
          <div class="empty-tab">
            <p class="empty-tab__icon">🔑</p>
            <p class="empty-tab__text">Ajoutez un <strong>mot-clé cible</strong> pour activer l'analyse sémantique.</p>
          </div>`;
      }
    }

    // Links tab
    const linksContent = $('#links-content');
    if (linksContent && data.links) {
      linksContent.innerHTML = renderLinksTab(data.links);
    }

    // Hn Structure tab
    const hnContent = $('#hn-content');
    if (hnContent) {
      hnContent.innerHTML = renderHnTab(data.hn);
    }
  }

  // ============================================================
  // TECHNICAL TAB
  // ============================================================
  function renderTechnicalTab(items) {
    return `<div class="tech-grid">${items.map(item => {
      const statusIcon = statusBadge(item.status);
      const pxInfo = item.pixel_width
        ? `<span class="tech-card__px">${item.pixel_width} px <small>(optimal : ${escHtml(item.optimal || '')})</small></span>`
        : '';
      const detailInfo = item.detail ? `<span class="tech-card__detail">${escHtml(item.detail)}</span>` : '';
      const tagsInfo = item.present
        ? `<div class="tech-card__tags">
            ${item.present.map(t => `<span class="badge badge--success">${escHtml(t)}</span>`).join('')}
            ${(item.missing || []).map(t => `<span class="badge badge--error">${escHtml(t)}</span>`).join('')}
           </div>`
        : '';

      return `
        <div class="tech-card tech-card--${item.status}">
          <div class="tech-card__header">
            <span class="tech-card__label">${escHtml(item.label)}</span>
            ${statusIcon}
          </div>
          <div class="tech-card__body">
            <code class="tech-card__value">${escHtml(truncate(item.value || '', 80))}</code>
            ${pxInfo}${detailInfo}${tagsInfo}
          </div>
        </div>`;
    }).join('')}</div>`;
  }

  // ============================================================
  // SEMANTIC TAB
  // ============================================================
  function renderSemanticTab(items) {
    return `<div class="semantic-grid">${items.map(item => {
      const statusIcon = statusBadge(item.status);

      // Density bar
      let barHtml = '';
      if (item.density !== undefined) {
        const pct = Math.min(item.density * 25, 100); // visual: 4% = 100% bar
        const barColor = item.status === 'success' ? '#10B981' : item.status === 'warning' ? '#F59E0B' : '#EF4444';
        barHtml = `
          <div class="density-bar">
            <div class="density-bar__fill" style="width:${pct}%;background:${barColor}"></div>
          </div>
          <span class="density-bar__label">${item.density}% <small>(${item.occurrences} occ. / ${item.word_count} mots)</small></span>
          <span class="density-bar__optimal">Optimal : ${escHtml(item.optimal || '0,5–2 %')}</span>`;
      }

      // Word count
      let wcHtml = '';
      if (item.word_count !== undefined && item.id === 'content_length') {
        wcHtml = `<span class="semantic-card__wc">${item.word_count} mots</span>`;
      }

      // Simple value
      let valHtml = '';
      if (item.value !== undefined && item.id !== 'content_length') {
        valHtml = `<span class="semantic-card__val">${escHtml(item.value)}</span>`;
      }

      return `
        <div class="semantic-card semantic-card--${item.status}">
          <div class="semantic-card__header">
            <span class="semantic-card__label">${escHtml(item.label)}</span>
            ${statusIcon}
          </div>
          <div class="semantic-card__body">
            ${valHtml}${barHtml}${wcHtml}
          </div>
        </div>`;
    }).join('')}</div>`;
  }

  // ============================================================
  // LINKS TAB
  // ============================================================
  function renderLinksTab(sections) {
    return sections.map(section => {
      const statusIcon = statusBadge(section.status);

      // Summary counters
      let counters = '';
      if (section.dofollow !== undefined) {
        counters = `
          <div class="links-counters">
            <span class="links-counter"><strong>${section.total}</strong> total</span>
            <span class="links-counter links-counter--good">${section.dofollow} dofollow</span>
            <span class="links-counter links-counter--muted">${section.nofollow} nofollow</span>
          </div>`;
      }

      // Images specific
      if (section.id === 'images') {
        counters = `
          <div class="links-counters">
            <span class="links-counter"><strong>${section.total}</strong> image(s)</span>
            <span class="links-counter links-counter--good">${section.with_alt} avec alt</span>
            <span class="links-counter links-counter--warn">${section.without_alt} sans alt</span>
          </div>`;
      }

      // Expandable details
      let detailsHtml = '';
      if (section.details && section.details.length) {
        const rows = section.details.slice(0, 20).map(l => `
          <tr>
            <td class="link-detail__anchor">${escHtml(l.anchor || '(vide)')}</td>
            <td class="link-detail__url"><a href="${escHtml(l.url)}" target="_blank" rel="noopener">${escHtml(truncate(l.url, 60))}</a></td>
            <td class="link-detail__rel"><span class="badge badge--${l.rel === 'dofollow' ? 'success' : 'warning'}">${l.rel}</span></td>
          </tr>`).join('');
        detailsHtml = `
          <details class="links-details">
            <summary>Voir les ${Math.min(section.details.length, 20)} premiers liens</summary>
            <table class="links-table">
              <thead><tr><th>Ancre</th><th>URL</th><th>Rel</th></tr></thead>
              <tbody>${rows}</tbody>
            </table>
          </details>`;
      }

      return `
        <div class="links-section links-section--${section.status}">
          <div class="links-section__header">
            <h4 class="links-section__title">${escHtml(section.label)}</h4>
            ${statusIcon}
          </div>
          ${counters}${detailsHtml}
        </div>`;
    }).join('');
  }

  // ============================================================
  // HN STRUCTURE TAB
  // ============================================================
  function renderHnTab(hn) {
    if (!hn) {
      return `<div class="empty-tab"><p class="empty-tab__icon">📐</p><p class="empty-tab__text">Données de structure Hn indisponibles.</p></div>`;
    }

    // ── Section 1 : résumé score + compteurs par niveau ──────
    const score = hn.score || 0;
    const counts = hn.counts || {};
    const levelBadges = ['H1','H2','H3','H4','H5','H6']
      .filter(tag => counts[tag] > 0)
      .map(tag => `<span class="hn-level-badge hn-level-badge--${tag.toLowerCase()}">${tag} <strong>${counts[tag]}</strong></span>`)
      .join('');

    const scoreColor = score >= 80 ? '#10B981' : score >= 50 ? '#F59E0B' : '#EF4444';
    const scoreLabel = score >= 80 ? 'Bonne structure' : score >= 50 ? 'À améliorer' : 'Structure faible';

    const summaryHtml = `
      <div class="hn-summary">
        <div class="hn-score-circle">
          <svg viewBox="0 0 60 60" width="64" height="64">
            <circle cx="30" cy="30" r="26" fill="none" stroke="#E2E8F0" stroke-width="5"/>
            <circle cx="30" cy="30" r="26" fill="none" stroke="${scoreColor}" stroke-width="5"
              stroke-dasharray="${(score/100)*163.4} ${163.4 - (score/100)*163.4}"
              stroke-linecap="round" transform="rotate(-90 30 30)"/>
          </svg>
          <div class="hn-score-circle__val">
            <span>${score}</span>
            <small>/100</small>
          </div>
        </div>
        <div class="hn-summary__info">
          <p class="hn-summary__label" style="color:${scoreColor}">${scoreLabel}</p>
          <div class="hn-levels">${levelBadges || '<span class="muted">Aucun titre</span>'}</div>
        </div>
      </div>`;

    // ── Section 2 : Arborescence visuelle ────────────────────
    const tree = hn.tree || [];
    const treeHtml = tree.length > 0
      ? `<div class="hn-tree-wrap">
          <h3 class="hn-section-title">Arborescence des titres</h3>
          <div class="hn-tree">${tree.map(h => renderHnNode(h)).join('')}</div>
        </div>`
      : `<div class="hn-tree-empty">Aucun titre détecté sur cette page.</div>`;

    // ── Section 3 : Critères détaillés ────────────────────────
    const criteria = hn.criteria || [];
    const criteriaHtml = `
      <div class="hn-criteria">
        <h3 class="hn-section-title">Analyse par critère</h3>
        ${criteria.map(c => renderHnCriterion(c)).join('')}
      </div>`;

    return summaryHtml + treeHtml + criteriaHtml;
  }

  /** Render one heading node in the visual tree */
  function renderHnNode(h) {
    const indent = (h.level - 1) * 20;
    const statusMap = { success: '#10B981', warning: '#F59E0B', error: '#EF4444', info: '#6B7280' };
    const tagStatus = h.text === '' ? 'error' : (h.length > 70 ? 'warning' : (h.words < 3 ? 'info' : 'success'));
    const dotColor  = statusMap[tagStatus] || '#6B7280';
    const label     = h.text !== '' ? escHtml(truncate(h.text, 90)) : '<em class="hn-empty-label">(vide)</em>';

    return `
      <div class="hn-node hn-node--${h.tag.toLowerCase()}" style="padding-left:${indent}px">
        <span class="hn-node__dot" style="background:${dotColor}" aria-hidden="true"></span>
        <span class="hn-node__tag">${h.tag}</span>
        <span class="hn-node__text">${label}</span>
        <span class="hn-node__meta">${h.length} car. · ${h.words} mots</span>
      </div>`;
  }

  /** Render one criterion card (issues + optional extras) */
  function renderHnCriterion(c) {
    const scoreColor = c.score >= 80 ? '#10B981' : c.score >= 50 ? '#F59E0B' : '#EF4444';
    const iconMap = { success: '✅', warning: '⚠️', error: '❌', info: 'ℹ️' };

    const issuesHtml = (c.issues || []).map(iss => `
      <div class="hn-issue hn-issue--${iss.type}">
        <span class="hn-issue__icon" aria-hidden="true">${iconMap[iss.type] || 'ℹ️'}</span>
        <span class="hn-issue__msg">${escHtml(iss.msg)}</span>
      </div>`).join('');

    // Coherence extras: show H1 / title / meta
    let extraHtml = '';
    if (c.id === 'hn_coherence' && (c.h1 || c.title || c.meta)) {
      extraHtml = `
        <div class="hn-coherence-table">
          ${c.h1    ? `<div class="hn-coh-row"><span class="hn-coh-tag">H1</span><span class="hn-coh-val">${escHtml(c.h1)}</span></div>` : ''}
          ${c.title ? `<div class="hn-coh-row"><span class="hn-coh-tag">Title</span><span class="hn-coh-val">${escHtml(c.title)}</span></div>` : ''}
          ${c.meta  ? `<div class="hn-coh-row"><span class="hn-coh-tag">Meta</span><span class="hn-coh-val">${escHtml(c.meta)}</span></div>` : ''}
        </div>`;
    }

    // Length details: per-heading table
    let detailHtml = '';
    if (c.id === 'hn_length' && c.detail && c.detail.length > 0) {
      const rows = c.detail.map(h => `
        <tr class="hn-len-row hn-len-row--${h.status}">
          <td class="hn-len__tag">${h.tag}</td>
          <td class="hn-len__text">${escHtml(truncate(h.text || '(vide)', 60))}</td>
          <td class="hn-len__chars">${h.length} car.</td>
          <td class="hn-len__words">${h.words} mots</td>
          <td class="hn-len__badge"><span class="badge badge--${h.status}">${h.status === 'success' ? 'OK' : h.status === 'warning' ? 'Long' : h.status === 'error' ? 'Vide' : 'Court'}</span></td>
        </tr>`).join('');
      detailHtml = `
        <details class="hn-length-details">
          <summary>Voir le détail par titre (${c.detail.length})</summary>
          <table class="hn-length-table">
            <thead><tr><th>Balise</th><th>Texte</th><th>Longueur</th><th>Mots</th><th>Statut</th></tr></thead>
            <tbody>${rows}</tbody>
          </table>
        </details>`;
    }

    // Score bar
    const barPct = Math.round(c.score);
    const scoreBarHtml = `
      <div class="hn-criterion__score-bar">
        <div class="hn-criterion__score-fill" style="width:${barPct}%;background:${scoreColor}"></div>
      </div>
      <span class="hn-criterion__score-val" style="color:${scoreColor}">${c.score}/100</span>`;

    return `
      <div class="hn-criterion hn-criterion--${c.status}">
        <div class="hn-criterion__header">
          <span class="hn-criterion__label">${escHtml(c.label)}</span>
          ${scoreBarHtml}
        </div>
        <div class="hn-criterion__body">
          ${issuesHtml}
          ${extraHtml}
          ${detailHtml}
        </div>
      </div>`;
  }

  // ============================================================
  // SHARED UI HELPERS
  // ============================================================
  function statusBadge(status) {
    const labels = { success: 'OK', warning: 'Attention', error: 'Critique', info: 'Info' };
    return `<span class="badge badge--${status}">${labels[status] || status}</span>`;
  }

  function truncate(str, max) {
    return str.length > max ? str.slice(0, max) + '…' : str;
  }

  function renderIssue(issue, type) {
    const icons = {
      error:   '<svg width="18" height="18" viewBox="0 0 24 24" fill="#EF4444"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>',
      warning: '<svg width="18" height="18" viewBox="0 0 24 24" fill="#F59E0B"><path d="M12 2L1 21h22L12 2zm0 3.5L20.5 19h-17L12 5.5zm-1 5v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>',
      success: '<svg width="18" height="18" viewBox="0 0 24 24" fill="#10B981"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>',
    };
    return `
      <div class="issue-item issue-item--${type}" role="listitem">
        <span class="issue-item__icon" aria-hidden="true">${icons[type]}</span>
        <div class="issue-item__content">
          <h4>${escHtml(issue.title)}</h4>
          <p>${escHtml(issue.description)}</p>
        </div>
      </div>
    `;
  }

  function scoreClass(score) {
    if (score >= 80) return 'score--good';
    if (score >= 50) return 'score--average';
    return 'score--poor';
  }

  function showError(msg) {
    if (!toolError) return;
    const msgEl = $('#error-message');
    if (msgEl) msgEl.textContent = msg;
    toolError.hidden = false;
  }

  // ============================================================
  // COUNTER ANIMATION
  // ============================================================
  function animateCounter(el, from, to, duration) {
    const start = performance.now();
    const update = (ts) => {
      const progress = Math.min((ts - start) / duration, 1);
      const ease     = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(from + (to - from) * ease);
      if (progress < 1) requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
  }

  // ============================================================
  // HTML ESCAPE UTILITY
  // ============================================================
  function escHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  // ============================================================
  // SMOOTH SCROLL FOR ANCHORS
  // ============================================================
  $$('a[href^="#"]').forEach(a => {
    a.addEventListener('click', (e) => {
      const target = $(a.getAttribute('href'));
      if (target) {
        e.preventDefault();
        const offset = (header?.offsetHeight || 72) + 20;
        const y = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top: y, behavior: 'smooth' });
      }
    });
  });

  // ============================================================
  // LAZY LOAD IMAGES (fallback for browsers without native)
  // ============================================================
  if ('IntersectionObserver' in window) {
    const lazyImages = $$('img[loading="lazy"]');
    if (lazyImages.length) {
      const imgObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            if (img.dataset.src) img.src = img.dataset.src;
            imgObserver.unobserve(img);
          }
        });
      }, { rootMargin: '200px' });
      lazyImages.forEach(img => imgObserver.observe(img));
    }
  }

})();
