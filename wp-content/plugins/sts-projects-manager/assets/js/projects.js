(function () {
  'use strict';

  function initProjectFilter() {
    var filter = document.querySelector('[data-project-filter]');
    var grid = document.querySelector('[data-project-grid]');
    if (!filter || !grid) return;

    var buttons = Array.prototype.slice.call(filter.querySelectorAll('[data-project-filter-value]'));
    var cards = Array.prototype.slice.call(grid.querySelectorAll('[data-project-category]'));
    var empty = document.querySelector('[data-project-empty]');

    function apply(value) {
      var visible = 0;
      cards.forEach(function (card) {
        var match = value === 'all' || card.getAttribute('data-project-category') === value;
        card.hidden = !match;
        if (match) visible++;
      });
      buttons.forEach(function (button) {
        button.classList.toggle('is-active', button.getAttribute('data-project-filter-value') === value);
      });
      if (empty) empty.hidden = visible !== 0;
    }

    buttons.forEach(function (button) {
      button.addEventListener('click', function () {
        apply(button.getAttribute('data-project-filter-value'));
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProjectFilter);
  } else {
    initProjectFilter();
  }
})();
