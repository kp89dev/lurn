(function ($, Drupal) {

  $('.poll-existing-choice').on('focus', function (event) {
    $(document).once('poll-existing-choice').each(function () {
      $(Drupal.theme('pollChoiceDeleteWarning')).insertBefore($('#choice-values')).hide().fadeIn('slow');
    });
  });

  $.extend(Drupal.theme, /** @lends Drupal.theme */{

    /**
     * @return {string}
     *   Markup for the warning.
     */
    pollChoiceDeleteWarning: function () {
      return '<div class="messages messages--warning" role="alert">' + Drupal.t('* Deleting a choice will delete the votes on it!') + '</div>';
    }
  });

})(jQuery, Drupal);
