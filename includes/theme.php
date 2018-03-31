<?php

/**
 * @file
 * Main theme setup
 */


// Admin UI related.
if (is_admin()) {
  if (defined('DOING_AJAX') && DOING_AJAX) {
    require_once __DIR__ . '/admin-ajax.php';
  }
  require_once __DIR__ . '/admin.php';
}

// Front End related.
else {
  require_once __DIR__ . '/front-end.php';
}

// Customizer related.
if (is_customize_preview()) {
  require_once __DIR__ . '/customizer.php';
}

// Include Integrations
if ( TINSTA_ENABLE_INTEGRATIONS ) {

  // Include BBPress integrations.
  //if (function_exists('bbpress')) {
  //  require_once __DIR__ . '/bbpress.php';
  //}

  // Include WooCommerce integrations.
  //if (defined('WOOCOMMERCE_VERSION')) {
  //  require_once __DIR__ . '/woocommerce.php';
  //}

  // Include SiteOriginPanels integraions.
  //if (defined('SITEORIGIN_PANELS_VERSION')) {
  //  require_once __DIR__ . '/siteorigin-panels.php';
  //}

  // Include JetPack integrations.
  //if (defined('JETPACK__VERSION')) {
  //   require_once __DIR__ . '/jetpack.php';
  //}

  // require_once __DIR__ . '/widget-logic.php';

}

/**
 * Prepare WordPress for the theme.
 */
add_action('after_setup_theme', function () {

  // First, need to load language, because some of the default options uses translations.
  load_theme_textdomain('tinsta', get_template_directory() . '/languages');

  register_nav_menus([
    'main' => __('Primary Site Menu', 'tinsta'),
  ]);

  add_image_size('tinsta_cover_small', 320, 200, true);
  add_image_size('tinsta_cover', 1280, 450, true);

  add_theme_support('title-tag');
  add_theme_support('automatic-feed-links');
  add_theme_support('post-thumbnails');
  add_theme_support('custom-logo');
  add_theme_support('html5', [
    'comment-list',
    'comment-form',
    'search-form',
    'gallery',
    'caption',
  ]);

  // Because we need to ensure all used theme mods had default values, and the filters like:
  // "default_option_theme_mods_{$theme_slug}" and "option_theme_mods_{$theme_slug}" doesn't do what expected to do...
  // Also set forced theme mods from tinsta_force_options filter.
  $theme_defaults = tinsta_get_options_defaults();
  $theme_mods     = get_theme_mods();
  $forced_theme_mods = apply_filters('tinsta_force_options', []);
  foreach ($theme_defaults as $mod_name => $mod_default_value) {

    if ( isset( $forced_theme_mods[$mod_name] ) && ( ! isset($theme_mods[$mod_name]) || $forced_theme_mods[$mod_name] != $theme_mods[$mod_name] ) ) {
      set_theme_mod($mod_name, $forced_theme_mods[$mod_name]);
      continue;
    }

    if ( ! isset($theme_mods[$mod_name]) && is_scalar($mod_default_value)) {
      set_theme_mod($mod_name, $mod_default_value);
    }

  }

  global $content_width;
  if ( ! empty($content_width)) {
    $content_width = sprintf('%d', get_theme_mod('section_root_width'));
  }

});

/**
 * Register widgets and sidebars.
 */
add_action('init', function () {

  $is_customizer_preview = is_customize_preview();

  register_sidebar([
    'name' => __('Header', 'tinsta'),
    'id' => 'header',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="screen-reader-text">',
    'after_title' => '</div>',
  ]);

  register_sidebar([
    'name' => __('Front-page', 'tinsta'),
    'id' => 'frontpage',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

  register_sidebar([
    'name' => __('Error 404', 'tinsta'),
    'id' => 'error-404',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

  // Post type variants.

  foreach (get_post_types(['public' => true], 'objects') as $post_type) {

    foreach (tinsta_get_post_type_sidebar_names() as $name => $label) {


      // Global.
      register_sidebar([
        'name' => $label,
        'id' => $name,
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<div class="widgettitle">',
        'after_title' => '</div>',
      ]);

      $setting = get_theme_mod("post_type_{$post_type->name}_sidebars_{$name}");

      // Shared for post type
      // If twe are on customizer preview, then should register all sidebars, just to register it in customizer.
      if ($setting == 'shared' || $is_customizer_preview) {
        register_sidebar([
          'name' => sprintf('%s - %s', $label, $post_type->label),
          'id' => "{$name}-{$post_type->name}",
          'before_widget' => '<div id="%1$s" class="widget %2$s">',
          'after_widget' => '</div>',
          'before_title' => '<div class="widgettitle">',
          'after_title' => '</div>',
        ]);
      }

      // Separated for post type
      // If twe are on customizer preview, then should register all sidebars, just to register it in customizer.
      if ($setting == 'separated' || $is_customizer_preview) {

        register_sidebar([
          'name' => sprintf('%s - %s - %s', $label, $post_type->label, __('Single', 'tinsta')),
          'id' => "{$name}-{$post_type->name}-single",
          'before_widget' => '<div id="%1$s" class="widget %2$s">',
          'after_widget' => '</div>',
          'before_title' => '<div class="widgettitle">',
          'after_title' => '</div>',
        ]);

        register_sidebar([
          'name' => sprintf('%s - %s - %s', $label, $post_type->label, __('Archive', 'tinsta')),
          'id' => "{$name}-{$post_type->name}-archive",
          'before_widget' => '<div id="%1$s" class="widget %2$s">',
          'after_widget' => '</div>',
          'before_title' => '<div class="widgettitle">',
          'after_title' => '</div>',
        ]);

      }

    }

    // If twe are on customizer preview, then should register all sidebars, just to register it in customizer.
    if (get_theme_mod("post_type_{$post_type->name}_layout") || $is_customizer_preview) {
      register_sidebar([
        'name' => sprintf(__('Widget Post Layout - %s', 'tinsta'), $post_type->label),
        'id' => "post-layout-widget-{$post_type->name}",
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<!-- Element: ',
        'after_title' => ' -->',
      ]);
    }

  }

  register_sidebar([
    'name' => __('Footer', 'tinsta'),
    'id' => 'footer',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

});

add_action('widgets_init', function () {

  require_once __DIR__ . '/widgets/breadcrumbs-widget.php';
  register_widget('Tinsta_BreadCrumbs_Widget');

  require_once __DIR__ . '/widgets/context-header-widget.php';
  register_widget('Tinsta_ContextHeader_Widget');

  require_once __DIR__ . '/widgets/logo-widget.php';
  register_widget('Tinsta_Logo_Widget');

  require_once __DIR__ . '/widgets/login-form-widget.php';
  register_widget('Tinsta_LoginForm_Widget');

  require_once __DIR__ . '/widgets/page-subnav-widget.php';
  register_widget('Tinsta_PageSubnav_Widget');

  require_once __DIR__ . '/widgets/user-menu-widget.php';
  register_widget('Tinsta_UserMenu_Widget');

  require_once __DIR__ . '/widgets/related-posts-widget.php';
  register_widget('Tinsta_RelatedPosts_Widget');

});

/**
 * Prepare theme settings when building the stylesheets.
 *
 * @TODO may be need to filter those that are not intended to be accessible in SCSS
 * @TODO better sanitization
 */
add_filter('tinsta_get_stylesheet_args', function ($args) {

  $defaults   = tinsta_get_options_defaults();
  $theme_mods = get_theme_mods();

  // Process and sanitize default theme mods.
  foreach ($theme_mods as $name => $value) {
    if (isset($defaults[$name])) {

      // @TODO better sanitization.
      if ( ! is_scalar($value)) {
        continue;
      }

      $args['variables'][$name] = $value;

      // Seems to be color.
      if (substr($defaults[$name], 0, 1) == '#') {
        if (substr($value, 0, 1) != '#' || (strlen($value) != 4 && strlen($value) != 7)) {
          $args['variables'][$name] = $defaults[$name];
        }
      } // Sanitize numbers.
      elseif (is_numeric($defaults[$name])) {
        if ( ! is_numeric($value)) {
          $float_val = sprintf('%.2f', $value);
          $int_val   = sprintf('%d', $value);
          if ($float_val == 0) {
            $args['variables'][$name] = $defaults[$name];
          } else {
            $args['variables'][$name] = $float_val != $int_val ? $float_val : $int_val;
          }
        }

        // Do not escape units
      } elseif (preg_match('#^([\d\.])(px|\%|em|pt|vv|rem|vw|vh)$#', trim($value))) {
        // do nothing

      } elseif (preg_match('#(http\:|^\/\/|\%)#', $value)) {
        $args['variables'][$name] = "'{$args['variables'][$name]}'";

      } elseif ( ! is_bool($args['variables'][$name]) && ! empty($args['variables'][$name])) {
        //$args['variables'][$name] = "'{$args['variables'][$name]}'";
        //$args['variables'][$name] =
      } elseif ( empty($args['variables'][$name]) ) {
        $args['variables'][$name] = null;
      }

    }
  }

  // Unitize the breakpoints.
  foreach (['section_root_breakpoint_desktop', 'section_root_breakpoint_tablet', 'section_root_breakpoint_mobile'] as $breakpoint) {
    if (empty($vars[$breakpoint]) || ! is_numeric($vars[$breakpoint])) {
      $args['variables'][$breakpoint] = $defaults[$breakpoint];
    }
  }

  return $args;
}, 5);
