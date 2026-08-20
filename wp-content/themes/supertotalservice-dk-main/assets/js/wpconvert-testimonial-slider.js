/* EC-CAROUSEL-030: data-driven testimonial slider runtime. */
(function () {
  var FLAG = 'wpcTestimonialSliderWired';
  function setup(root) {
    if (!root || root.dataset[FLAG] === '1') return;
    var slides = Array.prototype.slice.call(
      root.querySelectorAll('[data-wpc-tslide]')
    );
    if (slides.length < 2) return;
    var dots = Array.prototype.slice.call(
      root.querySelectorAll('[data-wpc-tslider-dot]')
    );
    var prevBtns = Array.prototype.slice.call(
      root.querySelectorAll('[data-wpc-tslider-prev]')
    );
    var nextBtns = Array.prototype.slice.call(
      root.querySelectorAll('[data-wpc-tslider-next]')
    );
    var idx = 0,
      timer = null;
    function show(i) {
      idx = (i + slides.length) % slides.length;
      for (var s = 0; s < slides.length; s++) {
        slides[s].style.display = s === idx ? '' : 'none';
      }
      for (var d = 0; d < dots.length; d++) {
        var on = d === idx;
        dots[d].classList.toggle('__on', on);
        dots[d].classList.toggle('is-active', on);
        dots[d].setAttribute('aria-current', on ? 'true' : 'false');
      }
    }
    function go(dir) {
      show(idx + dir);
      restart();
    }
    function restart() {
      if (timer) clearInterval(timer);
      timer = setInterval(function () {
        show(idx + 1);
      }, 6000);
    }
    prevBtns.forEach(function (b) {
      b.style.cursor = 'pointer';
      b.addEventListener('click', function (e) {
        e.preventDefault();
        go(-1);
      });
    });
    nextBtns.forEach(function (b) {
      b.style.cursor = 'pointer';
      b.addEventListener('click', function (e) {
        e.preventDefault();
        go(1);
      });
    });
    dots.forEach(function (b, i) {
      b.style.cursor = 'pointer';
      b.addEventListener('click', function (e) {
        e.preventDefault();
        show(i);
        restart();
      });
    });
    root.addEventListener('mouseenter', function () {
      if (timer) clearInterval(timer);
    });
    root.addEventListener('mouseleave', restart);
    show(0);
    restart();
    root.dataset[FLAG] = '1';
  }
  function init() {
    var roots = document.querySelectorAll('[data-wpc-testimonial-slider]');
    for (var i = 0; i < roots.length; i++) setup(roots[i]);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
