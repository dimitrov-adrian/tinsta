<?php

// @TODO remove before release.
add_action('after_switch_theme', function() {
  if (defined('WP_DEBUG') && WP_DEBUG) {
    foreach (tinsta_options_defaults() as $key => $val) {
      set_theme_mod($key, $val);
    }
  }
});

add_action('after_setup_theme', function () {

  // First, need to load language, because some of the default options uses translations.
  load_theme_textdomain('tinsta', get_template_directory() . '/languages');

  register_nav_menus([
    'main' => __('Site Primary Menu', 'tinsta'),
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
  $theme_defaults = tinsta_options_defaults();
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

});


// Rebuild the stylesheet when theme mods updated from customizer.
add_action('customize_save_after', function() {
  tinsta_setup_stylesheets('', 'clear=1');
  tinsta_setup_stylesheets('typography', 'clear=1');
});


add_action('init', function () {

  // @todo add them via widgets_init
  register_sidebar([
    'name' => __('Front-page Full Width', 'tinsta'),
    'id' => 'frontpage-full',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

  register_sidebar([
    'name' => __('Front-page Main', 'tinsta'),
    'id' => 'frontpage',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

  register_sidebar([
    'name' => __('Front-page Left', 'tinsta'),
    'id' => 'frontpage-primary',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

  register_sidebar([
    'name' => __('Front-page Right', 'tinsta'),
    'id' => 'frontpage-secondary',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widgettitle">',
    'after_title' => '</div>',
  ]);

  register_sidebar([
    'name' => __('Header', 'tinsta'),
    'id' => 'header',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="screen-reader-text">',
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

  register_sidebar([
    'name' => __('Error 404', 'tinsta'),
    'id' => 'error-404',
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

  return array_diff($classes, [
    'single',
    'archive',
  ]);

}, 10, 2);


// @TODO may be need to filter those that are not intended to be accessible in SCSS
// @TODO better sanitization
add_filter('tinsta_scss_variables', function ($vars = []) {

  $defaults = tinsta_options_defaults();
  $theme_mods = get_theme_mods();

  // Process and sanitize default theme mods.
  foreach ($theme_mods as $name => $value) {
    if (isset($defaults[$name])) {

      // @TODO better sanitization.
      if (!is_scalar($value)) {
        continue;
      }

      $vars[$name] = $value;

      // Seems to be color.
      if (substr($defaults[$name], 0, 1) == '#') {
        if (substr($value, 0, 1) != '#' || ( strlen($value) != 4 && strlen($value) != 7 ) ) {
          $vars[$name] = $defaults[$name];
        }
      }

      // Sanitize numbers.
      if (is_numeric($defaults[$name])) {
        if (!is_numeric($value)) {
          $float_val = sprintf('%.2f', $value);
          $int_val = sprintf('%d', $value);
          if ($float_val == 0) {
            $vars[$name] = $defaults[$name];
          }
          else {
            $vars[$name] = $float_val != $int_val ? $float_val : $int_val;
          }
        }

      }

      // Check for units.
      //      $unit_pattern = '/^\d+(em|ex|\%|px|cm|mm|in|pt|pc|ch|rem|vh|vw|vmin|vmax)?$/i';
      //      if (preg_match($unit_pattern, $defaults[$name])) {
      //        $vars[$name] = trim($value);
      //        if (!preg_match($unit_pattern, trim($value))) {
      //          $vars[$name] = $defaults[$name];
      //        }
      //      }

    }
  }

  // Unitize the breakpoints.
  foreach ([ 'breakpoint_desktop', 'breakpoint_tablet', 'breakpoint_mobile' ] as $breakpoint) {
    if (empty($vars[$breakpoint]) || !is_numeric($vars[$breakpoint])) {
      $vars[$breakpoint] = $defaults[$breakpoint];
    }
    $vars[$breakpoint] .= 'px';
  }

  return $vars;
}, 5);


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



add_action('wp_print_styles', function () {

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

  // Load the Tinsta stylesheet.
  // the theme mod stylesheet is not set and not exposed to customzier, it's just a prototype.
  $stylesheet = get_theme_mod('stylesheet');
  if (!$stylesheet) {
    $stylesheet = 'style';
  }
  $stylesheet_url = tinsta_setup_stylesheets($stylesheet);
  $hash = md5(serialize(get_theme_mods()));
  wp_enqueue_style('tinsta-stylesheet', $stylesheet_url, [], $hash);

});


add_filter('wp_resource_hints', function ($urls) {
  // @TODO seems to not works.
  if (wp_style_is('tinsta-google-fonts')) {
    $urls[] = [
      'href' => '//fonts.googleapis.com',
      'crossorigin',
    ];
  }

  return $urls;
});


add_action('admin_init', function () {
  // Add editor stylesheet.
  add_editor_style(tinsta_setup_stylesheets('typography'));
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
  foreach (tinsta_options_defaults() as $option_name => $value) {
    $wp_customize->add_setting($option_name, [
      'type' => 'theme_mod',
      'default' => $value,
      'transport' => 'refresh',
      // @TODO add sanitization
      // @TODO add validation
    ]);
  }


  // Theme components.
  $wp_customize->add_panel('tinsta_components', [
    'priority' => 70,
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
    'title' => __('Head Meta HTML', 'tinsta'),
    'panel' => 'tinsta_components',
    'priority' => 200,
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'header_markup', [
    'section' => 'tinsta_components_header_markup',
    'description' => __('Useful in case of putting extra 3rd party JS, CSS or HTML tags like analytics or etc.', 'tinsta'),
    'code_type' => 'text/html',
  ]));

  // Component: Footer HTML
  $wp_customize->add_section('tinsta_components_footer_markup', [
    'title' => __('Footer HTML', 'tinsta'),
    'panel' => 'tinsta_components_footer_markup',
    'priority' => 200,
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'footer_markup', [
    'section' => 'tinsta_components_footer_markup',
    'description' => __('Useful in case of putting extra 3rd party JS, CSS or HTML tags like analytics or etc.', 'tinsta'),
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

  // Component: Login and Register.
  $wp_customize->add_section('tinsta_components_login', [
    'title' => __('Login and Register', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('login_integration_mode', [
    'type' => 'select',
    'label' => __('Enable theme login and register forms.', 'tinsta'),
    'section' => 'tinsta_components_login',
    'choices' => [
      '' => __('Default', 'tinsta'),
      'brand' => __('Brand Only', 'tinsta'),
      'full' => __('Full theme integration', 'tinsta'),
    ],
  ]);

  // Component: 404
  $wp_customize->add_section('tinsta_components_404', [
    'title' => __('404', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('theme_404_page', [
    'type' => 'checkbox',
    'label' => __('Enable theme for 404 error page.', 'tinsta'),
    'section' => 'tinsta_components_404',
  ]);

  // Component: Effects
  $wp_customize->add_section('tinsta_components_effects', [
    'title' => __('Effects', 'tinsta'),
    'panel' => 'tinsta_components'
  ]);
  $wp_customize->add_control('effects', [
    'type' => 'checkbox',
    'label' => __('Enable theme effects', 'tinsta'),
    'section' => 'tinsta_components_effects',
    'description' => __('Enable theme effects like shadows, animations and etc.', 'tinsta'),
  ]);

  // Component: Topline
  $wp_customize->add_section('tinsta_components_topline', [
    'title' => __('Topline', 'tinsta'),
    'panel' => 'tinsta_components'
  ]);
  $wp_customize->add_control('site_topline', [
    'label' => __('Content', 'tinsta'),
    'description' => __('HTML is supported', 'tinsta'),
    'section' => 'tinsta_components_topline',
    'type' => 'textarea',
  ]);
  $wp_customize->selective_refresh->add_partial('site_topline', [
    'selector' => '.site-topline-wrapper',
  ]);

  // Component: Bottomline
  $wp_customize->add_section('tinsta_components_bottomline', [
    'title' => __('Bottomline', 'tinsta'),
    'panel' => 'tinsta_components'
  ]);
  $wp_customize->add_control('site_bottomline', [
    'label' => __('Content', 'tinsta'),
    'description' => __('HTML is supported', 'tinsta'),
    'section' => 'tinsta_components_bottomline',
    'type' => 'textarea',
  ]);
  $wp_customize->selective_refresh->add_partial('site_bottomline', [
    'selector' => '.site-bottomline-wrapper',
  ]);


  // Component: Custom CSS
  $wp_customize->get_section('custom_css')->panel = 'tinsta_components';
  $wp_customize->get_section('custom_css')->priority = 210;


  // Typography
  $wp_customize->add_section('tinsta_appearance_typography', [
    'title' => __('Typography', 'tinsta'),
    'priority' => 60,
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
  $wp_customize->add_control('font_headings_petite', [
    'label' => __('Petite headings', 'tinsta'),
    'section' => 'tinsta_appearance_typography',
    'type' => 'checkbox',
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


  // Sections:
  $wp_customize->add_panel('tinsta_sections', [
    'title' => __('Sections', 'tinsta'),
    'description' => __('Setup section appearances like colors, background and behavior.', 'tinsta'),
    'priority' => 50,
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
    'type' => 'number',
    'input_attrs' => [
      'min' => 960,
      'max' => 1800,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('breakpoint_tablet', [
    'label' => __('Tablet breakpoint (px)', 'tinsta'),
    'section' => 'tinsta_sections_globals',
    'type' => 'number',
    'input_attrs' => [
      'min' => 600,
      'max' => 1100,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('breakpoint_mobile', [
    'label' => __('Mobile breakpoint (px)', 'tinsta'),
    'section' => 'tinsta_sections_globals',
    'type' => 'number',
    'input_attrs' => [
      'min' => 240,
      'max' => 800,
      'step' => 1,
      'style' => 'width:6em;',
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
    'title' => __('Primary Menu', 'tinsta'),
    'panel' => 'tinsta_sections',
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

  // Tinsta theme misc
  $wp_customize->add_section('tinsta_appearance_misc', [
    'title' => __('Miscellaneous', 'tinsta'),
  ]);
  $wp_customize->add_control('legacy_support', [
    'type' => 'checkbox',
    'label' => __('Legacy browser support.', 'tinsta'),
    'description' => __('Enable legacy browsers support, it could heart the performance but will add support for old browser like IE < 10, and Chrome, Firefox and Opera versions few years.', 'tinsta'),
    'section' => 'tinsta_appearance_misc',
  ]);

});
