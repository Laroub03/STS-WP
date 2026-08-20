/* EC-GALLERY-001: Product-image gallery runtime.
 * Re-wires thumbnail buttons to swap the main image — the React onClick
 * (setActive) state handler is lost in the static snapshot. */
(function () {
  var WIRED_FLAG = 'wpcGalleryWired';
  function setupOne(wrapper) {
    if (!wrapper || wrapper.dataset[WIRED_FLAG] === '1') return;
    var main = wrapper.querySelector('[data-wpconvert-gallery-main]');
    if (!main) return;
    var thumbs = Array.prototype.slice.call(
      wrapper.querySelectorAll('[data-wpconvert-gallery-thumb]')
    );
    if (thumbs.length < 2) return;

    // Capture active / inactive class references from the initial markup.
    var activeClassName = thumbs[0].className;
    var inactiveClassName = activeClassName;
    for (var t = 1; t < thumbs.length; t++) {
      if (thumbs[t].className !== activeClassName) {
        inactiveClassName = thumbs[t].className;
        break;
      }
    }

    function imgIn(btn) {
      return btn.querySelector('img');
    }

    function activate(btn) {
      var src = imgIn(btn) ? imgIn(btn).getAttribute('src') : null;
      if (src) {
        main.setAttribute('src', src);
        var ss = imgIn(btn).getAttribute('srcset');
        if (ss) {
          main.setAttribute('srcset', ss);
        } else {
          main.removeAttribute('srcset');
        }
      }
      for (var i = 0; i < thumbs.length; i++) {
        if (thumbs[i] === btn) {
          thumbs[i].className = activeClassName;
          thumbs[i].setAttribute('aria-current', 'true');
        } else {
          thumbs[i].className = inactiveClassName;
          thumbs[i].removeAttribute('aria-current');
        }
      }
    }

    for (var k = 0; k < thumbs.length; k++) {
      (function (btn) {
        btn.style.cursor = 'pointer';
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          activate(btn);
        });
      })(thumbs[k]);
    }

    wrapper.dataset[WIRED_FLAG] = '1';
  }

  function init() {
    var wrappers = document.querySelectorAll('[data-wpconvert-gallery="true"]');
    for (var w = 0; w < wrappers.length; w++) setupOne(wrappers[w]);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
