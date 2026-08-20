/* EC-PARALLAX-001: generic parallax runtime.
 * Re-drives translateY-on-scroll layers (data-wpconvert-parallax-y) and
 * background-attachment:fixed layers (data-wpconvert-parallax-bg) that were
 * frozen at capture. Progressive enhancement: honours prefers-reduced-motion
 * and falls back to background-position on touch/small screens where
 * background-attachment:fixed is ignored (iOS/mobile Safari). */
(function () {
  var reduce =
    window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduce) return; // leave everything static

  // iOS/mobile Safari (and most touch browsers) ignore background-attachment:
  // fixed, so we JS-drive the background there instead of relying on CSS.
  function isTouchOrSmall() {
    var coarse =
      window.matchMedia &&
      window.matchMedia('(hover: none) and (pointer: coarse)').matches;
    return !!coarse || (window.innerWidth || 0) <= 1024;
  }

  // Preserve the centering idiom translateX/Y(±50%) when composing our
  // parallax translateY (EC-SCRUB-004: blindly rewriting transforms shifts
  // centered overlays by half their size). We strip only NON-centering
  // translateY() from the captured base, then append our own each frame.
  function isCentering50(v) {
    return /^\s*-?50(?:\.0+)?%\s*$/.test(v);
  }
  function baseTransform(t) {
    if (!t || t === 'none') return '';
    return t
      .replace(/translateY\(([^)]*)\)/g, function (m, v) {
        return isCentering50(v) ? m : '';
      })
      .replace(/\s+/g, ' ')
      .trim();
  }

  var yEntries = [];
  var bgEntries = [];

  function scrollTop() {
    return window.pageYOffset || document.documentElement.scrollTop || 0;
  }

  function setupY(el) {
    var speed = parseFloat(el.getAttribute('data-wpconvert-parallax-y'));
    if (isNaN(speed) || speed === 0) return;
    // Anchor at the CURRENT scroll so the layer is at REST (offset 0) on first
    // paint — matching the frozen snapshot and the original library, which
    // both sit at translateY(0) at the top. startY is virtually always 0.
    yEntries.push({
      el: el,
      speed: speed,
      base: baseTransform(el.style.transform),
      applied: 0,
      startY: scrollTop(),
    });
  }

  function setupBg(el) {
    var speed =
      Math.abs(parseFloat(el.getAttribute('data-wpconvert-parallax-bg'))) ||
      0.3;
    if (isTouchOrSmall()) {
      // Swap fixed -> scroll so we can drive it, then parallax the position.
      el.style.backgroundAttachment = 'scroll';
      bgEntries.push({ el: el, speed: speed });
    }
    // Desktop: leave CSS background-attachment:fixed alone (it already works).
  }

  function updateY() {
    // Scroll-linked model: translateY = speed * (scrollY - startY). This is
    // exactly what the probe MEASURED (speed = d(translateY)/d(scrollY)) and
    // what Rellax / jarallax / hand-rolled translateY do, so it (a) leaves
    // layers at rest at the top and (b) AGREES with any surviving original
    // driver instead of fighting it. No getBoundingClientRect → no feedback.
    var y = scrollTop();
    for (var i = 0; i < yEntries.length; i++) {
      var e = yEntries[i];
      var offset = (y - e.startY) * e.speed;
      e.applied = offset;
      var t = e.base ? e.base + ' ' : '';
      e.el.style.transform = t + 'translateY(' + offset.toFixed(2) + 'px)';
    }
  }

  function updateBg() {
    var vh = window.innerHeight || document.documentElement.clientHeight;
    for (var i = 0; i < bgEntries.length; i++) {
      var e = bgEntries[i];
      var rect = e.el.getBoundingClientRect();
      // 0 when the element top hits the viewport bottom, 1 when its bottom
      // leaves the top; centre the background at 50% and drift around it.
      var progress = (vh - rect.top) / (vh + rect.height);
      if (progress < 0) progress = 0;
      else if (progress > 1) progress = 1;
      var pos = 50 + (progress - 0.5) * e.speed * 60;
      e.el.style.backgroundPositionX = 'center';
      e.el.style.backgroundPositionY = pos.toFixed(2) + '%';
    }
  }

  var ticking = false;
  function onScroll() {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(function () {
      try {
        updateY();
        updateBg();
      } catch (e) {}
      ticking = false;
    });
  }

  function init() {
    var ys = document.querySelectorAll('[data-wpconvert-parallax-y]');
    var bgs = document.querySelectorAll('[data-wpconvert-parallax-bg]');
    for (var i = 0; i < ys.length; i++) {
      try {
        setupY(ys[i]);
      } catch (e) {}
    }
    for (var j = 0; j < bgs.length; j++) {
      try {
        setupBg(bgs[j]);
      } catch (e) {}
    }
    if (!yEntries.length && !bgEntries.length) return;
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    updateY();
    updateBg();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
