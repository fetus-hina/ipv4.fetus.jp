jQuery($ => {
  $('[data-bs-toggle="tooltip"]').each(function () {
    const $this = $(this);
    $this.data('tooltip', new bootstrap.Tooltip($this));
  });
});
