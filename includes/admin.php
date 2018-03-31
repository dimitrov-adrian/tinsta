<?php

/**
 * @file
 * Only hooks that are responsible for admin/back-end and NOT for front-end interface.
 */


/**
 * Import .tinsta settings file
 *
 * @param $file
 *
 * @return int
 */
function tinsta_settings_import($file)
{
  $imported_settings_count = 0;
  if ( file_exists($file) && is_readable($file) ) {
    $data = file_get_contents($file);
    $data = @json_decode($data, true);
    if ( ! json_last_error()) {
      foreach ($data as $key => $value) {
        set_theme_mod($key, $value);
        $imported_settings_count++;
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
 * @param string $widget_id   ID of the widget (search, recent-posts, etc.)
 * @param array $widget_data  Widget settings.
 * @param string $sidebar     ID of the sidebar.
 */
function tinsta_insert_widget_in_sidebar( $widget_id, $widget_data, $sidebar )
{
  // Retrieve sidebars, widgets and their instances
  $sidebars_widgets = get_option( 'sidebars_widgets', array() );
  $widget_instances = get_option( 'widget_' . $widget_id, array() );
  // Retrieve the key of the next widget instance
  $numeric_keys = array_filter( array_keys( $widget_instances ), 'is_int' );
  $next_key = $numeric_keys ? max( $numeric_keys ) + 1 : 2;
  // Add this widget to the sidebar
  if ( ! isset( $sidebars_widgets[ $sidebar ] ) ) {
    $sidebars_widgets[ $sidebar ] = array();
  }
  $sidebars_widgets[ $sidebar ][] = $widget_id . '-' . $next_key;
  // Add the new widget instance
  $widget_instances[ $next_key ] = $widget_data;
  // Store updated sidebars, widgets and their instances
  update_option( 'sidebars_widgets', $sidebars_widgets );
  update_option( 'widget_' . $widget_id, $widget_instances );
}

/**
 * Because add_theme_support( 'starter-content', []) doesn't works well,
 * we will populate some of the sidebars (if they are empty) with some random widgets.
 */
if (get_option('fresh_site')) {
  add_action('after_switch_theme', function () {

   if ( ! is_active_sidebar('header')) {
      tinsta_insert_widget_in_sidebar('tinsta_logo_widget', [], 'header');
      tinsta_insert_widget_in_sidebar('search', [], 'header');
    }

    if ( ! is_active_sidebar('footer')) {
      tinsta_insert_widget_in_sidebar('meta', [], 'footer');
      tinsta_insert_widget_in_sidebar('tag_cloud', [], 'footer');
      tinsta_insert_widget_in_sidebar('calendar', [], 'footer');
    }

    if ( ! is_active_sidebar('before-entries')) {
      tinsta_insert_widget_in_sidebar('tinsta_breadcrumbs_widget', [], 'before-entries');
    }

    if ( ! is_active_sidebar('primary')) {
      tinsta_insert_widget_in_sidebar('pages', [], 'primary');
    }

  });
}


/**
 * Setups and preparation for admin.
 */
add_action('admin_init', function () {

  if ( ! tinsta_check_stylesheet_cache_directory()) {
    add_action('admin_notices', function () {
      echo '
        <div class="error">
          <p> <strong>Tinsta:</strong> ' . sprintf(__('The directory <code>%s</code> MUST have write access.', 'tinsta'),
          WP_CONTENT_DIR . TINSTA_STYLESHEET_CACHE_DIR) . ' </p>
        </div>';
    });
  }

  // Build/rebuild only when in editing post.
  if ( get_current_screen() == 'edit' ) {
    add_editor_style(tinsta_get_stylesheet('typography'));
  }

  wp_enqueue_style('tinsta-admin', get_template_directory_uri() . '/assets/css/admin.css');

});

/**
 * Add admin theme pages.
 */
add_action('admin_menu', function () {

  add_theme_page(__('Tools', 'tinsta'), __('Tools', 'tinsta'), 'edit_theme_options', 'tinsta-tools', function () {
    require __DIR__ . '/tools.php';
  });

  add_theme_page(__('Support', 'tinsta'), __('Support', 'tinsta'), 'edit_theme_options', 'https://github.com/dimitrov-adrian/tinsta/issues');
});

/**
 * Hook to set tinsta related settings per widget.
 */
add_filter('widget_update_callback', function ($instance, $new_instance, $widget, $object) {

  $instance = wp_parse_args( (array) $new_instance, [
    'tinsta_widget_size_enable' => '',
    'tinsta_widget_size' => '',
  ]);
  return $instance;
}, 10, 4);

/**
 * Add tinsta related settings to widgets.
 */
add_action('in_widget_form', function ($object, &$return, $instance) {

  if ($object->number !== '__i__') {
    $sidebar = null;
    foreach (wp_get_sidebars_widgets() as $registered_sidebars => $sidebar_widgets ) {
      if ( in_array( $object->id, $sidebar_widgets ) ) {
        $sidebar = $registered_sidebars;
        break;
      }
    }

    if ( in_array( $sidebar, [ 'primary', 'secondary' ])) {
      return;
    }

  }

  // New fields are added.
  $return = null;

  $instance = wp_parse_args((array)$instance, [
    'tinsta_widget_size_enable' => '',
    'tinsta_widget_size' => 33,
  ]);
  ?>

  <fieldset class="tinsta-widget-fieldset">

    <legend>
        <?php _e('Tinsta', 'tinsta')?>
    </legend>

    <p>

      <input name="<?php echo $object->get_field_name('tinsta_widget_size_enable') ?>"
             type="checkbox"
             value="on"
        <?php checked($instance['tinsta_widget_size_enable'], 'on')?> />

      <label for="<?php echo $object->get_field_id('tinsta_widget_size') ?>">
        <?php _e('Width', 'tinsta') ?>
      </label>

      <input name="<?php echo $object->get_field_name('tinsta_widget_size') ?>"
             id="<?php echo $object->get_field_id('tinsta_widget_size') ?>"
             style="vertical-align: middle;"
             type="range"
             min="10"
             max="100"
             step="5"
             value="<?php echo esc_attr($instance['tinsta_widget_size'])?>"
      />

    </p>
  </fieldset>

  <?php
  return [$object, $return, $instance];

}, 10, 3);
