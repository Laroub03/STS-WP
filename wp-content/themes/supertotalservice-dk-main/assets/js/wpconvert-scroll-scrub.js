/* EC-SCRUB-001: scroll-scrub frame-sequence canvas runtime.
 * Re-drives an Apple-style pinned image sequence on scroll from the frames
 * already shipped in assets/images. Progressive enhancement: the frozen
 * <img data-wpconvert-canvas-snapshot> stays as the no-JS fallback. */
(function () {
  function pad(n, width) {
    n = String(n);
    while (n.length < width) n = '0' + n;
    return n;
  }

  function frameUrlPrefix(stage, base) {
    // Derive the frame directory from the stage background-image (already a
    // fully-resolved URL at runtime, so the theme path token is handled for us).
    try {
      var bg = window.getComputedStyle(stage).backgroundImage || '';
      var m = /url\(\s*["\']?([^"\')]+)["\']?\s*\)/i.exec(bg);
      if (m && m[1]) {
        var url = m[1];
        var idx = url.lastIndexOf('/');
        return idx >= 0 ? url.slice(0, idx + 1) : '';
      }
    } catch (e) {}
    return '';
  }

  function setup(section) {
    var canvas = section.querySelector(
      'canvas[data-wpconvert-scroll-scrub-canvas]'
    );
    if (!canvas) return;
    var stage = canvas.parentNode;
    if (!stage) return;

    var base = section.getAttribute('data-scrub-frame-base') || '';
    var ext = section.getAttribute('data-scrub-frame-ext') || 'jpg';
    var count =
      parseInt(section.getAttribute('data-scrub-frame-count'), 10) || 0;
    var padW = parseInt(section.getAttribute('data-scrub-frame-pad'), 10) || 0;
    var start = parseInt(section.getAttribute('data-scrub-frame-start'), 10);
    if (isNaN(start)) start = 1;
    if (count < 2) return;

    var prefix = frameUrlPrefix(stage, base);
    var images = [];
    for (var i = 0; i < count; i++) {
      var img = new Image();
      img.src = prefix + base + pad(start + i, padW) + '.' + ext;
      images.push(img);
    }

    var fallback = stage.querySelector('img[data-wpconvert-canvas-snapshot]');
    var beats = [];
    (function () {
      var nodes = section.querySelectorAll(
        '[data-wpconvert-scroll-scrub-beat]'
      );
      for (var b = 0; b < nodes.length; b++) beats.push(nodes[b]);
      beats.sort(function (a, c) {
        return (
          (parseInt(a.getAttribute('data-wpconvert-scroll-scrub-beat'), 10) ||
            0) -
          (parseInt(c.getAttribute('data-wpconvert-scroll-scrub-beat'), 10) ||
            0)
        );
      });
    })();
    var ctx = canvas.getContext('2d');
    var curFrame = -1;
    var lastP = -1;
    var raf = 0;
    var revealed = false;

    function ramp(x, a, b) {
      return Math.max(0, Math.min(1, (x - a) / (b - a)));
    }

    // Cross-fade the narrative beats across scroll progress: beat i owns the
    // slice [i/N, (i+1)/N]; the first stays lit from the top, the last stays
    // lit to the bottom. Mirrors the source ramp cross-fade, generically.
    function drawBeats(p) {
      var n = beats.length;
      if (!n) return;
      var seg = 1 / n;
      for (var i = 0; i < n; i++) {
        var fadeW = Math.min(0.06, seg * 0.5);
        var fin = i === 0 ? 1 : ramp(p, i * seg - fadeW, i * seg + fadeW);
        var fout =
          i === n - 1
            ? 0
            : ramp(p, (i + 1) * seg - fadeW, (i + 1) * seg + fadeW);
        beats[i].style.opacity = String(Math.max(0, Math.min(fin, 1 - fout)));
      }
    }

    function sizeCanvas() {
      var dpr = Math.min(window.devicePixelRatio || 1, 2);
      if (!canvas.clientWidth || !canvas.clientHeight) return;
      canvas.width = Math.round(canvas.clientWidth * dpr);
      canvas.height = Math.round(canvas.clientHeight * dpr);
    }

    function draw(idx, force) {
      var img = images[idx];
      if (!img || !img.complete || !img.naturalWidth) return;
      if (idx === curFrame && !force) return;
      if (!canvas.width || !canvas.height) sizeCanvas();
      curFrame = idx;
      var cw = canvas.width,
        ch = canvas.height;
      var ir = img.naturalWidth / img.naturalHeight,
        cr = cw / ch;
      var dw, dh, dx, dy;
      if (ir > cr) {
        dh = ch;
        dw = ch * ir;
        dx = (cw - dw) / 2;
        dy = 0;
      } else {
        dw = cw;
        dh = cw / ir;
        dx = 0;
        dy = (ch - dh) / 2;
      }
      ctx.drawImage(img, dx, dy, dw, dh);
      if (!revealed && fallback) {
        fallback.style.opacity = '0';
        revealed = true;
      }
    }

    function progress() {
      var rect = section.getBoundingClientRect();
      var vh = window.innerHeight || document.documentElement.clientHeight;
      var total = rect.height - vh;
      var p = total > 0 ? -rect.top / total : 0;
      return Math.max(0, Math.min(1, p));
    }

    var reduce =
      window.matchMedia &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduce) {
      // Static: draw the first frame + show the first beat, no scroll loop.
      sizeCanvas();
      if (images[0].complete) draw(0, true);
      else
        images[0].onload = function () {
          sizeCanvas();
          draw(0, true);
        };
      drawBeats(0);
      window.addEventListener('resize', function () {
        sizeCanvas();
        draw(Math.max(curFrame, 0), true);
      });
      return;
    }

    function tick() {
      raf = window.requestAnimationFrame(tick);
      if (!canvas.width) sizeCanvas();
      var p = progress();
      if (Math.abs(p - lastP) < 0.0005 && curFrame >= 0) return;
      lastP = p;
      draw(Math.round(p * (count - 1)));
      drawBeats(p);
    }

    if (images[0].complete) {
      sizeCanvas();
      draw(0, true);
    } else
      images[0].onload = function () {
        sizeCanvas();
        draw(0, true);
      };
    window.addEventListener('resize', function () {
      sizeCanvas();
      draw(Math.max(curFrame, 0), true);
    });
    raf = window.requestAnimationFrame(tick);
  }

  function init() {
    var sections = document.querySelectorAll('[data-wpconvert-scroll-scrub]');
    for (var i = 0; i < sections.length; i++) {
      try {
        setup(sections[i]);
      } catch (e) {
        /* never break the page */
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
