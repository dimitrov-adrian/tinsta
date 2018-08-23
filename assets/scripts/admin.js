(function($) {

  // Override Iris palette.
  if (window.hasOwnProperty('tinsta') && tinsta.hasOwnProperty('palette')) {
    $(document).ready(function() {
      if (window.hasOwnProperty('jQuery') && jQuery.hasOwnProperty('wp') && jQuery.wp.hasOwnProperty('wpColorPicker')) {
        jQuery.wp.wpColorPicker.prototype.options.palettes = tinsta.palette;
      }
    });
  }

}(jQuery));
