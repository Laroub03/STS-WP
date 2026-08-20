/* WPConvert EC-SCRUB-006 — sticky scroll-story replay */
(function () {
  'use strict';

  var SPACER = '[data-wpc-scroll-story]';
  var STAGE = '[data-wpc-scroll-story-stage]';
  var STATE = '[data-wpc-story-state]';

  function setup(spacer) {
    var stage = spacer.querySelector(STAGE);
    if (!stage) return;

    var states = [];
    for (var i = 0; i < stage.children.length; i++) {
      if (stage.children[i].matches && stage.children[i].matches(STATE))
        states.push(stage.children[i]);
    }
    if (states.length < 2) return;

    var reduced =
      window.matchMedia &&
      window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduced) {
      spacer.style.height = 'auto';
      stage.style.position = 'static';
      stage.style.height = 'auto';
      for (var r = 0; r < states.length; r++)
        states[r].style.display = 'contents';
      return;
    }

    var current = -1;
    function render() {
      var rect = spacer.getBoundingClientRect();
      var range = rect.height - window.innerHeight;
      var progress = range <= 0 ? 0 : -rect.top / range;
      if (progress < 0) progress = 0;
      if (progress > 1) progress = 1;

      var index = Math.floor(progress * states.length);
      if (index >= states.length) index = states.length - 1;
      if (index === current) return;
      current = index;

      for (var s = 0; s < states.length; s++) {
        states[s].style.display = s === index ? 'contents' : 'none';
      }
    }

    var ticking = false;
    function onScroll() {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(function () {
        ticking = false;
        render();
      });
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    render();
  }

  function init() {
    var spacers = document.querySelectorAll(SPACER);
    for (var i = 0; i < spacers.length; i++) setup(spacers[i]);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
