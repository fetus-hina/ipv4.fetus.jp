($ => {
  $('.table-sortable')
    .stupidtable()
    .on('aftertablesort', function (event, data) {
      const th = $(this).find('th');
      th.find('.arrow').remove();
      th.eq(data.column)
        .append(' ')
        .append(
          $('<span>')
            .addClass('arrow bi')
            .addClass(
              data.direction === $.fn.stupidtable.dir.ASC ? 'bi-arrow-up-short' : 'bi-arrow-down-short'
            )
        );
    });
})(jQuery);
