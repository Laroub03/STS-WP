/* EC-WEBGL-001 / EC-CANVAS-LIVE-001 runtime - carries a live WebGL or ambient
   2D capsule iframe with a poster fallback. Dependency-free; inert unless a
   .wpconvert-webgl element exists. canvas2d kind skips the WebGL-support
   gate; data-wpconvert-pointer enables mouse forwarding into the capsule. */
(function () {
  'use strict';
  var reduce =
    window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var conn = navigator.connection || {};
  var saveData = !!conn.saveData;
  function webglSupported() {
    try {
      var c = document.createElement('canvas');
      return !!(
        c.getContext('webgl2') ||
        c.getContext('webgl') ||
        c.getContext('experimental-webgl')
      );
    } catch (e) {
      return false;
    }
  }
  function activate(wrap) {
    var frame = wrap.querySelector('iframe[data-src]');
    var poster = wrap.querySelector('.wpconvert-webgl-poster');
    if (!frame) return; // poster-only hit (capsule failed boot verification)
    var kind = wrap.getAttribute('data-canvas-kind') || 'webgl';
    // canvas2d ambient backgrounds do not need WebGL; only WebGL kind is gated.
    if (reduce || saveData) return; // keep the poster
    if (kind !== 'canvas2d' && !webglSupported()) return;
    var loaded = false;
    var settled = false;
    function load() {
      if (loaded) return;
      loaded = true;
      frame.src = frame.getAttribute('data-src');
    }
    var io = null;
    if ('IntersectionObserver' in window) {
      io = new IntersectionObserver(
        function (entries) {
          for (var i = 0; i < entries.length; i++) {
            if (entries[i].isIntersecting) {
              io.disconnect();
              load();
              break;
            }
          }
        },
        { rootMargin: '300px' }
      );
      io.observe(wrap);
    } else {
      load();
    }
    var timeout = setTimeout(function () {
      /* not ready -> keep poster */
    }, 15000);
    window.addEventListener('message', function (ev) {
      if (frame.contentWindow && ev.source !== frame.contentWindow) return;
      var d = ev.data || {};
      if (d.type === 'wpconvert-webgl-ready') {
        settled = true;
        clearTimeout(timeout);
        frame.style.opacity = '1';
        if (poster) {
          poster.style.opacity = '0';
        }
        if (wrap.getAttribute('data-webgl-scroll') === '1') {
          var fwd = function () {
            try {
              frame.contentWindow.postMessage(
                {
                  type: 'wpconvert-webgl-scroll',
                  y: window.pageYOffset,
                  vh: window.innerHeight,
                  h: document.documentElement.scrollHeight,
                },
                '*'
              );
            } catch (e) {}
          };
          window.addEventListener('scroll', fwd, { passive: true });
          window.addEventListener('resize', fwd, { passive: true });
          fwd();
        }
        // EC-CANVAS-LIVE-001: forward pointer into the capsule so particle
        // attraction / hover effects still work. The iframe stays
        // pointer-events:none; the wrap receives events (pointer-events:auto).
        if (wrap.getAttribute('data-wpconvert-pointer') === '1') {
          wrap.style.pointerEvents = 'auto';
          var sendPtr = function (ev) {
            try {
              var r = wrap.getBoundingClientRect();
              frame.contentWindow.postMessage(
                {
                  type: 'wpconvert-webgl-pointer',
                  clientX: ev.clientX,
                  clientY: ev.clientY,
                  x: ev.clientX - r.left,
                  y: ev.clientY - r.top,
                  buttons: ev.buttons || 0,
                },
                '*'
              );
            } catch (e) {}
          };
          wrap.addEventListener('mousemove', sendPtr, { passive: true });
          wrap.addEventListener('pointermove', sendPtr, { passive: true });
          wrap.addEventListener(
            'mouseleave',
            function () {
              try {
                frame.contentWindow.postMessage(
                  {
                    type: 'wpconvert-webgl-pointer',
                    clientX: -9999,
                    clientY: -9999,
                    x: -9999,
                    y: -9999,
                    buttons: 0,
                  },
                  '*'
                );
              } catch (e) {}
            },
            { passive: true }
          );
        }
      }
    });
    frame.addEventListener('error', function () {
      /* keep poster */
    });
  }
  function init() {
    var wraps = document.querySelectorAll(
      '.wpconvert-webgl[data-wpconvert-webgl]'
    );
    for (var i = 0; i < wraps.length; i++) {
      try {
        activate(wraps[i]);
      } catch (e) {}
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
