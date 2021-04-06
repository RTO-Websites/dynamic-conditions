(function($) {
  'use strict';

  const dcHiddenSelector = '.dc-hidden-column',
    dcHideWrapperSelector = '.dc-hide-wrapper',
    dcHideOthersSelector = '.dc-hide-others',
    dcRowSelector = '.elementor-row,.elementor-container',
    dcColumnSelector = '> .elementor-column';


  function resizeColumns() {
    const $columns = $(dcHiddenSelector);
    $columns.each(function(index, column) {
      const $column = $(column),
        hiddenSize = parseFloat($column.data('size')),
        $row = $column.closest(dcRowSelector),
        $children = $row.find(dcColumnSelector);

      if ($children.length === 0) {
        return;
      }

      // get percent-width of row
      const rowSize = $children.toArray().reduce(
        (acc, child) => acc + calcRowWidth($(child), $row),
        0
      );

      $children.each(function(cIndex, child) {
        // resize columns
        const $child = $(child),
          childSize = calcRowWidth($child, $row),
          newSize = childSize + (hiddenSize * (childSize / rowSize));

        if (childSize < 100) {
          $child.css({width: newSize + '%'});
        }
      });

    });
  }

  function calcRowWidth($child, $row) {
    return parseFloat($child.width() / $row.width() * 100);
  }

  function resetColumns() {
    const $columns = $(dcHiddenSelector);
    $columns.each(function(index, column) {
      const $children = $(column).closest(dcRowSelector).find(dcColumnSelector);

      // reset width for recalc
      $children.css({width: ''});
    });
  }

  function hideWrappers() {
    const $elements = $(dcHideWrapperSelector);
    $elements.each(function(index, element) {
      const $element = $(element),
        $wrapper = $element.closest($element.data('selector'));
      $wrapper.css({display: 'none'});
    });
  }

  function hideOthers() {
    const $elements = $(dcHideOthersSelector);
    $elements.each(function(index, element) {
      const $element = $(element),
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
