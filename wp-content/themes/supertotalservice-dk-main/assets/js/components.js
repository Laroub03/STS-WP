/* Fetches shared header/footer, sets active nav link, wires mobile toggle */
(function () {
  var serviceMetrics = [
    ['24/7', 'Beredskab'],
    ['Fleksibel', 'Aftale'],
    ['INSTA', '800 cert.'],
    ['2 t', 'Svartid'],
  ];

  function renderServiceMetrics() {
    document
      .querySelectorAll('[data-service-metrics]')
      .forEach(function (metrics) {
        metrics.classList.add('metric-strip');
        metrics.innerHTML = serviceMetrics
          .map(function (metric) {
            return (
              '<div class="metric"><strong>' +
              metric[0] +
              '</strong><span>' +
              metric[1] +
              '</span></div>'
            );
          })
          .join('');
      });
  }

  function addFloatingContactRail() {
    if (location.pathname.indexOf('/admin') === 0) return;
    if (document.getElementById('floating-contact-rail')) return;

    var rail = document.createElement('aside');
    rail.id = 'floating-contact-rail';
    rail.className = 'floating-contact-rail';
    rail.setAttribute('aria-label', 'Hurtig kontakt');
    rail.innerHTML =
      '' +
      '<a class="floating-contact-btn" href="tel:+4536302525" aria-label="Ring til STS ApS">' +
      '<span class="floating-contact-icon" aria-hidden="true">📞</span>' +
      '<span class="floating-contact-text">36 30 25 25</span>' +
      '</a>' +
      '<a class="floating-contact-btn" href="mailto:mail@st-service.dk" aria-label="Send mail til STS ApS">' +
      '<span class="floating-contact-icon" aria-hidden="true">✉</span>' +
      '<span class="floating-contact-text">Send mail</span>' +
      '</a>';

    document.body.appendChild(rail);
  }

  function setActiveNav() {
    var cur =
      location.pathname.replace(/\/index\.html$/, '/').replace(/\/$/, '') ||
      '/';
    document.querySelectorAll('.site-nav a').forEach(function (a) {
      try {
        var ap =
          new URL(a.href).pathname
            .replace(/\/index\.html$/, '/')
            .replace(/\/$/, '') || '/';
        if (ap === cur) a.setAttribute('aria-current', 'page');
        else a.removeAttribute('aria-current');
      } catch (_) {}
    });
  }

  function initNav() {
    var t = document.querySelector('.nav-toggle');
    var n = document.querySelector('.site-nav');
    if (t && n && t.dataset.navToggleBound !== 'true') {
      t.dataset.navToggleBound = 'true';
      t.addEventListener('click', function () {
        var open = n.classList.toggle('open');
        t.setAttribute('aria-expanded', String(open));
      });
    }
  }

  function inject(id, url) {
    var el = document.getElementById(id);
    if (!el) return Promise.resolve();
    return fetch(url)
      .then(function (r) {
        return r.text();
      })
      .then(function (html) {
        var tmp = document.createElement('div');
        tmp.innerHTML = html.trim();
        el.replaceWith(tmp.firstElementChild);
      });
  }

  document.addEventListener('DOMContentLoaded', function () {
    renderServiceMetrics();
    addFloatingContactRail();
    Promise.all([
      inject('site-header', '/components/header.html'),
      inject('site-footer', '/components/footer.html'),
    ]).then(function () {
      setActiveNav();
      initNav();
    });
  });
})();
