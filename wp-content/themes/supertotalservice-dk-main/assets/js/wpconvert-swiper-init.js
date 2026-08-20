/* EC-SWIPER-002: Themento (Elementor) data-swiper slider FALLBACK initializer.
 * Dependency-free. Runs after window.load (+delay) so the site's own
 * Elementor/inc.js gets first chance; then initializes only the sliders
 * nobody initialized (el.swiper unset). Mirrors inc.js option mapping. */
(function () {
  var STATE_CLASSES = [
    'swiper-initialized',
    'swiper-horizontal',
    'swiper-vertical',
    'swiper-pointer-events',
    'swiper-backface-hidden',
    'swiper-rtl',
    'swiper-css-mode',
    'swiper-watch-progress',
    'swiper-free-mode',
  ];
  var SLIDE_STATE_CLASSES = [
    'swiper-slide-active',
    'swiper-slide-next',
    'swiper-slide-prev',
    'swiper-slide-visible',
    'swiper-slide-fully-visible',
    'swiper-slide-duplicate-active',
    'swiper-slide-duplicate-next',
    'swiper-slide-duplicate-prev',
  ];

  // Remove any frozen runtime state Swiper baked into the snapshot so a
  // fresh new Swiper() rebuilds clones/positions from clean markup. (Usually
  // a no-op because EC-SWIPER-001 already cleaned it at conversion time.)
  function cleanState(el) {
    try {
      var dups = el.querySelectorAll('.swiper-slide-duplicate');
      for (var d = 0; d < dups.length; d++) {
        if (dups[d].parentNode) dups[d].parentNode.removeChild(dups[d]);
      }
      for (var c = 0; c < STATE_CLASSES.length; c++)
        el.classList.remove(STATE_CLASSES[c]);
      var wrap = el.querySelector('.swiper-wrapper');
      if (wrap) {
        wrap.style.removeProperty('transform');
        wrap.style.removeProperty('transition-duration');
        wrap.style.removeProperty('-webkit-transform');
      }
      var slides = el.querySelectorAll('.swiper-slide');
      for (var s = 0; s < slides.length; s++) {
        for (var k = 0; k < SLIDE_STATE_CLASSES.length; k++)
          slides[s].classList.remove(SLIDE_STATE_CLASSES[k]);
        slides[s].style.removeProperty('width');
        slides[s].style.removeProperty('margin-right');
        slides[s].style.removeProperty('margin-left');
      }
    } catch (e) {
      /* non-fatal */
    }
  }

  function buildParams(o, id) {
    var sel = '#' + id + ' ';
    var params = {
      slidesPerView: o.columns || 1,
      spaceBetween: o.space || 0,
      loop: !!o.infinite,
      centeredSlides: !!o.centerMode,
      pagination: { el: sel + '.swiper-pagination', clickable: true },
      navigation: {
        nextEl: sel + '.swiper-button-next',
        prevEl: sel + '.swiper-button-prev',
      },
      breakpoints: {
        10: { slidesPerView: o.columns_mobile || o.columns || 1 },
        480: { slidesPerView: o.columns_mobile_h || o.columns || 1 },
        768: { slidesPerView: o.columns_tablet || o.columns || 1 },
        1024: { slidesPerView: o.columns || 1 },
      },
    };
    if (o.autoplay) {
      params.autoplay = { delay: o.speed || 3000, disableOnInteraction: false };
    }
    if (o.effect && o.effect !== 'none') {
      params.effect = o.effect;
      if (
        o.effect === 'cube' ||
        o.effect === 'coverflow' ||
        o.effect === 'flip' ||
        o.effect === 'cards' ||
        o.effect === 'creative'
      ) {
        params.grabCursor = true;
      }
    }
    return params;
  }

  function setupOne(el) {
    // Skip anything the site's own Elementor/inc.js already initialized —
    // Swiper v8 does NOT guard against a second new Swiper() (double autoplay).
    if (
      !el ||
      el.swiper ||
      el.getAttribute('data-' + 'wpc-swiper-init') === '1'
    )
      return;
    if (!el.classList || !el.classList.contains('swiper')) return;
    if (!el.querySelector('.swiper-wrapper')) return;
    var raw = el.getAttribute('data-swiper');
    if (!raw) return;
    var o;
    try {
      o = JSON.parse(raw);
    } catch (e) {
      return;
    }
    if (!el.id) el.id = 'wpc-swiper-' + Math.random().toString(36).slice(2, 10);
    cleanState(el);
    var params = buildParams(o, el.id);
    el.setAttribute('data-' + 'wpc-swiper-init', '1');
    var instance;
    try {
      instance = new Swiper(el, params);
    } catch (e) {
      return;
    }
    if (o.autoplay && o.pause_on_hover && instance && instance.autoplay) {
      el.addEventListener('mouseenter', function () {
        try {
          instance.autoplay.stop();
        } catch (e) {}
      });
      el.addEventListener('mouseleave', function () {
        try {
          instance.autoplay.start();
        } catch (e) {}
      });
    }
  }

  var tries = 0;
  function initAll() {
    var els = document.querySelectorAll('[data-swiper]');
    if (!els.length) return; // inert on pages without themento sliders
    if (typeof window.Swiper === 'undefined') {
      if (tries++ > 120) return; // ~6s of polling, then give up
      window.setTimeout(initAll, 50);
      return;
    }
    for (var i = 0; i < els.length; i++) setupOne(els[i]);
  }

  // Fallback timing: let the site's own Elementor init run first, then fill
  // in any slider it left dead. 300ms after load is imperceptible for a hero.
  function schedule() {
    window.setTimeout(initAll, 300);
  }
  if (document.readyState === 'complete') {
    schedule();
  } else {
    window.addEventListener('load', schedule);
  }
})();
