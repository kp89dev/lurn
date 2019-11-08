/**
 * @file
 * Facebook Likebox behaviors.
 */

Drupal.behaviors.fbLikebox = {
  attach: function (context, settings) {
    if (context !== document) {
      // AJAX request.
      return;
    }
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) {
        return;
      }
      js = d.createElement(s);
      js.id = id;
      js.src = "//connect.facebook.net/" + settings.fbLikeboxLanguage + "/sdk.js#xfbml=1&version=v2.5";
      if (settings.fb_likebox_app_id) {
          js.src += "&appId=" + settings.fbLikeboxAppId;
      }
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk', settings));
  }
};
