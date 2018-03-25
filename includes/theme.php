<?php


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
  add_theme_support('post-formats');
  add_theme_support('custom-logo');
  add_theme_support('customize-selective-refresh-widgets');
  add_theme_support('html5', [
    'comment-list',
    'comment-form',
    'search-form',
    'gallery',
    'caption',
  ]);

  // This seems to be limited for very few widget types and only from core, but is some start at least.
  add_theme_support( 'starter-content', [
    'widgets' => [
      'header' => [
        'tinsta_logo_widget',
        'search',
      ],
      'sidebar-post' => [
        'archive',
      ],
      'footer' => [
        'meta',
        'tag_cloud',
        'calendar',
      ],
    ],
  ]);

  // Because we need to ensure all used theme mods had default values, and the filters like:
  // "default_option_theme_mods_{$theme_slug}" and "option_theme_mods_{$theme_slug}" doesn't do what expected to do...
  $theme_defaults = tinsta_get_options_defaults();
  $theme_mods = get_theme_mods();
  foreach ($theme_defaults as $mod_name => $mod_default_value) {
    if (!isset($theme_mods[$mod_name]) && is_scalar($mod_default_value)) {
      set_theme_mod($mod_name, $mod_default_value);
    }
  }

  global $content_width;
  if (!empty($content_width)) {
    $content_width = sprintf('%d', get_theme_mod('site_wrapper_width'));
  }

  if (!defined('TINSTA_DISABLE_INTEGRATIONS')) {
    require_once __DIR__ . '/integrations/index.php';
  }

});


// @todo add them via widgets_init
add_action('init', function () {

  register_sidebar([
    'name' => __('Header', 'tinsta'),
    'id' => 'header',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="screen-reader-text">',
    'after_title' => '</div>',
  ]);

//  register_sidebar([
//    'name' => sprintf('%s - %s', __('Front-page', 'tinsta'), __('Top', 'tinsta')),
//    'id' => 'frontpage-full',
//    'before_widget' => '<div id="%1$s" class="widget %2$s">',
//    'after_widget' => '</div>',
//    'before_title' => '<div class="widgettitle">',
//    'after_title' => '</div>',
//  ]);
//
//  register_sidebar([
//    'name' => __('Front-page', 'tinsta'),
//    'id' => 'frontpage',
//    'before_widget' => '<div id="%1$s" class="widget %2$s">',
//    'after_widget' => '</div>',
//    'before_title' => '<div class="widgettitle">',
//    'after_title' => '</div>',
//  ]);
//
//  register_sidebar([
//    'name' => sprintf('%s - %s', __('Front-page', 'tinsta'), __('Left', 'tinsta')),
//    'id' => 'frontpage-primary',
//    'before_widget' => '<div id="%1$s" class="widget %2$s">',
//    'after_widget' => '</div>',
//    'before_title' => '<div class="widgettitle">',
//    'after_title' => '</div>',
//  ]);
//
//  register_sidebar([
//    'name' => sprintf('%s - %s', __('Front-page', 'tinsta'), __('Right', 'tinsta')),
//    'id' => 'frontpage-secondary',
//    'before_widget' => '<div id="%1$s" class="widget %2$s">',
//    'after_widget' => '</div>',
//    'before_title' => '<div class="widgettitle">',
//    'after_title' => '</div>',
//  ]);

  register_sidebar([
    'name' => __('Error 404', 'tinsta'),
    'id' => 'error-404',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

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


add_filter('body_class', function ($classes, $class) {

  if (get_theme_mod('site_height_full')) {
    $classes[] = 'full-height';
  }

  if (get_theme_mod('header_sticky')) {
    $classes[] = 'sticky-header';
  }

  if (get_theme_mod('header_disable')) {
    $classes[] = 'disable-header';
  }

  if (get_theme_mod('effects')) {
    $classes[] = 'effects';
  }

  if (get_theme_mod('site_layout_boxed')) {
    $classes[] = 'boxed';
  }

  // Add class of hfeed to non-singular pages.
  if (!is_singular()) {
    $classes[] = 'hfeed';
  }

  return array_diff($classes, [
    'single',
    'archive',
  ]);

}, 10, 2);


// Override read more string
add_filter('excerpt_more', function($read_more = '') {
  $mod_read_more = get_theme_mod('excerpt_more', $read_more);
  if ($mod_read_more) {
    $read_more = ' ' . $mod_read_more;
  }
  return $read_more;
});


// @TODO may be need to filter those that are not intended to be accessible in SCSS
// @TODO better sanitization
add_filter('tinsta_get_stylesheet', function ($args) {

  $defaults = tinsta_get_options_defaults();
  $theme_mods = get_theme_mods();

  // Process and sanitize default theme mods.
  foreach ($theme_mods as $name => $value) {
    if (isset($defaults[$name])) {

      // @TODO better sanitization.
      if (!is_scalar($value)) {
        continue;
      }

      $args['variables'][$name] = $value;

      // Seems to be color.
      if (substr($defaults[$name], 0, 1) == '#') {
        if (substr($value, 0, 1) != '#' || ( strlen($value) != 4 && strlen($value) != 7 ) ) {
          $args['variables'][$name] = $defaults[$name];
        }
      }

      // Sanitize numbers.
      if (is_numeric($defaults[$name])) {
        if (!is_numeric($value)) {
          $float_val = sprintf('%.2f', $value);
          $int_val = sprintf('%d', $value);
          if ($float_val == 0) {
            $args['variables'][$name] = $defaults[$name];
          }
          else {
            $args['variables'][$name] = $float_val != $int_val ? $float_val : $int_val;
          }
        }

      }

    }
  }

  // Unitize the breakpoints.
  foreach ([ 'breakpoint_desktop', 'breakpoint_tablet', 'breakpoint_mobile' ] as $breakpoint) {
    if (empty($vars[$breakpoint]) || !is_numeric($vars[$breakpoint])) {
      $args['variables'][$breakpoint] = $defaults[$breakpoint];
    }
    $args['variables'][$breakpoint] .= 'px';
  }

  return $args;
}, 5);


// Customizer Preview settings patches.
add_filter('tinsta_get_stylesheet', function ($args) {
  if (is_customize_preview()) {
    global $wp_customize;
    // @TODO optimize.
    $customizer_patched_data_values = $wp_customize->unsanitized_post_values();
    if ($wp_customize->validate_setting_values($customizer_patched_data_values)) {
      $customizer_patched_data_values = array_intersect_key($customizer_patched_data_values, tinsta_get_options_defaults());
      $args['variables'] = array_replace_recursive($args['variables'], $customizer_patched_data_values);
    }
    $args['preview'] = true;
  }
  return $args;
}, 1000);


add_action('wp_head', function () {
  echo get_theme_mod('header_markup');
  if (is_singular() && pings_open()) {
    printf('<link rel="pingback" href="%s">' . "\n", get_bloginfo('pingback_url'));
  }
});


add_action('wp_footer', function () {
  echo get_theme_mod('footer_markup');
  if (get_theme_mod('site_agreement_enable')) {
    get_template_part('template-parts/misc/agreement');
  }
});


add_action('wp_enqueue_scripts', function () {

  $fonts_google = [];

  if (get_theme_mod('font_google')) {
    $fonts_google[] = urldecode(trim(get_theme_mod('font_google')));
  }

  if (get_theme_mod('font_headings_google')) {
    $fonts_google[] = urldecode(trim(get_theme_mod('font_headings_google')));
  }

  if ($fonts_google) {
    $fonts_google = implode('|', array_map('urlencode', $fonts_google));
    wp_enqueue_style('tinsta-google-fonts', "//fonts.googleapis.com/css?family={$fonts_google}", [], null);
  }

  // Disable the ugly WP styles for recent comments widget.
  add_filter('show_recent_comments_widget_style', '__return_false');

  // Enqueue stylesheets.
  $stylesheet = tinsta_get_stylesheet(get_template_directory() . '/assets/scss/style');
  wp_enqueue_style('tinsta-stylesheet', $stylesheet, [], md5(serialize(get_transient('tinsta_theme'))));

});


add_action('admin_init', function () {
  if (!tinsta_check_stylesheet_cache_directory()) {
    add_action('admin_notices', function() {
      echo '
        <div class="error">
          <p> '. sprintf(__('<strong>Tinsta:</strong> The directory <code>%s</code> MUST have write access.', 'tinsta'), WP_CONTENT_DIR . TINSTA_STYLESHEET_CACHE_DIR) . ' </p>
        </div>';
    });
  }
});


add_action('admin_menu', function() {
  add_theme_page(__('Tools', 'tinsta'), __('Tools', 'tinsta'), 'edit_theme_options', 'tools', function() {
    require __DIR__ . '/tools.php';
  });
});

add_action('wp_ajax_tinsta-export-settings', function() {
  tinsta_settings_export();
});


add_action('wp_enqueue_scripts', function () {

  // Legacy supports.
  if (get_theme_mod('legacy_support')) {

    wp_enqueue_script('jquery');

    // https://github.com/jonathantneal/flexibility
    wp_enqueue_script('flexibility', get_template_directory_uri() . '/assets/scripts/flexibility.js', [], '2.0.1');
    wp_script_add_data('flexibility', 'conditional', 'let IE 10');

    // https://github.com/wilddeer/stickyfill
    wp_enqueue_script('stickyfill', get_template_directory_uri() . '/assets/scripts/stickyfill.min.js', [], '2.0.3');
    wp_script_add_data('stickyfill', 'conditional', 'lte IE 11');

    // https://github.com/aFarkas/html5shiv
    wp_enqueue_script('html5shiv', get_template_directory_uri() . '/assets/scripts/html5shiv.min.js', [], '3.7.3');
    wp_script_add_data('html5shiv', 'conditional', 'lte IE 9');

    // https://github.com/corysimmons/selectivizr2
    wp_enqueue_script('selectivizr', get_template_directory_uri() . '/assets/scripts/selectivizr2.min.js', ['jquery'], '1.0.9');
    wp_script_add_data('selectivizr', 'conditional', 'lte IE 9');

    // https://github.com/LeaVerou/prefixfree
    wp_enqueue_script('prefixfree', get_template_directory_uri() . '/assets/scripts/prefixfree.min.js', [], '1.0.7');
  }

  // Theme's script.
  wp_enqueue_script('tinsta', get_template_directory_uri() . '/assets/scripts/main.js', [], wp_get_theme()->get('Version'), true);
  wp_localize_script('tinsta', 'tinsta', [
    'menuLabel' => __('Menu', 'tinsta'),
    'closeLabel' => __('Close', 'tinsta'),
    'top' => __('Top', 'tinsta'),
    'scrolltop' => get_theme_mod('scrolltop'),
    'breakpoints' => [
      'desktop' => get_theme_mod('breakpoint_desktop'),
      'tablet' => get_theme_mod('breakpoint_tablet'),
      'mobile' => get_theme_mod('breakpoint_mobile'),
    ],
  ]);

  // Comment respond form reply script.
  if (is_singular() && comments_open()) {
    wp_enqueue_script('comment-reply');
  }

});


add_action('customize_controls_enqueue_scripts', function() {
  wp_enqueue_style('tinsta-admin-customizer', get_template_directory_uri() . '/assets/css/admin.css');
});



// Theming login pages.
//
// Theming modes:
//  brand     - only titles, colors and logo
//  full      - integrate into theme
add_action('login_init', function () {

  $login_integration_mode = get_theme_mod('login_integration_mode');

  // Disable for interim login.
  if (isset($_REQUEST['interim-login']) || isset($_POST['interim-login']) || isset($_GET['interim-login'])) {
    $login_integration_mode = 'brand';
  }

  // Brand mode
  if ($login_integration_mode == 'brand') {

    add_action('login_head', function () {
      ?>
      <style>
        #login h1 a {
          background: none;
          width: auto;
          display: block;
          text-indent: 0;
          height: auto;
          font-size: 48px;
          line-height: 1em;
          padding: 0;
          margin: 0;
        }
        #login .logo-site-description {
          font-size: 12px;
          line-height: 24px;
        }
      </style>
      <?php
    });

    add_filter('login_headerurl', function () {
      return home_url();
    });

    add_filter('login_headertitle', function () {
      ob_start();
      if (get_theme_mod('custom_logo') && get_custom_logo()) {
        the_custom_logo();
      }
      else {
        bloginfo('blogname');
        if (get_theme_mod('header_textcolor') !== 'blank') {
          echo '<div class="logo-site-description">';
          bloginfo('description');
          echo '</div>';
        }
      }
      return ob_get_clean();
    });

  }

  // Full mode.
  elseif ($login_integration_mode == 'full') {

    wp_deregister_style('login');

    add_filter('login_body_class', 'get_body_class');

    add_filter('login_headerurl', '__return_null');
    add_filter('login_headertitle', '__return_null');

    add_action('login_head', function () {
      wp_meta();
      wp_head();
    });

    add_action('login_header', function () {
      locate_template('template-parts/misc/header.php', true);
    });

    add_action('login_footer', function () {
      locate_template('template-parts/misc/footer.php', true);
    });
  }

});


// Register setting controls
add_action('customize_register', function ($wp_customize) {

  /** @var $wp_customize \WP_Customize_Manager */

  // Remove built-in color customizer.
  $wp_customize->remove_section('colors');

  if (!tinsta_is_customizer_enabled()) {
    return;
  }

  // Register all theme mods as settings in customizer.
  foreach (tinsta_get_options_defaults() as $option_name => $value) {
    $wp_customize->add_setting($option_name, [
      'type' => 'theme_mod',
      'default' => $value,
      'transport' => 'refresh',
      // @TODO add sanitization
      // @TODO add validation
    ]);
  }

  // Typography
  $wp_customize->add_section('tinsta_appearance_typography', [
    'title' => __('Typography', 'tinsta'),
    'priority' => 20,
  ]);
  $wp_customize->add_control('font_size', [
    'label' => __('Base font size (px)', 'tinsta'),
    'section' => 'tinsta_appearance_typography',
    'type' => 'number',
    'input_attrs' => [
      'min' => 10,
      'max' => 20,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('font_headings_style', [
    'label' => __('Headings Style', 'tinsta'),
    'section' => 'tinsta_appearance_typography',
    'type' => 'select',
    'choices' => [
      '' => __('Default', 'tinsta'),
      'uppercase' => __('Uppercase', 'tinsta'),
      'all-small-caps' => __('All Small Caps', 'tinsta'),
      'small-caps' => __('Small Caps', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('font_family', [
    'label' => __('Base font family', 'tinsta'),
    'section' => 'tinsta_appearance_typography',
    'type' => 'textarea',
  ]);
  $wp_customize->add_control('font_family_headings', [
    'label' => sprintf(__('(Headings) %s', 'tinsta'), __('Base font family', 'tinsta')),
    'section' => 'tinsta_appearance_typography',
    'type' => 'textarea',
  ]);
  $wp_customize->add_control('font_google', [
    'label' => __('Google Font', 'tinsta'),
    'section' => 'tinsta_appearance_typography',
    'description' => sprintf(__('Use font name from %s', 'tinsta'), '<a target="_blank" href="https://fonts.google.com/">google fonts</a>'),
  ]);
  $wp_customize->add_control('font_headings_google', [
    'label' => sprintf(__('(Headings) %s', 'tinsta'), __('Google Font', 'tinsta')),
    'section' => 'tinsta_appearance_typography',
    'description' => sprintf(__('Use font name from %s', 'tinsta'), '<a target="_blank" href="https://fonts.google.com/">google fonts</a>'),
  ]);
  $wp_customize->add_control('text_wordbreak', [
    'label' => __('Word-break', 'tinsta'),
    'section' => 'tinsta_appearance_typography',
    'type' => 'checkbox',
  ]);
  $wp_customize->add_control('text_justify', [
    'label' => __('Justify Text', 'tinsta'),
    'section' => 'tinsta_appearance_typography',
    'type' => 'checkbox',
  ]);
  $wp_customize->add_control('form_spacing', [
    'label' => __('Forms field spacing', 'tinsta'),
    'section' => 'tinsta_appearance_typography',
    'type' => 'number',
    'input_attrs' => [
      'min' => 0,
      'max' => 30,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('form_borders', [
    'label' => __('Forms field borders', 'tinsta'),
    'section' => 'tinsta_appearance_typography',
    'type' => 'number',
    'input_attrs' => [
      'min' => 0,
      'max' => 4,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('form_button_style', [
    'label' => __('Button Style', 'tinsta'),
    'section' => 'tinsta_appearance_typography',
    'type' => 'select',
    'choices' => [
      '' => __('None', 'tinsta'),
      'fill' => __('Fill', 'tinsta'),
      'border' => __('Border', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('font_button_text_style', [
    'label' => __('Button Text Style', 'tinsta'),
    'section' => 'tinsta_appearance_typography',
    'type' => 'select',
    'choices' => [
      '' => __('Default', 'tinsta'),
      'uppercase' => __('Uppercase', 'tinsta'),
      'all-small-caps' => __('All Small Caps', 'tinsta'),
      'small-caps' => __('Small Caps', 'tinsta'),
    ],
  ]);

  /*
   * Sections:
   */
  $wp_customize->add_panel('tinsta_sections', [
    'title' => __('Sections', 'tinsta'),
    'description' => __('Setup section appearances like colors, background and behavior.', 'tinsta'),
    'priority' => 25,
  ]);

  // Section: Globals
  $wp_customize->add_section('tinsta_sections_globals', [
    'title' => __('Globals', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('site_layout_boxed', [
    'type' => 'checkbox',
    'label' => __('Use boxed layout', 'tinsta'),
    'section' => 'tinsta_sections_globals',
  ]);
  $wp_customize->add_control('site_height_full', [
    'type' => 'checkbox',
    'label' => __('Full-height page', 'tinsta'),
    'section' => 'tinsta_sections_globals',
  ]);
  $wp_customize->add_control('site_wrapper_width', [
    'label' => __('Width', 'tinsta'),
    'section' => 'tinsta_sections_globals',
    'type' => 'number',
    'input_attrs' => [
      'min' => 600,
      'max' => 1440,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('breakpoint_desktop', [
    'label' => __('Desktop breakpoint (px)', 'tinsta'),
    'section' => 'tinsta_sections_globals',
    'type' => 'select',
    'choices' => [
      1920 => 1920,
      1600 => 1600,
      1400 => 1400,
      1200 => 1200,
      1024 => 1024,
    ],
  ]);
  $wp_customize->add_control('breakpoint_tablet', [
    'label' => __('Tablet breakpoint (px)', 'tinsta'),
    'section' => 'tinsta_sections_globals',
    'type' => 'select',
    'choices' => [
      960 => 960,
      920 => 920,
      800 => 800,
      720 => 720,
    ],
  ]);
  $wp_customize->add_control('breakpoint_mobile', [
    'label' => __('Mobile breakpoint (px)', 'tinsta'),
    'section' => 'tinsta_sections_globals',
    'type' => 'select',
    'choices' => [
      640 => 640,
      568 => 568,
      480 => 480,
      320 => 320,
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_primary', [
    'label' => __('Primary Color', 'tinsta'),
    'section' => 'tinsta_sections_globals',
  ]));
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_primary_inverted', [
    'label' => sprintf(__('(Inverted) %s', 'tinsta'), __('Primary Color', 'tinsta')),
    'section' => 'tinsta_sections_globals',
  ]));
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_secondary', [
    'label' => __('Secondary Color', 'tinsta'),
    'section' => 'tinsta_sections_globals',
  ]));
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_secondary_inverted', [
    'label' => sprintf(__('(Inverted) %s', 'tinsta'), __('Secondary Color', 'tinsta')),
    'section' => 'tinsta_sections_globals',
  ]));
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'site_color_background', [
    'label' => __('Background', 'tinsta'),
    'section' => 'tinsta_sections_globals',
  ]));
  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'site_body_image', [
    'label' => __('Background Image', 'tinsta'),
    'section' => 'tinsta_sections_globals',
  ]));
  $wp_customize->add_control(new WP_Customize_Background_Position_Control($wp_customize, 'site_body_image_background_position', [
    'label' => __('Background Image Position', 'tinsta'),
    'section' => 'tinsta_sections_globals',
    'settings' => [
      'x' => 'site_body_image_position_x',
      'y' => 'site_body_image_position_y',
    ],
  ]));
  $wp_customize->add_control('site_body_image_size', [
    'label' => __('Background Image Size', 'tinsta'),
    'section' => 'tinsta_sections_globals',
    'type' => 'select',
    'choices' => [
      'auto' => __('Original', 'tinsta'),
      'contain' => __('Fit to Screen', 'tinsta'),
      'cover' => __('Fill Screen', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('site_body_image_repeat', [
    'label' => __('Repeat Background Image', 'tinsta'),
    'section' => 'tinsta_sections_globals',
    'type' => 'checkbox',
  ]);
  $wp_customize->add_control('site_body_image_attachment_scroll', [
    'label' => __('Scroll with Page', 'tinsta'),
    'section' => 'tinsta_sections_globals',
    'type' => 'checkbox',
  ]);

  // Section: Topline
  $wp_customize->add_section('tinsta_sections_topline', [
    'title' => __('Topline', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('topline_sticky', [
    'type' => 'checkbox',
    'label' => __('Sticky', 'tinsta'),
    'section' => 'tinsta_sections_topline',
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'topline_color_background', [
    'label' => __('Background', 'tinsta'),
    'section' => 'tinsta_sections_topline',
  ]));
  $wp_customize->add_control('topline_color_background_opacity', [
    'label' => __('Opacity', 'tinsta'),
    'section' => 'tinsta_sections_topline',
    'type' => 'number',
    'input_attrs' => [
      'min' => 0,
      'max' => 100,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'topline_color_foreground', [
    'label' => __('Foreground', 'tinsta'),
    'section' => 'tinsta_sections_topline',
  ]));

  // Section: Header
  $wp_customize->add_section('tinsta_sections_header', [
    'title' => __('Header', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('header_padding', [
    'label' => __('Padding', 'tinsta'),
    'section' => 'tinsta_sections_header',
    'type' => 'number',
    'input_attrs' => [
      'min' => 0,
      'max' => 120,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('header_sticky', [
    'type' => 'checkbox',
    'label' => __('Sticky', 'tinsta'),
    'section' => 'tinsta_sections_header',
  ]);
  $wp_customize->add_control('header_background_wrapper', [
    'type' => 'checkbox',
    'label' => __('Background on wrapper', 'tinsta'),
    'section' => 'tinsta_sections_header',
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_color_background', [
    'label' => __('Background', 'tinsta'),
    'section' => 'tinsta_sections_header',
  ]));
  $wp_customize->add_control('header_color_background_opacity', [
    'label' => __('Opacity', 'tinsta'),
    'section' => 'tinsta_sections_header',
    'type' => 'number',
    'input_attrs' => [
      'min' => 0,
      'max' => 100,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_color_foreground', [
    'label' => __('Foreground', 'tinsta'),
    'section' => 'tinsta_sections_header',
  ]));
  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'header_image', [
    'label' => __('Background Image', 'tinsta'),
    'section' => 'tinsta_sections_header',
  ]));
  $wp_customize->add_control(new WP_Customize_Background_Position_Control($wp_customize, 'header_image_background_position', [
    'label' => __('Background Image Position', 'tinsta'),
    'section' => 'tinsta_sections_header',
    'settings' => [
      'x' => 'header_image_position_x',
      'y' => 'header_image_position_y',
    ],
  ]));
  $wp_customize->add_control('header_image_size', [
    'label' => __('Background Image Size', 'tinsta'),
    'section' => 'tinsta_sections_header',
    'type' => 'select',
    'choices' => [
      'auto' => __('Original', 'tinsta'),
      'contain' => __('Fit to Screen', 'tinsta'),
      'cover' => __('Fill Screen', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('header_image_repeat', [
    'label' => __('Repeat Background Image', 'tinsta'),
    'section' => 'tinsta_sections_header',
    'type' => 'checkbox',
  ]);
  $wp_customize->add_control('header_image_attachment_scroll', [
    'label' => __('Scroll with Page', 'tinsta'),
    'section' => 'tinsta_sections_header',
    'type' => 'checkbox',
  ]);

  // Section: Primary Menu
  $wp_customize->add_section('tinsta_sections_primary_menu', [
    'title' => __('Primary Site Menu', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('primary_menu_movetop', [
    'label' => __('Move to top', 'tinsta'),
    'section' => 'tinsta_sections_primary_menu',
    'type' => 'checkbox',
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_menu_color_background', [
    'label' => __('Background', 'tinsta'),
    'section' => 'tinsta_sections_primary_menu',
  ]));
  $wp_customize->add_control('primary_menu_color_background_opacity', [
    'label' => __('Opacity', 'tinsta'),
    'section' => 'tinsta_sections_primary_menu',
    'type' => 'number',
    'input_attrs' => [
      'min' => 0,
      'max' => 100,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_menu_color_foreground', [
    'label' => __('Foreground', 'tinsta'),
    'section' => 'tinsta_sections_primary_menu',
  ]));

  // Section: Main
  $wp_customize->add_section('tinsta_sections_main', [
    'title' => __('Main', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'main_color_background', [
    'label' => __('Background', 'tinsta'),
    'section' => 'tinsta_sections_main',
  ]));
  $wp_customize->add_control('main_color_background_opacity', [
    'label' => __('Opacity', 'tinsta'),
    'section' => 'tinsta_sections_main',
    'type' => 'number',
    'input_attrs' => [
      'min' => 0,
      'max' => 100,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'main_color_foreground', [
    'label' => __('Foreground', 'tinsta'),
    'section' => 'tinsta_sections_main',
  ]));
  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'main_image', [
    'label' => __('Background Image', 'tinsta'),
    'section' => 'tinsta_sections_main',
  ]));
  $wp_customize->add_control(new WP_Customize_Background_Position_Control($wp_customize, 'main_image_background_position', [
    'label' => __('Background Image Position', 'tinsta'),
    'section' => 'tinsta_sections_main',
    'settings' => [
      'x' => 'main_image_position_x',
      'y' => 'main_image_position_y',
    ],
  ]));
  $wp_customize->add_control('main_image_size', [
    'label' => __('Background Image Size', 'tinsta'),
    'section' => 'tinsta_sections_main',
    'type' => 'select',
    'choices' => [
      'auto' => __('Original', 'tinsta'),
      'contain' => __('Fit to Screen', 'tinsta'),
      'cover' => __('Fill Screen', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('main_image_repeat', [
    'label' => __('Repeat Background Image', 'tinsta'),
    'section' => 'tinsta_sections_main',
    'type' => 'checkbox',
  ]);
  $wp_customize->add_control('main_image_attachment_scroll', [
    'label' => __('Scroll with Page', 'tinsta'),
    'section' => 'tinsta_sections_main',
    'type' => 'checkbox',
  ]);

  // Section: Sidebar
  $wp_customize->add_section('tinsta_sections_sidebar', [
    'title' => __('Primary Sidebar', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('sidebar_primary_width', [
    'label' => __('Width', 'tinsta'),
    'section' => 'tinsta_sections_sidebar',
    'type' => 'number',
    'input_attrs' => [
      'min' => 600,
      'max' => 1440,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sidebar_primary_color_background', [
    'label' => __('Background', 'tinsta'),
    'section' => 'tinsta_sections_sidebar',
  ]));
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sidebar_primary_color_foreground', [
    'label' => __('Foreground', 'tinsta'),
    'section' => 'tinsta_sections_sidebar',
  ]));

  // Section: Sidebar Secondary
  $wp_customize->add_section('tinsta_sections_sidebar_secondary', [
    'title' => __('Secondary Sidebar', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('sidebar_secondary_width', [
    'label' => __('Width', 'tinsta'),
    'section' => 'tinsta_sections_sidebar_secondary',
    'type' => 'number',
    'input_attrs' => [
      'min' => 100,
      'max' => 300,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sidebar_secondary_color_background', [
    'label' => __('Background', 'tinsta'),
    'section' => 'tinsta_sections_sidebar_secondary',
  ]));
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sidebar_secondary_color_foreground', [
    'label' => __('Foreground', 'tinsta'),
    'section' => 'tinsta_sections_sidebar_secondary',
  ]));

  // Section: Footer
  $wp_customize->add_section('tinsta_sections_footer', [
    'title' => __('Footer', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('footer_background_highlight_inner', [
    'type' => 'checkbox',
    'label' => __('Highlight inner background', 'tinsta'),
    'section' => 'tinsta_sections_footer',
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_color_background', [
    'label' => __('Background', 'tinsta'),
    'section' => 'tinsta_sections_footer',
  ]));
  $wp_customize->add_control('footer_color_background_opacity', [
    'label' => __('Opacity', 'tinsta'),
    'section' => 'tinsta_sections_footer',
    'type' => 'number',
    'input_attrs' => [
      'min' => 0,
      'max' => 100,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_color_foreground', [
    'label' => __('Foreground', 'tinsta'),
    'section' => 'tinsta_sections_footer',
  ]));
  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'footer_image', [
    'label' => __('Background Image', 'tinsta'),
    'section' => 'tinsta_sections_footer',
  ]));
  $wp_customize->add_control(new WP_Customize_Background_Position_Control($wp_customize, 'footer_image_background_position', [
    'label' => __('Background Image Position', 'tinsta'),
    'section' => 'tinsta_sections_footer',
    'settings' => [
      'x' => 'footer_image_position_x',
      'y' => 'footer_image_position_y',
    ],
  ]));
  $wp_customize->add_control('footer_image_size', [
    'label' => __('Background Image Size', 'tinsta'),
    'section' => 'tinsta_sections_footer',
    'type' => 'select',
    'choices' => [
      'auto' => __('Original', 'tinsta'),
      'contain' => __('Fit to Screen', 'tinsta'),
      'cover' => __('Fill Screen', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('footer_image_repeat', [
    'label' => __('Repeat Background Image', 'tinsta'),
    'section' => 'tinsta_sections_footer',
    'type' => 'checkbox',
  ]);
  $wp_customize->add_control('footer_image_attachment_scroll', [
    'label' => __('Scroll with Page', 'tinsta'),
    'section' => 'tinsta_sections_footer',
    'type' => 'checkbox',
  ]);

  // Section: Bottomline
  $wp_customize->add_section('tinsta_sections_bottomline', [
    'title' => __('Bottomline', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('bottomline_sticky', [
    'type' => 'checkbox',
    'label' => __('Sticky', 'tinsta'),
    'section' => 'tinsta_sections_bottomline',
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'bottomline_color_background', [
    'label' => __('Background', 'tinsta'),
    'section' => 'tinsta_sections_bottomline',
  ]));
  $wp_customize->add_control('bottomline_color_background_opacity', [
    'label' => __('Opacity', 'tinsta'),
    'section' => 'tinsta_sections_bottomline',
    'type' => 'number',
    'input_attrs' => [
      'min' => 0,
      'max' => 100,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'bottomline_color_foreground', [
    'label' => __('Foreground', 'tinsta'),
    'section' => 'tinsta_sections_bottomline',
  ]));


  /*
   * Theme components.
   */
  $wp_customize->add_panel('tinsta_components', [
    'priority' => 30,
    'title' => __('Components', 'tinsta'),
    'description' => __('Configure theme components', 'tinsta'),
  ]);

  // Component: Site Identity
  $wp_customize->get_section('title_tagline')->panel = 'tinsta_components';

  // Component: Social networks code
  $wp_customize->add_section('tinsta_components_social_networks_code', [
    'title' => __('Social networks code', 'tinsta'),
    'panel' => 'tinsta_components',
    'priority' => 200,
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'social_networks_code', [
    'section' => 'tinsta_components_social_networks_code',
    'description' => __('Appears in posts headers. Can be used to put AddThis or social networks like, follow, share or etc.', 'tinsta'),
    'code_type'   => 'text/html',
  ]));

  // Component: Meta HTML
  $wp_customize->add_section('tinsta_components_header_markup', [
    'title' => __('HTML <head>', 'tinsta'),
    'panel' => 'tinsta_components',
    'priority' => 200,
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'header_markup', [
    'section' => 'tinsta_components_header_markup',
    'description' => __('Useful in case of adding extra 3rd party JS, CSS or HTML tags like analytics or etc.', 'tinsta'),
    'code_type' => 'text/html',
  ]));

  // Component: Footer HTML
  $wp_customize->add_section('tinsta_components_footer_markup', [
    'title' => __('HTML <body>', 'tinsta'),
    'panel' => 'tinsta_components',
    'priority' => 200,
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'footer_markup', [
    'section' => 'tinsta_components_footer_markup',
    'description' => __('Useful in case of adding extra 3rd party JS, CSS or HTML tags like analytics or etc.', 'tinsta'),
    'code_type' => 'text/html',
  ]));

  // Component: Agreement
  $wp_customize->add_section('tinsta_components_agreement', [
    'title' => __('Agreement Dialog', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('site_agreement_enable', [
    'label' => __('Enable', 'tinsta'),
    'section' => 'tinsta_components_agreement',
    'type' => 'checkbox',
  ]);
  $wp_customize->add_control('site_agreement_text', [
    'label' => __('Text', 'tinsta'),
    'section' => 'tinsta_components_agreement',
    'type' => 'textarea',
  ]);
  $wp_customize->add_control('site_agreement_agree_button', [
    'label' => __('Button Text', 'tinsta'),
    'section' => 'tinsta_components_agreement',
  ]);
  $wp_customize->add_control('site_agreement_cancel_title', [
    'label' => __('Cancel Text', 'tinsta'),
    'section' => 'tinsta_components_agreement',
  ]);
  $wp_customize->add_control('site_agreement_cancel_url', [
    'label' => __('Cancel URL', 'tinsta'),
    'section' => 'tinsta_components_agreement',
  ]);

  // Component: Breadcrumbs
  $wp_customize->add_section('tinsta_components_breadcrumbs', [
    'title' => __('Breadcrumbs', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('include_home_in_breadcrumbs', [
    'type' => 'checkbox',
    'label' => __('Include Home', 'tinsta'),
    'section' => 'tinsta_components_breadcrumbs',
  ]);

  // Component: Context Header
  $wp_customize->add_section('tinsta_components_context_header', [
    'title' => __('Context Header', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('context_header_date_format', [
    'label' => __('Date format', 'tinsta'),
    'section' => 'tinsta_components_context_header',
    'description' => __('Use <a href="http://php.net/manual/bg/function.date.php" target="_blank">PHP date</a> format.', 'tinsta'),
  ]);

  // Component: Pagination
  $wp_customize->add_section('tinsta_components_pagination', [
    'title' => __('Pagination', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('pagination_style', [
    'label' => __('Style', 'tinsta'),
    'section' => 'tinsta_components_pagination',
    'type' => 'select',
    'choices' => [
      '' => __('None', 'tinsta'),
      'borders' => __('Borders', 'tinsta'),
      'bold' => __('Bold', 'tinsta'),
    ]
  ]);

  // Component: Scroll Top
  $wp_customize->add_section('tinsta_components_scrolltop', [
    'title' => __('Scroll Top', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('scrolltop', [
    'label' => __('Position', 'tinsta'),
    'section' => 'tinsta_components_scrolltop',
    'type' => 'select',
    'choices' => [
      '' => __('None', 'tinsta'),
      'top-right' => sprintf('%s - %s', __('Top', 'tinsta'), __('Right', 'tinsta')),
      'top-left' => sprintf('%s - %s', __('Top', 'tinsta'), __('Left', 'tinsta')),
      'bottom-right' => sprintf('%s - %s', __('Bottom', 'tinsta'), __('Right', 'tinsta')),
      'bottom-left' => sprintf('%s - %s', __('Bottom', 'tinsta'), __('Left', 'tinsta')),
    ]
  ]);

  // Component: Fivestar
  $wp_customize->add_section('tinsta_components_fivestar', [
    'title' => __('Fivestar', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('fivestar_max_value', [
    'label' => __('Max rate value', 'tinsta'),
    'section' => 'tinsta_components_fivestar',
    'type' => 'number',
  ]);
  $wp_customize->add_control('fivestar_symbol_empty', [
    'label' => __('Empty symbol', 'tinsta'),
    'section' => 'tinsta_components_fivestar',
    'description' => sprintf(__('Use %s as a reference.', 'tinsta'), '<a href="https://icons8.com/line-awesome/cheatsheet" target="_blank">LineAwesome</a>'),
    'input_attrs' => [
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('fivestar_symbol_full', [
    'label' => __('Full symbol', 'tinsta'),
    'section' => 'tinsta_components_fivestar',
    'description' => sprintf(__('Use %s as a reference.', 'tinsta'), '<a href="https://icons8.com/line-awesome/cheatsheet" target="_blank">LineAwesome</a>'),
    'input_attrs' => [
      'style' => 'width:6em;',
    ],
  ]);

  // Component: Outdated posts
  $wp_customize->add_section('tinsta_components_outdated_post', [
    'title' => __('Outdated Post notification', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('outdated_post_time', [
    'type' => 'number',
    'label' => __('Time', 'tinsta'),
    'section' => 'tinsta_components_outdated_post',
    'description' => __('In days, 0 or empty to disable.', 'tinsta'),
  ]);
  $wp_customize->add_control('outdated_post_message', [
    'type' => 'textarea',
    'label' => __('Message', 'tinsta'),
    'section' => 'tinsta_components_outdated_post',
    'description' => __('Use %time% token to show the time ago', 'tinsta'),
  ]);

  // Component: Avatars
  $wp_customize->add_section('tinsta_components_avatars', [
    'title' => __('Avatars', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('avatar_size', [
    'label' => __('Size', 'tinsta'),
    'section' => 'tinsta_components_avatars',
    'type' => 'number',
    'input_attrs' => [
      'min' => 32,
      'max' => 128,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('avatar_size_small', [
    'label' => __('Small size', 'tinsta'),
    'section' => 'tinsta_components_avatars',
    'type' => 'number',
    'input_attrs' => [
      'min' => 24,
      'max' => 96,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);

  // Component: Topline
  $wp_customize->add_section('tinsta_components_topline', [
    'title' => __('Topline', 'tinsta'),
    'panel' => 'tinsta_components'
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'site_topline', [
    'section' => 'tinsta_components_topline',
    'code_type' => 'text/html',
  ]));
  $wp_customize->selective_refresh->add_partial('site_topline', [
    'selector' => '.site-topline-wrapper',
  ]);

  // Component: Bottomline
  $wp_customize->add_section('tinsta_components_bottomline', [
    'title' => __('Bottomline', 'tinsta'),
    'panel' => 'tinsta_components'
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'site_bottomline', [
    'section' => 'tinsta_components_bottomline',
    'code_type' => 'text/html',
  ]));
  $wp_customize->selective_refresh->add_partial('site_bottomline', [
    'selector' => '.site-bottomline-wrapper',
  ]);

  // Component: Custom CSS
  $wp_customize->get_section('custom_css')->panel = 'tinsta_components';
  $wp_customize->get_section('custom_css')->priority = 210;


  /**
   * Page types
   */
  $wp_customize->add_panel('tinsta_page_types', [
    'title' => __('Page Types', 'tinsta'),
  ]);

  // Page Type: Homepage settings
  $wp_customize->get_section('static_front_page')->panel = 'tinsta_page_types';

  // Page Type: Login and Register
  $wp_customize->add_section('tinsta_components_login', [
    'title' => __('Login and Register', 'tinsta'),
    'panel' => 'tinsta_page_types',
  ]);
  $wp_customize->add_control('login_integration_mode', [
    'type' => 'select',
    'label' => __('Theming', 'tinsta'),
    'section' => 'tinsta_components_login',
    'choices' => [
      '' => __('None', 'tinsta'),
      'brand' => __('Brand Only', 'tinsta'),
      'full' => __('Full theme integration', 'tinsta'),
    ],
  ]);

  // Page Type: 404
  $wp_customize->add_section('tinsta_components_404', [
    'title' => __('404', 'tinsta'),
    'panel' => 'tinsta_page_types',
  ]);
  $wp_customize->add_control('theme_404_page', [
    'type' => 'checkbox',
    'label' => __('Theming', 'tinsta'),
    'section' => 'tinsta_components_404',
  ]);


  /**
   * Tinsta theme misc
   */
  $wp_customize->add_section('tinsta_appearance_misc', [
    'title' => __('Miscellaneous', 'tinsta'),
  ]);
  $wp_customize->add_control('legacy_support', [
    'type' => 'checkbox',
    'label' => __('Legacy browser support.', 'tinsta'),
    'description' => __('Enable legacy browsers support, it could heart the performance but will add support for old browser like IE < 10, and Chrome, Firefox and Opera versions few years.', 'tinsta'),
    'section' => 'tinsta_appearance_misc',
  ]);
  $wp_customize->add_control('excerpt_more', [
    'label' => __('Read More Style', 'tinsta'),
    'section' => 'tinsta_appearance_misc',
    'type' => 'select',
    'choices' => [
      '' => __('Default', 'tinsta'),
      '&hellip;' => __('Hellip', 'tinsta'),
      '&rarr;' => __('Arrow', 'tinsta'),
      '&#9657;' => __('Triangle', 'tinsta'),
    ]
  ]);
  $wp_customize->add_control('font_icons_name', [
    'label' => __('Read More Style', 'tinsta'),
    'section' => 'tinsta_appearance_misc',
    'type' => 'select',
    'choices' => [
      'line-awesome' => 'LineAwesome',
      'fontawesome' => 'FontAwesome',
      '' => __('External FontAwesome compatible front', 'tinsta'),
    ],
    'description' => __('Icon font to use, must be FontAwesome compatible.', 'tinsta'),
  ]);
  $wp_customize->add_control('effects', [
    'type' => 'checkbox',
    'label' => __('Enable theme effects', 'tinsta'),
    'section' => 'tinsta_appearance_misc',
    'description' => __('Enable theme effects like shadows, animations and etc.', 'tinsta'),
  ]);


});
