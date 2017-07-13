(function($) {
  'use strict';

  $(function() {
    // Code to fire when the DOM is ready.
    $('.wpbrigade-video-link').on('click', function(e) {
      e.preventDefault();
      var target = $(this).data('video-id');
      $('#' + target).fadeIn();
    });
    $('.wpbrigade-close-popup').on('click', function(e) {
      $(this).parent().parent().fadeOut();
      $('.wpbrigade-video-wrapper iframe').attr('src',
        'https://www.youtube.com/embed/GMAwsHomJlE');
    });

    $("#wpuf-loginpress_setting\\[enable_repatcha_promo\\]").on('click', function() {

      var promotion = $('#wpuf-loginpress_setting\\[enable_repatcha_promo\\]');
      if ( promotion.is(":checked") ) {
        $('tr.recapthca-promo-img').show();
      } else {
        $('tr.recapthca-promo-img').hide();
      }
    }); // on click promo checkbox.

    $("#wpuf-loginpress_setting\\[enable_privacy_policy\\]").on('click', function() {

      var privacy_editor = $('#wpuf-loginpress_setting\\[enable_privacy_policy\\]');
      if ( privacy_editor.is(":checked") ) {
        $('tr.privacy_policy').show();
      } else {
        $('tr.privacy_policy').hide();
      }
    }); // on click promo checkbox.

    $(window).on('load', function() {

      $('<tr class="recapthca-promo-img"><th class="recapthca-promo" colspan="2"><img src="' + loginpress_script.plugin_url + '/loginpress/img/promo/recaptcha_promo.png"><a class="recapthca-promo-link" href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&amp;utm_medium=recaptcha-settings&amp;utm_campaign=pro-upgrade" target="_blank"><span>Unlock Premium Feature</span></a></th></tr>').insertAfter( $(".enable_repatcha_promo").closest('tr') );

      var promotion = $('#wpuf-loginpress_setting\\[enable_repatcha_promo\\]');
      if ( promotion.is(":checked") ) {
        $('tr.recapthca-promo-img').show();
      }

      var privacy_editor = $('#wpuf-loginpress_setting\\[enable_privacy_policy\\]');
      if ( privacy_editor.is(":checked") ) {
        $('tr.privacy_policy').show();
      }
    }); // Window on load.

  });
})(jQuery); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.
