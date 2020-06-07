(function ($) {
  Drupal.behaviors.aa_core__usertweaks = {
    attach: function (context, drupalSettings) {
      console.log(drupalSettings);
      $("nav ul.tabs--primary").addClass("is-expanded");
    },
  };
})(jQuery);
