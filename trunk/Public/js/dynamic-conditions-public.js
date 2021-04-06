(function($) {
  'use strict';

  const dcHiddenSelector = '.dc-hidden-column',
    dcHideWrapperSelector = '.dc-hide-wrapper',
    dcHideOthersSelector = '.dc-hide-others',
    dcRowSelector = '.elementor-row,.elementor-container',
    dcColumnSelector = '> .elementor-column';


  function resizeColumns() {
    let $columns = $(dcHiddenSelector);
    $columns.each(function(index, column) {
      let $column = $(column),
        hiddenSize = parseFloat($column.data('size')),
        $row = $column.closest(dcRowSelector),
        $children = $row.find(dcColumnSelector),
        rowSize = 0;

      if ($children.length === 0) {
        return;
      }

      // get percent-width of row
      $children.each(function(cIndex, child) {
        const $child = $(child);
        rowSize += calcRowWidth($child, $row);
      });

      $children.each(function(cIndex, child) {
        // resize columns
        let $child = $(child),
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
    let $columns = $(dcHiddenSelector);
    $columns.each(function(index, column) {
      let $column = $(column),
        $row = $column.closest(dcRowSelector),
        $children = $row.find(dcColumnSelector);

      // reset width for recalc
      $children.css({width: ''});
    });
  }

  function hideWrappers() {
    let $elements = $(dcHideWrapperSelector);
    $elements.each(function(index, element) {
      let $element = $(element),
        $wrapper = $element.closest($element.data('selector'));
      $wrapper.css({display: 'none'});
    });
  }

  function hideOthers() {
    let $elements = $(dcHideOthersSelector);
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
