(function($) {
  'use strict';

  function resizeColumns() {
    let $columns = $('.dc-hidden-column');
    $columns.each(function(index, column) {
      let $column = $(column),
        hiddenSize = parseFloat($column.data('size')),
        $row = $column.closest('.elementor-row,.elementor-container'),
        $children = $row.find('> .elementor-column'),
        rowSize = 0;

      if ($children.length === 0) {
        return;
      }

      // get percent-width of row
      $children.each(function(cIndex, child) {
        let $child = $(child);
        rowSize += parseFloat($child.width() / $row.width() * 100);
      });

      $children.each(function(cIndex, child) {
        // resize columns
        let $child = $(child),
          childSize = parseFloat($child.width() / $row.width() * 100),
          newSize = childSize + (hiddenSize * (childSize / rowSize));

        if (childSize < 100) {
          $child.css({width: newSize + '%'});
        }
      });

    });
  }

  function resetColumns() {
    let $columns = $('.dc-hidden-column');
    $columns.each(function(index, column) {
      let $column = $(column),
        $row = $column.closest('.elementor-row,.elementor-container'),
        $children = $row.find('> .elementor-column');

      // reset width for recalc
      $children.css({width: ''});
    });
  }

  function hideWrappers() {
    let $elements = $('.dc-hide-wrapper');
    $elements.each(function(index, element) {
      let $element = $(element),
        $wrapper = $element.closest($element.data('selector'));
      $wrapper.css({display: 'none'});
    });
  }

  function hideOthers() {
    let $elements = $('.dc-hide-others');
    $elements.each(function(index, element) {
      let $element = $(element),
        $toHide = $($element.data('selector'));
      $toHide.css({display: 'none'});
    });
  }

  $(window).on('resize', function() {
    resetColumns();
    resizeColumns();
  });

  $(window).on('elementor/frontend/init', function() {
    resetColumns();
    resizeColumns();
    hideWrappers();
    hideOthers();
  });
})(jQuery);
