<?php

/**
 * @file
 * Only hooks that are responsible for admin/back-end and NOT for front-end interface.
 */


/**
 * Import .tinsta settings file (only local files)
 *
 * @param string $file
 * @param bool $tinsta_settings_only
 *
 * @return int
 */
function tinsta_settings_import($file, $tinsta_settings_only = true)
{
  $defaults = [];
  if ($tinsta_settings_only) {
    $defaults = tinsta_get_options_defaults();
  }

  $imported_settings_count = 0;

  require_once ABSPATH . '/wp-admin/includes/file.php';
  WP_Filesystem();
  global $wp_filesystem;

  if ($wp_filesystem->is_readable($file)) {

    $data = $wp_filesystem->get_contents($file);
    $data = @json_decode($data, true);

    if (!json_last_error()) {
      foreach ($data as $key => $value) {
        // Import ONLY the settings, described in tinsta_get_options_defaults().
        if ($tinsta_settings_only && !isset($defaults[$key])) {
          remove_theme_mod($key);
        }
        else {
          set_theme_mod($key, $value);
          $imported_settings_count++;
        }
      }
    }
  }

  return $imported_settings_count;
}


/**
 * Insert a widget in a sidebar.
 *
 * This function is copied from, all credits goes to "tyxla" as it's author
 * https://gist.github.com/tyxla/372f51ea1340e5e643f6b47e2ddf43f2
 *
 * @param string $widget_id ID of the widget (search, recent-posts, etc.)
 * @param array $widget_data Widget settings.
 * @param string $sidebar ID of the sidebar.
 */
function tinsta_insert_widget_in_sidebar($widget_id, $widget_data, $sidebar)
{
  // Retrieve sidebars, widgets and their instances
  $sidebars_widgets = get_option('sidebars_widgets', array());
  $widget_instances = get_option('widget_' . $widget_id, array());
  // Retrieve the key of the next widget instance
  $numeric_keys = array_filter(array_keys($widget_instances), 'is_int');
  $next_key = $numeric_keys ? max($numeric_keys) + 1 : 2;
  // Add this widget to the sidebar
  if (!isset($sidebars_widgets[$sidebar])) {
    $sidebars_widgets[$sidebar] = array();
  }
  $sidebars_widgets[$sidebar][] = $widget_id . '-' . $next_key;
  // Add the new widget instance
  $widget_instances[$next_key] = $widget_data;
  // Store updated sidebars, widgets and their instances
  update_option('sidebars_widgets', $sidebars_widgets);
  update_option('widget_' . $widget_id, $widget_instances);
}


//function tinsta_create_restore_point()
//{
//  $name = 'tinsta/' . date('YmdHis') . '/' . substr(md5(random_bytes(16)), 0, 2);
//  set_transient($name, get_theme_mods(), time() + 60*60*24*30);
//  return $name;
//}
//
//
//function tinsta_restore_from_point($name)
//{
//  $tr = get_transient($name);
//  delete_transient($name);
//  foreach (tinsta_get_options_defaults() as $key => $val) {
//    if (isset($tr[$key])) {
//      set_theme_mod($key, $tr[$key]);
//    }
//  }
//}


/**
 * Add theme exports AJAX endpoint
 * wp-admin/admin-ajax.php?action=tinsta-export-settings
 */
add_action('wp_ajax_tinsta-export-settings', function () {
  $filename = get_bloginfo('name') . '-' . date('YmdHi') . '.tinsta';
  if ( ! headers_sent() ) {
    header('Cache-Control: no-cache, must-revalidate', true);
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT', true);
    header('Content-Type: plain/text; charset=UTF-8', true);
    header('Content-Disposition: attachment; filename="' . addslashes($filename) . '"', true);
  }

  $document = array_merge( get_theme_mods(), [
    '$tinstaVersion' =>  wp_get_theme()->Version,
  ]);

  echo json_encode( $document, JSON_PRETTY_PRINT );
  exit;
});

/**
 * Because add_theme_support( 'starter-content', []) doesn't works well,
 * we will populate some of the sidebars (if they are empty) with some random widgets.
 */
if (get_option('fresh_site')) {
  add_action('after_switch_theme', function () {

    if (!is_active_sidebar('header')) {
      tinsta_insert_widget_in_sidebar('tinsta_logo_widget', [], 'header');
      tinsta_insert_widget_in_sidebar('search', [], 'header');
    }

    if (!is_active_sidebar('footer')) {
      tinsta_insert_widget_in_sidebar('meta', [], 'footer');
      tinsta_insert_widget_in_sidebar('tag_cloud', [], 'footer');
      tinsta_insert_widget_in_sidebar('calendar', [], 'footer');
    }

    if (!is_active_sidebar('before-entries')) {
      tinsta_insert_widget_in_sidebar('tinsta_breadcrumbs_widget', [], 'before-entries');
    }

    if (!is_active_sidebar('primary')) {
      tinsta_insert_widget_in_sidebar('pages', [], 'primary');
    }

  });
}


/**
 * Admin Scripts and Styles
 */
add_action('admin_print_scripts', function () {
  // Build/rebuild only when in editing post.
  if (get_current_screen() == 'edit') {
    add_editor_style(tinsta_get_stylesheet('typography'));
  }
  wp_enqueue_style('tinsta-admin', get_template_directory_uri() . '/assets/css/admin.css');
  wp_enqueue_script('tinsta-admin', get_template_directory_uri() . '/assets/scripts/admin.js', ['jquery']);
  wp_localize_script('tinsta-admin', 'tinsta', [
    'palette' => array_values(tinsta_get_color_palette()),
  ]);
});


/**
 * Add admin theme pages.
 */
add_action('admin_menu', function () {

  add_theme_page(__('Tools', 'tinsta'), __('Tools', 'tinsta'), 'edit_theme_options', 'tinsta-tools', function () {
    require __DIR__ . '/tools.php';
  });

});


/**
 * Hook to set tinsta related settings per widget.
 */
add_filter('widget_update_callback', function ($instance, $new_instance, $widget, $object) {

  $instance = wp_parse_args((array)$new_instance, [
    'tinsta_widget_size_enable' => '',
    'tinsta_widget_size' => '',
    'tinsta_boxed' => '',
  ]);
  return $instance;
}, 10, 4);


/**
 * Add tinsta related settings to widgets.
 */
add_action('in_widget_form', function ($object, &$return, $instance) {

  $sidebar = null;

  if ($object->number !== '__i__') {
    foreach (wp_get_sidebars_widgets() as $registered_sidebars => $sidebar_widgets) {
      if (in_array($object->id, $sidebar_widgets)) {
        $sidebar = $registered_sidebars;
        break;
      }
    }
  }

  if (!$sidebar) {
    return [$object, $return, $instance];
  }

  // New fields are added.
  $return = null;

  $instance = wp_parse_args((array)$instance, [
    'tinsta_boxed' => '',
    'tinsta_widget_size_enable' => '',
    'tinsta_widget_size' => 33,
  ]);

  ob_start();

  if (in_array($sidebar, ['before-content', 'after-content'])) {
    ?>
    <p>
      <input id="<?php echo $object->get_field_id('tinsta_boxed') ?>"
             name="<?php echo $object->get_field_name('tinsta_boxed') ?>"
             type="checkbox"
             value="on"
      <?php checked($instance['tinsta_boxed'], 'on') ?> />
      <label for="<?php echo $object->get_field_id('tinsta_boxed') ?>">
        <?php _e('Boxed', 'tinsta') ?>
      </label>
    </p>
    <?php
  }

  if (!in_array($sidebar, ['primary', 'secondary'])) {
    ?>
    <p>
      <input name="<?php echo $object->get_field_name('tinsta_widget_size_enable') ?>"
             type="checkbox"
             value="on"
        <?php checked($instance['tinsta_widget_size_enable'], 'on') ?> />
      <label for="<?php echo $object->get_field_id('tinsta_widget_size') ?>">
        <?php _e('Width', 'tinsta') ?>
      </label>
      <input name="<?php echo $object->get_field_name('tinsta_widget_size') ?>"
             id="<?php echo $object->get_field_id('tinsta_widget_size') ?>"
             style="vertical-align: middle; width: 4em;"
             type="number"
             min="10"
             max="100"
             step="1"
             value="<?php echo esc_attr($instance['tinsta_widget_size']) ?>"
      /> %
    </p>
    <?php
  }

  $tinsta_fields = ob_get_clean();

  if ($tinsta_fields) {
    ?>
    <fieldset class="tinsta-widget-fieldset">
      <legend>
        <?php _e('Layout', 'tinsta') ?>
      </legend>
      <?php echo $tinsta_fields?>
    </fieldset>
    <?php
  }

  return [$object, $return, $instance];
}, 10, 3);
