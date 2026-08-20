const THEME_STORAGE_KEY = 'sts-theme';
const darkModeMedia = window.matchMedia
  ? window.matchMedia('(prefers-color-scheme: dark)')
  : null;
const THEME_MODE_ORDER = ['auto', 'dark', 'light'];

function readStoredValue(key) {
  try {
    return localStorage.getItem(key);
  } catch (_) {
    return null;
  }
}

function writeStoredValue(key, value) {
  try {
    if (value === null) {
      localStorage.removeItem(key);
      return;
    }
    localStorage.setItem(key, value);
  } catch (_) {
    // Ignore storage failures (private mode or blocked storage).
  }
}

function getStoredTheme() {
  const value = readStoredValue(THEME_STORAGE_KEY);
  if (value === 'light' || value === 'dark' || value === 'auto') return value;
  return 'auto';
}

function getEffectiveTheme() {
  const stored = getStoredTheme();
  if (stored === 'light' || stored === 'dark') return stored;
  return darkModeMedia && darkModeMedia.matches ? 'dark' : 'light';
}

function applyThemePreference(themeMode) {
  const root = document.documentElement;
  if (themeMode === 'dark' || themeMode === 'light') {
    root.setAttribute('data-theme', themeMode);
  } else {
    root.removeAttribute('data-theme');
  }
}

function themeModeLabel(mode) {
  if (mode === 'light') return 'Lys';
  if (mode === 'dark') return 'Mørk';
  return 'Auto';
}

function updateThemeModeControls() {
  const selectedMode = getStoredTheme();

  document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
    const label = themeModeLabel(selectedMode);
    button.textContent = `Tema: ${label}`;
    button.setAttribute('aria-label', `Skift tema. Nuvaerende: ${label}`);
    button.setAttribute('title', `Tema: ${label}`);
  });
}

function setThemeMode(mode) {
  const nextMode =
    mode === 'light' || mode === 'dark' || mode === 'auto' ? mode : 'auto';
  writeStoredValue(THEME_STORAGE_KEY, nextMode);
  applyThemePreference(nextMode);
  updateThemeModeControls();
}

function handleThemeModeActivate(event) {
  if (event) event.preventDefault();
  const current = getStoredTheme();
  const idx = THEME_MODE_ORDER.indexOf(current);
  const next = THEME_MODE_ORDER[(idx + 1) % THEME_MODE_ORDER.length];
  setThemeMode(next);
}

function bindThemeModeButtons(scope) {
  (scope || document)
    .querySelectorAll('[data-theme-toggle]')
    .forEach((button) => {
      if (button.dataset.themeModeBound === 'true') return;
      button.dataset.themeModeBound = 'true';
      button.addEventListener('click', handleThemeModeActivate);
      button.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        handleThemeModeActivate(event);
      });
    });
}

function buildFooterThemeControls() {
  const wrapper = document.createElement('div');
  wrapper.className = 'footer-theme-controls';
  wrapper.innerHTML =
    '' +
    '<button type="button" class="theme-subtle-toggle" data-theme-toggle> Tema: Auto </button>';
  return wrapper;
}

function ensureFooterThemeControls() {
  if (window.location.pathname.startsWith('/admin')) return;

  document.querySelectorAll('.site-footer').forEach((footer) => {
    const controls = footer.querySelector('.footer-theme-controls');
    if (controls) bindThemeModeButtons(controls);
  });

  updateThemeModeControls();
}

function ensureNewsNavLink() {
  const nav = document.querySelector('.site-nav');
  if (!nav) return;

  // Skip if link already exists.
  const alreadyHasNews = Array.from(nav.querySelectorAll('a')).some((a) => {
    const href = (a.getAttribute('href') || '').trim();
    return (
      href === '/blog' ||
      href === '../blog' ||
      href === 'blog' ||
      href === './blog'
    );
  });
  if (alreadyHasNews) return;

  const serviceLink = nav.querySelector('a[href*="service/index.html"]');
  let newsHref = 'blog';
  if (serviceLink) {
    const serviceHref = serviceLink.getAttribute('href') || '';
    if (serviceHref.startsWith('../')) newsHref = '../blog';
    else if (serviceHref.startsWith('/')) newsHref = '/blog';
  }

  const newsLink = document.createElement('a');
  newsLink.setAttribute('href', newsHref);
  newsLink.textContent = 'Nyheder';

  const aboutLink = Array.from(nav.querySelectorAll('a')).find((a) =>
    (a.getAttribute('href') || '').includes('hvem-er-sts/index.html')
  );

  if (aboutLink) {
    nav.insertBefore(newsLink, aboutLink);
  } else {
    nav.appendChild(newsLink);
  }
}

function initNavToggle() {
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.site-nav');
  if (toggle && nav && toggle.dataset.navToggleBound !== 'true') {
    toggle.dataset.navToggleBound = 'true';
    toggle.addEventListener('click', () => {
      const open = nav.classList.toggle('open');
      toggle.setAttribute('aria-expanded', String(open));
    });
  }
}

function initSharedNav() {
  initNavToggle();
}

const legacyCategoryBySlug = {
  byggepladsservice: 'byg',
  'asbest-og-nedrivning': 'byg',
  murer: 'byg',
  toemrer: 'byg',
  nedrivningsservice: 'byg',
  mandskabsudlejning: 'byg',
  haandvaerkere: 'byg',
  maler: 'mal',
  gulvbehandling: 'mal',
  facademaling: 'mal',
  'spartelarbejde-og-filtopsaetning': 'mal',
  trappeopgangsmaling: 'mal',
  'epoxy-og-specialmaling': 'mal',
  rengoering: 'ren',
  vinduespolering: 'ren',
  'rengoering-efter-haandvaerkere': 'ren',
  vicevaertservice: 'ren',
  ejendomsservice: 'ren',
  gartnerservice: 'ren',
  'glatfoere-bekaempelse-snerydning-og-saltning': 'ren',
  'insta-800-certificeret-kontrol-og-inspektion': 'ren',
};

const pillarPageByCategory = {
  byg: 'sts-byg/index.html',
  mal: 'sts-mal/index.html',
  ren: 'sts-ren/index.html',
};

const fallbackServiceImage = '/media/uploads/stock-images/haandvaerkere.jpg';

const defaultServiceImages = {
  rengoering: '/media/uploads/stock-images/rengoering.jpg',
  haandvaerkere: '/media/uploads/stock-images/haandvaerkere.jpg',
  byggepladsservice: '/media/uploads/stock-images/byggepladsservice.jpg',
  'asbest-og-nedrivning':
    '/media/uploads/stock-images/asbest-og-nedrivning.jpg',
  murer: '/media/uploads/stock-images/murer.jpg',
  maler: '/media/uploads/stock-images/maler.jpg',
  toemrer: '/media/uploads/stock-images/toemrer.jpg',
  gulvbehandling: '/media/uploads/stock-images/gulvbehandling.jpg',
  gartnerservice: '/media/uploads/stock-images/gartnerservice.jpg',
  mandskabsudlejning: '/media/uploads/stock-images/mandskabsudlejning.jpg',
  vinduespolering: '/media/uploads/stock-images/vinduespolering.jpg',
  'rengoering-efter-haandvaerkere':
    '/media/uploads/stock-images/rengoering-efter-haandvaerkere.jpg',
  vicevaertservice: '/media/uploads/stock-images/ejendomsservice.jpg',
  'insta-800-certificeret-kontrol-og-inspektion':
    '/media/uploads/stock-images/insta-800-certificeret-kontrol-og-inspektion.jpg',
  ejendomsservice: '/media/uploads/stock-images/ejendomsservice.jpg',
  'glatfoere-bekaempelse-snerydning-og-saltning':
    '/media/uploads/stock-images/glatfoere-bekaempelse-snerydning-og-saltning.jpg',
  nedrivningsservice: '/media/uploads/stock-images/nedrivningsservice.jpg',
  facademaling: '/media/uploads/stock-images/facademaling.jpg',
  'spartelarbejde-og-filtopsaetning':
    '/media/uploads/stock-images/spartelarbejde-og-filtopsaetning.jpg',
  trappeopgangsmaling: '/media/uploads/stock-images/trappeopgangsmaling.jpg',
  'epoxy-og-specialmaling':
    '/media/uploads/stock-images/epoxy-og-specialmaling.jpg',
};

function escapeHtml(value) {
  return String(value || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function normalizeServiceCategory(service) {
  const slug = String(service?.slug || '').trim();
  const category = String(service?.category || '')
    .trim()
    .toLowerCase();
  if (category === 'byg' || category === 'mal' || category === 'ren') {
    return category;
  }
  return legacyCategoryBySlug[slug] || 'ren';
}

function resolveServiceImage(service) {
  const custom = String(service?.image || '').trim();
  if (custom) return custom;
  if (service?.card_image) return String(service.card_image).trim();
  const slug = String(service?.slug || '').trim();
  return defaultServiceImages[slug] || fallbackServiceImage;
}

function buildPillarPageHref(category, prefix) {
  const page = pillarPageByCategory[category] || pillarPageByCategory.ren;
  return `${prefix || ''}${page}`;
}

function initClickablePillarCards() {
  document.querySelectorAll('[data-pillar-link]').forEach((card) => {
    const href = card.getAttribute('data-pillar-link');
    if (!href || card.dataset.pillarBound === 'true') return;
    card.dataset.pillarBound = 'true';
    card.setAttribute('role', 'link');
    card.setAttribute('tabindex', '0');

    const navigate = () => {
      window.location.href = href;
    };

    card.addEventListener('click', (event) => {
      if (event.target.closest('a, button')) return;
      navigate();
    });

    card.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        navigate();
      }
    });
  });
}

function renderPillarTagLists(services) {
  const grouped = { byg: [], mal: [], ren: [] };
  services.forEach((service) => {
    grouped[normalizeServiceCategory(service)].push(service);
  });

  document.querySelectorAll('[data-pillar-list-root]').forEach((root) => {
    const prefix = root.getAttribute('data-path-prefix') || '';

    root.querySelectorAll('[data-pillar]').forEach((list) => {
      const category = list.getAttribute('data-pillar') || 'ren';
      const items = grouped[category] || [];

      list.innerHTML = items.length
        ? items
            .map((service) => {
              const title = escapeHtml(service.title || 'Service');
              const icon = escapeHtml(service.icon || '🔧');
              const slug = escapeHtml(service.slug || '');
              const href = slug ? `${prefix}${slug}/index.html` : '#';
              return `<li><a href="${href}"><span class="service-tag-icon">${icon}</span><span>${title}</span></a></li>`;
            })
            .join('')
        : '<li>Ingen services i denne kategori endnu.</li>';
    });

    root.querySelectorAll('[data-pillar-category-link]').forEach((link) => {
      const category = link.getAttribute('data-pillar-category-link') || 'ren';
      link.setAttribute('href', buildPillarPageHref(category, prefix));
    });

    root.querySelectorAll('[data-pillar-card-category]').forEach((card) => {
      const category = card.getAttribute('data-pillar-card-category') || 'ren';
      card.setAttribute(
        'data-pillar-link',
        buildPillarPageHref(category, prefix)
      );
    });
  });
}

function renderPillarDirectories(services) {
  const grouped = { byg: [], mal: [], ren: [] };
  services.forEach((service) => {
    grouped[normalizeServiceCategory(service)].push(service);
  });

  document.querySelectorAll('[data-pillar-directory]').forEach((container) => {
    const category = container.getAttribute('data-pillar-directory') || 'ren';
    const prefix = container.getAttribute('data-path-prefix') || '';
    const items = grouped[category] || [];

    container.innerHTML = items.length
      ? items
          .map((service) => {
            const title = escapeHtml(service.title || 'Service');
            const image = escapeHtml(resolveServiceImage(service));
            const slug = escapeHtml(service.slug || '');
            const description = escapeHtml(
              service.description || 'Professionel service leveret af STS ApS.'
            );
            const href = slug ? `${prefix}${slug}/index.html` : '#';
            return (
              `<a class="service-card pillar-service-card" href="${href}">` +
              `<img class="service-card-media" src="${image}" alt="${title}">` +
              `<div class="service-card-body">` +
              `<h3>${title}</h3>` +
              `<p>${description}</p>` +
              `<span class="service-link">Læs mere →</span>` +
              `</div>` +
              `</a>`
            );
          })
          .join('')
      : '<p>Ingen services i denne kategori endnu.</p>';
  });
}

function initServicePillars() {
  const hasPillars = document.querySelector(
    '[data-pillar-list-root], [data-pillar-directory]'
  );
  if (!hasPillars) return;

  fetch('/api/services')
    .then((res) => {
      if (!res.ok) throw new Error('Could not load services');
      return res.json();
    })
    .then((payload) => {
      const services = Array.isArray(payload?.services) ? payload.services : [];
      renderPillarTagLists(services);
      renderPillarDirectories(services);
      initClickablePillarCards();
    })
    .catch(() => {
      document.querySelectorAll('[data-pillar]').forEach((list) => {
        list.innerHTML = '<li>Kunne ikke indlæse services lige nu.</li>';
      });
      document
        .querySelectorAll('[data-pillar-directory]')
        .forEach((container) => {
          container.innerHTML = '<p>Kunne ikke indlæse services lige nu.</p>';
        });
      initClickablePillarCards();
    });
}

applyThemePreference(getStoredTheme());

// The public homepage renders its header directly, before DOMContentLoaded.
initNavToggle();

if (darkModeMedia && typeof darkModeMedia.addEventListener === 'function') {
  darkModeMedia.addEventListener('change', () => {
    if (getStoredTheme() === 'auto') {
      applyThemePreference('auto');
      updateThemeModeControls();
    }
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    initSharedNav();
    initServicePillars();
    ensureFooterThemeControls();
  });
} else {
  initSharedNav();
  initServicePillars();
  ensureFooterThemeControls();
}
