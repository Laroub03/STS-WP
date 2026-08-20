(function () {
  document
    .querySelectorAll('[data-wpc-carousel="true"]')
    .forEach(function (container) {
      var slides = container.querySelectorAll('[data-wpc-slide]');
      var dots = container.querySelectorAll('[data-wpc-carousel-dot]');
      var prevBtns = container.querySelectorAll('[data-wpc-carousel-prev]');
      var nextBtns = container.querySelectorAll('[data-wpc-carousel-next]');
      if (slides.length < 2) return;
      var current = 0;
      var total = slides.length;
      var timer = null;
      // EC-HERO-CAROUSEL-015: derive the active/inactive dot class deltas from the
      // captured markup so the indicator tracks the active slide for ANY source
      // styling. (The previous hard-coded bg-foreground/bg-foreground-25 tokens
      // were almost never the real dot classes — e.g. Horizons uses w-8 bg-primary
      // vs w-2 bg-white/40 — so the active dot never moved.)
      var dotActiveAdd = [],
        dotInactiveAdd = [];
      (function () {
        if (dots.length < 2) return;
        var sigs = [],
          i;
        for (i = 0; i < dots.length; i++)
          sigs.push(
            (dots[i].className || '').trim().split(/\s+/).filter(Boolean)
          );
        var groups = {},
          order = [];
        for (i = 0; i < sigs.length; i++) {
          var key = sigs[i].slice().sort().join(' ');
          if (!groups[key]) {
            groups[key] = { count: 0, toks: sigs[i] };
            order.push(key);
          }
          groups[key].count++;
        }
        if (order.length !== 2) return;
        var gA = groups[order[0]],
          gB = groups[order[1]],
          act,
          inact;
        if (gA.count !== gB.count) {
          act = gA.count < gB.count ? gA.toks : gB.toks;
          inact = act === gA.toks ? gB.toks : gA.toks;
        } else {
          var hint =
            /^(bg-primary|bg-foreground|bg-white|w-6|w-8|w-10|w-12|scale-)/;
          var aOnly = gA.toks.filter(function (x) {
            return gB.toks.indexOf(x) < 0;
          });
          var bOnly = gB.toks.filter(function (x) {
            return gA.toks.indexOf(x) < 0;
          });
          var aHint = aOnly.some(function (x) {
            return hint.test(x);
          });
          var bHint = bOnly.some(function (x) {
            return hint.test(x);
          });
          if (aHint && !bHint) {
            act = gA.toks;
            inact = gB.toks;
          } else if (bHint && !aHint) {
            act = gB.toks;
            inact = gA.toks;
          } else return;
        }
        dotActiveAdd = act.filter(function (x) {
          return x && inact.indexOf(x) < 0;
        });
        dotInactiveAdd = inact.filter(function (x) {
          return x && act.indexOf(x) < 0;
        });
      })();
      function goTo(idx) {
        slides.forEach(function (s, i) {
          if (i === idx) {
            s.classList.remove('opacity-0', 'pointer-events-none');
            s.classList.add('opacity-100');
          } else {
            s.classList.remove('opacity-100');
            s.classList.add('opacity-0', 'pointer-events-none');
          }
        });
        dots.forEach(function (d, i) {
          if (dotActiveAdd.length || dotInactiveAdd.length) {
            var add = i === idx ? dotActiveAdd : dotInactiveAdd;
            var rem = i === idx ? dotInactiveAdd : dotActiveAdd;
            var k;
            for (k = 0; k < rem.length; k++) {
              try {
                d.classList.remove(rem[k]);
              } catch (e) {}
            }
            for (k = 0; k < add.length; k++) {
              try {
                d.classList.add(add[k]);
              } catch (e) {}
            }
          } else {
            // No derivable delta (identical dots / >2 styles): neutral marker only.
            if (i === idx) {
              try {
                d.classList.add('wpc-dot-active');
              } catch (e) {}
            } else {
              try {
                d.classList.remove('wpc-dot-active');
              } catch (e) {}
            }
          }
        });
        current = idx;
      }
      function pauseTimer() {
        if (timer) clearInterval(timer);
        timer = null;
      }
      function startTimer() {
        // EC-EDITOR-067: never autoplay while the inline editor is open — the
        // timer was switching slides while users edited a carousel image.
        if (
          document.body &&
          document.body.classList.contains('wpconvert-editing')
        )
          return;
        pauseTimer();
        timer = setInterval(function () {
          goTo((current + 1) % total);
        }, 6000);
      }
      container._wpcCarouselPause = pauseTimer;
      container._wpcCarouselGoTo = goTo;
      if (!window._wpcCarouselPauseAll) {
        window._wpcCarouselPauseAll = function () {
          document
            .querySelectorAll('[data-wpc-carousel="true"]')
            .forEach(function (c) {
              if (c._wpcCarouselPause) c._wpcCarouselPause();
            });
        };
      }
      // EC-HERO-CAROUSEL-013: normalize initial state on load so exactly one slide
      // shows even if a later build pass stripped the base opacity-0 from the
      // non-active slide markup (prevents all-slides-stacked flicker/overlap).
      goTo(0);
      startTimer();
      dots.forEach(function (d) {
        d.style.cursor = 'pointer';
        d.addEventListener('click', function () {
          pauseTimer();
          goTo(parseInt(d.getAttribute('data-wpc-carousel-dot'), 10));
          startTimer();
        });
      });
      prevBtns.forEach(function (b) {
        b.style.cursor = 'pointer';
        b.addEventListener('click', function () {
          pauseTimer();
          goTo((current - 1 + total) % total);
          startTimer();
        });
      });
      nextBtns.forEach(function (b) {
        b.style.cursor = 'pointer';
        b.addEventListener('click', function () {
          pauseTimer();
          goTo((current + 1) % total);
          startTimer();
        });
      });
    });
})();
