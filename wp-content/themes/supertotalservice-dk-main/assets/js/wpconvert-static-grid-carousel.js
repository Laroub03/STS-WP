/* EC-CAROUSEL-022: Static-grid carousel runtime.
 * Root-cause fix: the source React component leaves prev/next arrows
 * without onClick handlers. When (slides <= visibleCols) the source
 * intent is decorative — we HIDE the arrows. When (slides > visibleCols)
 * we wire a transform-based slider (no overflow-scroll dependency). */
(function () {
  var WIRED_FLAG = 'wpcStaticCarouselWired';
  function setupOne(wrapper) {
    if (!wrapper || wrapper.dataset[WIRED_FLAG] === '1') return;
    var track = wrapper.querySelector('[data-wpconvert-static-carousel-track]');
    if (!track) return;
    var slides = Array.prototype.slice.call(
      track.querySelectorAll('[data-wpconvert-static-carousel-slide]')
    );
    if (slides.length < 2) return;
    var prevBtns = Array.prototype.slice.call(
      wrapper.querySelectorAll('[data-wpconvert-static-carousel-prev]')
    );
    var nextBtns = Array.prototype.slice.call(
      wrapper.querySelectorAll('[data-wpconvert-static-carousel-next]')
    );
    if (!prevBtns.length && !nextBtns.length) return;

    function readVisibleCols() {
      // Prefer raw value (preserves "repeat(N, ...)") then fall back to
      // resolved (split track lengths). Browsers may resolve repeat() to
      // explicit lengths or leave it intact depending on the property.
      var cs = window.getComputedStyle(track);
      var gtc = (cs.gridTemplateColumns || '').trim();
      if (!gtc || gtc === 'none') return 1;
      var rm = gtc.match(/repeat\s*\(\s*(\d+)/i);
      if (rm) return parseInt(rm[1], 10) || 1;
      var n = gtc.split(/\s+/).filter(Boolean).length;
      return n > 0 ? n : 1;
    }

    function readGapPx() {
      var cs = window.getComputedStyle(track);
      return parseFloat(cs.columnGap || cs.gap || '0') || 0;
    }

    function setBtnVisible(btn, visible) {
      // We only ever toggle between "inline display:none" and "no inline
      // override". This is critical because the source CSS often hides
      // arrows on mobile via @media (min-width: 1024px). If we snapshotted
      // the desktop "display:flex" and restored it on resize-to-mobile, we
      // would defeat the source's responsive intent and force-show the
      // arrows on mobile. Instead, "show" = removeProperty (let CSS rule
      // decide), "hide" = inline display:none (always wins over CSS rule).
      if (visible) {
        btn.style.removeProperty('display');
      } else {
        btn.style.display = 'none';
      }
    }

    // -- Mode A (decorative, no overflow) -----------------------------
    // We never modify the grid layout; we just hide buttons. Restored
    // to visible if a later resize creates overflow.
    function applyDecorative() {
      teardownPaginate(); // safe no-op if not paginating
      for (var i = 0; i < prevBtns.length; i++)
        setBtnVisible(prevBtns[i], false);
      for (var j = 0; j < nextBtns.length; j++)
        setBtnVisible(nextBtns[j], false);
    }

    // -- Mode B (real pagination, transform-based) --------------------
    var inner = null; // <div> wrapping all cards inside the track
    var origTrackDisplay = null;
    var origTrackOverflow = null;
    var origTrackPadding = null;
    var currentIndex = 0;
    var origCardStyles = []; // snapshot of inline styles per card

    function buildPaginate() {
      if (inner) return; // already built
      origTrackDisplay = track.style.display;
      origTrackOverflow = track.style.overflow;
      origTrackPadding = track.style.padding;
      // Snapshot original card inline styles so teardown is exact.
      origCardStyles = slides.map(function (c) {
        return c.getAttribute('style') || '';
      });
      // Switch the original grid into a single-row viewport.
      track.style.display = 'block';
      track.style.overflow = 'hidden';
      // min-width: 0 lets the viewport shrink inside its flex parent.
      track.style.minWidth = '0';
      // Build the inner flex row that gets translated.
      inner = document.createElement('div');
      inner.setAttribute('data-wpconvert-static-carousel-row', '');
      inner.style.display = 'flex';
      inner.style.flexWrap = 'nowrap';
      inner.style.willChange = 'transform';
      inner.style.transition = 'transform 0.35s ease';
      // Move existing slide elements into the inner row.
      for (var k = 0; k < slides.length; k++) {
        inner.appendChild(slides[k]);
      }
      track.appendChild(inner);
    }

    function teardownPaginate() {
      if (!inner) return;
      // Move slides back to direct children of the track in original order.
      for (var k = 0; k < slides.length; k++) {
        track.appendChild(slides[k]);
        if (origCardStyles[k])
          slides[k].setAttribute('style', origCardStyles[k]);
        else slides[k].removeAttribute('style');
      }
      try {
        track.removeChild(inner);
      } catch (e) {
        /* ignore */
      }
      inner = null;
      track.style.display = origTrackDisplay || '';
      track.style.overflow = origTrackOverflow || '';
      track.style.padding = origTrackPadding || '';
      track.style.removeProperty('min-width');
      currentIndex = 0;
    }

    function applyPaginateLayout(visibleCols, gap) {
      buildPaginate();
      // Each card gets exact width = (track - gaps) / visibleCols.
      var trackW = track.clientWidth;
      if (trackW <= 0) return; // not laid out yet (display:none ancestor)
      var cardW = (trackW - gap * Math.max(0, visibleCols - 1)) / visibleCols;
      // The inner row carries the gaps between cards.
      inner.style.gap = gap + 'px';
      for (var i = 0; i < slides.length; i++) {
        slides[i].style.flex = '0 0 ' + cardW + 'px';
        slides[i].style.minWidth = '0'; // prevent intrinsic min-width on cards
      }
      // Clamp index — viewport may have grown to show more cards now.
      var maxIndex = Math.max(0, slides.length - visibleCols);
      if (currentIndex > maxIndex) currentIndex = maxIndex;
      // Translate by index * (cardW + gap).
      var x = -currentIndex * (cardW + gap);
      inner.style.transform = 'translateX(' + x + 'px)';
      // Show buttons; disable past the ends so users get visual feedback.
      for (var p = 0; p < prevBtns.length; p++) {
        setBtnVisible(prevBtns[p], true);
        prevBtns[p].disabled = currentIndex <= 0;
        prevBtns[p].setAttribute(
          'aria-disabled',
          currentIndex <= 0 ? 'true' : 'false'
        );
      }
      for (var n = 0; n < nextBtns.length; n++) {
        setBtnVisible(nextBtns[n], true);
        nextBtns[n].disabled = currentIndex >= maxIndex;
        nextBtns[n].setAttribute(
          'aria-disabled',
          currentIndex >= maxIndex ? 'true' : 'false'
        );
      }
    }

    function go(dir) {
      var visibleCols = readVisibleCols();
      var gap = readGapPx();
      if (slides.length <= visibleCols) return; // decorative — nothing to do
      var maxIndex = Math.max(0, slides.length - visibleCols);
      var next = currentIndex + dir;
      if (next < 0) next = 0;
      if (next > maxIndex) next = maxIndex;
      if (next === currentIndex) return;
      currentIndex = next;
      applyPaginateLayout(visibleCols, gap);
    }

    function reflow() {
      var visibleCols = readVisibleCols();
      var gap = readGapPx();
      if (slides.length <= visibleCols) {
        applyDecorative();
      } else {
        applyPaginateLayout(visibleCols, gap);
      }
    }

    // Wire clicks once. Closure correctly captures `wrapper` because
    // setupOne is invoked per-wrapper from the outer init() loop.
    for (var pi = 0; pi < prevBtns.length; pi++) {
      (function (btn) {
        btn.style.cursor = 'pointer';
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          go(-1);
        });
      })(prevBtns[pi]);
    }
    for (var ni = 0; ni < nextBtns.length; ni++) {
      (function (btn) {
        btn.style.cursor = 'pointer';
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          go(1);
        });
      })(nextBtns[ni]);
    }

    // Re-evaluate on resize (debounced via rAF) — visible-col count is
    // viewport-dependent (e.g. 1 col on mobile, 3 cols on desktop).
    var rafPending = false;
    window.addEventListener('resize', function () {
      if (rafPending) return;
      rafPending = true;
      window.requestAnimationFrame(function () {
        rafPending = false;
        reflow();
      });
    });

    // Initial pass.
    reflow();
    wrapper.dataset[WIRED_FLAG] = '1';
  }

  function init() {
    var wrappers = document.querySelectorAll(
      '[data-wpconvert-static-carousel="true"]'
    );
    for (var w = 0; w < wrappers.length; w++) setupOne(wrappers[w]);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
