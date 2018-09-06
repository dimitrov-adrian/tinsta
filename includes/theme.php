<?php

/**
 * @file
 * Main theme setup
 */


/**
 * Prepare WordPress for the theme.
 */
add_action( 'after_setup_theme', function () {

  // First, need to load language, because some of the default options uses translations.
  load_theme_textdomain('tinsta', get_template_directory() . '/languages');

  register_nav_menus([
    'main' => __('Primary Site Menu', 'tinsta'),
  ]);

  //@TODO check for usages
  //add_image_size('tinsta_cover_small', 320, 200, true);
  //add_image_size('tinsta_cover', 1280, 450, true);

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

  // Nulling search forms
  if (get_theme_mod('system_page_search_disable_search')) {
    add_filter('get_search_form', '__return_null');
  }

});

/**
 * Init hook
 */
add_action('init', function () {
  // @TODO performance improvements.
  // Because we need to ensure all used theme mods had default values, and the filters like:
  // "default_option_theme_mods_{$theme_slug}" and "option_theme_mods_{$theme_slug}" doesn't do what expected to do...
  // Also set forced theme mods from tinsta_force_options filter.
  $theme_defaults = tinsta_get_options_defaults();
  $theme_mods = get_theme_mods();
  $forced_theme_mods = (array) apply_filters('tinsta_force_options', []);
  foreach ($theme_defaults as $mod_name => $mod_default_value) {

    if (isset($forced_theme_mods[$mod_name]) && (!isset($theme_mods[$mod_name]) || $forced_theme_mods[$mod_name] != $theme_mods[$mod_name])) {
      set_theme_mod($mod_name, $forced_theme_mods[$mod_name]);
      continue;
    }

    if (!isset($theme_mods[$mod_name]) && is_scalar($mod_default_value)) {
      set_theme_mod($mod_name, $mod_default_value);
    }

  }

  global $content_width;
  if (!empty($content_width)) {
    $content_width = sprintf('%d', get_theme_mod('region_root_width'));
  }

});

/**
 * Register widgets and sidebars.
 * 
 * should be on widgets_init but is_customize_preview() is not available then.
 * 
 */
add_action('widgets_init', function () {

  $is_customizer_preview = is_customize_preview();

  register_sidebar([
    'name' => __('Header', 'tinsta'),
    'description' => __('Display widgets in Header Region.', 'tinsta'),
    'id' => 'header',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="screen-reader-text">',
    'after_title' => '</div>',
  ]);

  register_sidebar([
    'name' => __('Before Main Content', 'tinsta'),
    'description' => __('Full width region between Header and Main Content of the page.', 'tinsta'),
    'id' => 'before-content',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

  register_sidebar([
    'name' => __('Front Page', 'tinsta'),
    'description' => __('Displayed before main content of home page (front-page).', 'tinsta'),
    'id' => 'frontpage',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

  if (get_theme_mod("system_page_404_content") == 'widgets' || $is_customizer_preview) {
    register_sidebar([
      'name' => __('Error 404', 'tinsta'),
      'id' => 'error-404',
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widgettitle">',
      'after_title' => '</div>',
    ]);
  }

  register_sidebar([
    'name' => __('Primary Sidebar', 'tinsta'),
    'description' => __('Primary Sidebar, usually displayed left to Main Content.', 'tinsta'),
    'id' => 'primary',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

  register_sidebar([
    'name' => __('Secondary Sidebar', 'tinsta'),
    'description' => __('Secondary Sidebar, usually displayed right to Main Content.', 'tinsta'),
    'id' => 'secondary',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

  register_sidebar([
    'name' => __('Before Entries', 'tinsta'),
    'description' => __('Displayed before entries or single entry, between the sidebars. Scoped in Main Content.', 'tinsta'),
    'id' => 'before-entries',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

  register_sidebar([
    'name' => __('After Entries', 'tinsta'),
    'description' => __('Displayed after entries or single entry, between the sidebars. Scoped in Main Content.', 'tinsta'),
    'id' => 'after-entries',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

  // Post type variants.
  foreach (get_post_types(['public' => true], 'objects') as $post_type) {

    // If twe are on customizer preview, then should register all sidebars, just to register it in customizer.
    if (get_theme_mod("post_type_{$post_type->name}_layout_archive") == 'widgets-area' || $is_customizer_preview) {
      register_sidebar([
        'name' => sprintf(__('Archive %s &mdash; Widgets Area Layout', 'tinsta'), $post_type->label),
        'description' => sprintf(__('Manage %s display, when displaying multiple entries, via Widgets.', 'tinsta'), $post_type->label),
        'id' => "post-layout-archive-widget-{$post_type->name}",
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        // Hide titles with screen-reader-text,
        // it will hide from users, but will show as alttext for readers.
        'before_title' => '<div class="screen-reader-text">',
        'after_title' => '</div>',
      ]);
    }
    
    // If twe are on customizer preview, then should register all sidebars, just to register it in customizer.
    if (get_theme_mod("post_type_{$post_type->name}_layout") == 'widgets-area' || $is_customizer_preview) {
      register_sidebar([
        'name' => sprintf(__('Single %s &mdash; Widgets Area Layout', 'tinsta'), $post_type->label),
        'description' => sprintf(__('Manage %s display, when displaying single entry, via Widgets.', 'tinsta'), $post_type->label),
        'id' => "post-layout-widget-{$post_type->name}",
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        // Hide titles with screen-reader-text,
        // it will hide from users, but will show as alttext for readers.
        'before_title' => '<div class="screen-reader-text">',
        'after_title' => '</div>',
      ]);
    }
  }

  register_sidebar([
    'name' => __('After Main Content', 'tinsta'),
    'description' => __('Full width region between Main Content and Footer of the page.', 'tinsta'),
    'id' => 'after-content',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

  register_sidebar([
    'name' => __('Footer', 'tinsta'),
    'description' => __('Display widgets in Footer Region.', 'tinsta'),
    'id' => 'footer',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);


  // Make tinsta-nav-menu-widget-area available as sidebar.
  $menu_sidebars = get_posts([
    'post_type' => 'nav_menu_item',
    'meta_query' => [
      [
        'key' => '_menu_item_type',
        'value' => 'tinsta-nav-menu-widget-area',
      ]
    ],
    'orderby' => 'title',
    'posts_per_page' => -1,
  ]);
  foreach ($menu_sidebars as $sidebar) {
    register_sidebar([
      'name' => sprintf(__('Menu "%s"', 'tinsta'), $sidebar->post_title),
      'description' => sprintf(__('Appears under the "%s" menu item as mega menu', 'tinsta'), $sidebar->post_title),
      'id' => 'tinsta-menu-' . $sidebar->post_name,
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widgettitle">',
      'after_title' => '</div>',
    ]);
  }

});

/**
 * Register widgets
 */
add_action('widgets_init', function () {

  require __DIR__ . '/widgets/breadcrumbs-widget.php';
  register_widget('Tinsta_BreadCrumbs_Widget');

  require __DIR__ . '/widgets/context-header-widget.php';
  register_widget('Tinsta_ContextHeader_Widget');

  require __DIR__ . '/widgets/logo-widget.php';
  register_widget('Tinsta_Logo_Widget');

  require __DIR__ . '/widgets/login-form-widget.php';
  register_widget('Tinsta_LoginForm_Widget');

  require __DIR__ . '/widgets/page-subnav-widget.php';
  register_widget('Tinsta_PageSubnav_Widget');

  require __DIR__ . '/widgets/user-profile-widget.php';
  register_widget('Tinsta_UserProfile_Widget');

  require __DIR__ . '/widgets/related-posts-widget.php';
  register_widget('Tinsta_RelatedPosts_Widget');

  require __DIR__ . '/widgets/page-content-widget.php';
  register_widget('Tinsta_PageContent_Widget');

});

/**
 * Prepare theme settings when building the stylesheets.
 *
 * @TODO may be need to filter those that are not intended to be accessible in SCSS
 * @TODO better sanitization
 */
add_filter('tinsta_get_stylesheet_args', function ($args = []) {

  $defaults = tinsta_get_options_defaults();
  $theme_mods = array_replace_recursive(get_theme_mods(), $args['variables']);

  // Process and sanitize default theme mods.
  foreach ($theme_mods as $name => $value) {
    if (isset($defaults[$name])) {

      // @TODO better sanitization.
      if (!is_scalar($value)) {
        // Should not have such variables.
        die('Incorrect type: ' . $name . ' = ' . print_r($value,1));
        continue;
      }

      $args['variables'][$name] = $value;

      // Seems to be color.
      if (substr($defaults[$name], 0, 1) == '#') {
        if (substr($value, 0, 1) != '#' || (strlen($value) != 4 && strlen($value) != 7)) {
          $args['variables'][$name] = $defaults[$name];
        }
      }

      // Sanitize numbers.
      elseif (is_numeric($defaults[$name])) {
        if (!is_numeric($value)) {
          $float_val = sprintf('%.2f', $value);
          $int_val = sprintf('%d', $value);
          if ($float_val == 0) {
            $args['variables'][$name] = $defaults[$name];
          } else {
            $args['variables'][$name] = $float_val != $int_val ? $float_val : $int_val;
          }
        }

      }

      // Do not escape units.
      elseif (preg_match('#^([\d\.])(px|pt|\%|em|rem||vv|vw|vh)$#', trim($value), $matches)) {

        // But if we have some like 0em, then convert to 0.
        if ($matches[0] === 0) {
          $args['variables'][$name] = 0;
        }

      }

      // Quote URLs.
      elseif (preg_match('#(http\:|^\/\/|\%)#', $value)) {
        $args['variables'][$name] = "'{$args['variables'][$name]}'";

      }

      elseif (!is_bool($args['variables'][$name]) && !empty($args['variables'][$name])) {

        //$args['variables'][$name] = "'{$args['variables'][$name]}'";
        //$args['variables'][$name] =
        // do nothing generic variables.

      }

      // Convert nulls and empties to null.
      elseif (empty($args['variables'][$name])) {
        $args['variables'][$name] = null;
      }

    }
  }

  // Sanitize values that should be transformed to comma separated list
  // Example:
  //  "\nword1;word2, word3\nword4\n,;" => 'word1, word2, word3, word4'
  // - typography_font_family
  // - typography_font_family_headings
  $list = [
    'typography_font_family',
    'typography_font_family_headings',
  ];
  foreach ($list as $name) {
    if (!empty($args['variables'][$name])) {

      // Break if quotes opening quotes doesn't match closing.
      if (substr_count($args['variables'][$name], '"') %2 || substr_count($args['variables'][$name], '\'') %2) {
        $args['variables'][$name] = $defaults[$name];
      }

      // Split also by \t \n , and ;
      $args['variables'][$name] = preg_split('#[\t\n\,\;\,]#', $args['variables'][$name]);
      foreach ($args['variables'][$name] as $index => $value) {
        $value = trim($value);
        if (strpos($value, ' ') !== false && ( $value{0} !== '"' && $value{0} !== '\'') ) {
          $value = "'" . addslashes($value) . "'";
        }
        $args['variables'][$name][$index] = $value;
      }
      $args['variables'][$name] = implode(', ', $args['variables'][$name]);

      // Strip C styled comments.
      $args['variables'][$name] = preg_replace('#\/\*(.|\n)*?\*\/#', '', $args['variables'][$name]);
    }

  }

  return $args;
});
