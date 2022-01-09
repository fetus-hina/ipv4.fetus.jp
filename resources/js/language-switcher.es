jQuery($ => {
  $('.language-switcher').click(function () {
    const $this = $(this);
    $
      .ajax(
        '/site/switch-language',
        {
          data: {
            language: $this.attr('data-language')
          },
          method: 'POST'
        }
      )
      .done(() => {
        window.location.reload();
      });

    return false;
  });
});
