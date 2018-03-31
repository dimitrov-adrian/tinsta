<?php

/**
 * @file
 * Only hooks that are responsible for admin AJAX requests.
 */


/**
 * Export .tinsta settings file
 */
function tinsta_settings_export()
{
  $filename = get_bloginfo('name') . '-' . date('YmdHi') . '.tinsta';
  @header('Cache-Control: no-cache, must-revalidate');
  @header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
  @header('Content-Type: plain/text; charset=UTF-8');
  @header('Content-Disposition: attachment; filename="' . addslashes($filename) . '"');
  echo json_encode( get_theme_mods() );
  exit;
}

/**
 * Add theme exports AJAX endpoint
 * wp-admin/admin-ajax.php?action=tinsta-export-settings
 */
add_action('wp_ajax_tinsta-export-settings', function () {
  tinsta_settings_export();
});
