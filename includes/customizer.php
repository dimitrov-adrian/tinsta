<?php

/**
 * @file
 * Setup customizer.
 */


add_theme_support( 'customize-selective-refresh-widgets' );


/**
 * Setup base region setting controls
 *
 * @param \WP_Customize_Manager
 * @param string $region_slug
 */
function tinsta_customizer_setup_color_controls($wp_customize, $region_slug, $options = [])
{
  $defaults = tinsta_get_options_defaults();

  $region_slug            = 'region_' . $region_slug;
  $customizer_region_name = 'tinsta_' . $region_slug;

  if (isset($defaults["{$region_slug}_color_background"])) {
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, "{$region_slug}_color_background", [
      'label'   => __('Background Color', 'tinsta'),
      'section' => $customizer_region_name,
    ]));
  }

  if (isset($defaults["{$region_slug}_color_background_opacity"])) {
    $wp_customize->add_control("{$region_slug}_color_background_opacity", [
      'label'       => __('Background Color Opacity', 'tinsta') . ' (%)',
      'section' => $customizer_region_name,
      'type'        => 'number',
      'input_attrs' => [
        'min'   => 0,
        'max'   => 100,
        'step'  => 1,
        'style' => 'width:6em;',
      ],
    ]);
  }

  if (isset($defaults["{$region_slug}_color_foreground"])) {
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, "{$region_slug}_color_foreground", [
      'label'   => __('Foreground Color', 'tinsta'),
      'section' => $customizer_region_name,
    ]));
  }

  if (isset($defaults["{$region_slug}_color_primary"])) {
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, "{$region_slug}_color_primary", [
      'label'   => __('Primary Color', 'tinsta'),
      'section' => $customizer_region_name,
    ]));
  }

  if (isset($defaults["{$region_slug}_color_secondary"])) {
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, "{$region_slug}_color_secondary", [
      'label'   => __('Secondary Color', 'tinsta'),
      'section' => $customizer_region_name,
    ]));
  }

}


/**
 * Setup base region setting controls
 *
 * @param \WP_Customize_Manager
 * @param string $region_slug
 */
function tinsta_customizer_setup_background_controls($wp_customize, $region_slug)
{

  $region_slug            = 'region_' . $region_slug;
  $customizer_region_name = 'tinsta_' . $region_slug;

  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "{$region_slug}_image", [
    'label'   => __('Background Image', 'tinsta'),
    'section' => $customizer_region_name,
  ]));
  $wp_customize->add_control(new WP_Customize_Background_Position_Control($wp_customize, "{$region_slug}_image_position", [
    'label'           => __('Background Image Position', 'tinsta'),
    'section' => $customizer_region_name,
    'settings'        => [
      'x' => "{$region_slug}_image_position_x",
      'y' => "{$region_slug}_image_position_y",
    ],
    'active_callback' => function () use ($region_slug) {
      return ! ! get_theme_mod($region_slug . '_image');
    },
  ]));
  $wp_customize->add_control("{$region_slug}_image_size", [
    'label'           => __('Background Image Size', 'tinsta'),
    'section' => $customizer_region_name,
    'type'            => 'select',
    'choices'         => [
      'auto'    => __('Original', 'tinsta'),
      'contain' => __('Fit to Screen', 'tinsta'),
      'cover'   => __('Fill Screen', 'tinsta'),
    ],
    'active_callback' => function () use ($region_slug) {
      return ! ! get_theme_mod($region_slug . '_image');
    },
  ]);
  $wp_customize->add_control("{$region_slug}_image_repeat", [
    'label'           => __('Repeat Background Image', 'tinsta'),
    'section' => $customizer_region_name,
    'type'            => 'checkbox',
    'active_callback' => function () use ($region_slug) {
      return ! ! get_theme_mod($region_slug . '_image');
    },
  ]);
  $wp_customize->add_control("{$region_slug}_image_attachment_scroll", [
    'label'           => __('Scroll with Page', 'tinsta'),
    'section' => $customizer_region_name,
    'type'            => 'checkbox',
    'active_callback' => function () use ($region_slug) {
      return ! ! get_theme_mod($region_slug . '_image');
    },
  ]);

}


/**
 * Customizer Preview settings patches.
 */
add_filter('tinsta_get_stylesheet_args', function ($args) {

  global $wp_customize;

  $customizer_patched_data_values = $wp_customize->unsanitized_post_values();

  if ($wp_customize->validate_setting_values($customizer_patched_data_values)) {
    $customizer_patched_data_values = array_intersect_key($customizer_patched_data_values, tinsta_get_options_defaults());
    $customizer_patched_data_values = array_replace_recursive($args['variables'], $customizer_patched_data_values);

    // Set the preview changed flag only when the values are changed,
    // there is a lot of cases where no need to update preview stylesheets (eg.: widget change, post content changes, etc...)
    if ($args['variables'] != $customizer_patched_data_values) {
      $args['variables']          = $customizer_patched_data_values;
      $args['preview_is_updated'] = true;
    }

  }

  // @TODO make this works because it filter vars that are not in the defaults, such like post type related.
  $args['preview_is_updated'] = true;

  $args['preview'] = true;

  return $args;
}, 5);


/**
 * Register setting controls
 */
add_action('customize_register', function ($wp_customize) {

  /** @var $wp_customize \WP_Customize_Manager */

  // Remove built-in color customizer.
  $wp_customize->remove_section('colors');

  // Forced theme options.
  $forced_theme_mods = (array) apply_filters('tinsta_force_options', []);

  // Register all theme mods as settings in customizer.
  foreach (tinsta_get_options_defaults() as $option_name => $value) {

    // Do not add settings for forced options.
    if (array_key_exists($option_name, $forced_theme_mods)) {
      continue;
    }

    // General theme's setting registering.
    // overriding some of the props be done with
    // $wp_customize->get_setting(<setting_name>)-><property> = <new_value>;
    $wp_customize->add_setting($option_name, [
      'type'      => 'theme_mod',
      'default'   => $value,
      'transport' => 'refresh',
      'sanitize_callback' => function($value) {
        return strval($value);
      },
      // @TODO add sanitization
      // @TODO add validation
    ]);
  }

  // Typography
  $wp_customize->add_section('tinsta_typography', [
    'title'    => __('Typography', 'tinsta'),
    'priority' => 20,
  ]);
  $wp_customize->add_control('typography_font_size', [
    'label'       => __('Base Font Size', 'tinsta') . ' (px)',
    'section' => 'tinsta_typography',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 10,
      'max'   => 20,
      'step'  => 0.5,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('typography_font_line_height', [
    'label'       => __('Line Height', 'tinsta') . ' (%)',
    'section' => 'tinsta_typography',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 50,
      'max'   => 300,
      'step'  => 10,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('typography_font_headings_style', [
    'label'   => __('Headings Style', 'tinsta'),
    'section' => 'tinsta_typography',
    'type'    => 'select',
    'choices' => [
      ''               => __('Default', 'tinsta'),
      'uppercase'      => __('Uppercase', 'tinsta'),
      'all-small-caps' => __('All Small Caps', 'tinsta'),
      'small-caps'     => __('Small Caps', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('typography_font_family', [
    'label'   => __('Fallback font-family', 'tinsta'),
    'section' => 'tinsta_typography',
    'type'    => 'textarea',
  ]);
  $wp_customize->add_control('typography_font_family_headings', [
    'label'   => sprintf(__('(Headings) %s', 'tinsta'), __('Fallback font-family', 'tinsta')),
    'section' => 'tinsta_typography',
    'type'    => 'textarea',
  ]);

  // Use select for google font names, but if there is some problem, fallback to text input.
  $wp_customize->add_control('typography_font_google', [
    'label'       => __('Google Font', 'tinsta'),
    'section' => 'tinsta_typography',
    'description' => sprintf(
      __('Use font name from %s', 'tinsta'),
      '<a target="_blank" href="https://fonts.google.com/" rel="noopener">google fonts</a>'),
  ]);
  $wp_customize->add_control('typography_font_headings_google', [
    'label'       => sprintf(__('(Headings) %s', 'tinsta'), __('Google Font', 'tinsta')),
    'section' => 'tinsta_typography',
    'description' => sprintf(
      __('Use font name from %s', 'tinsta'),
      '<a target="_blank" href="https://fonts.google.com/" rel="noopener">google fonts</a>'),
  ]);

  $wp_customize->add_control('typography_text_wordbreak', [
    'label'   => __('Word-break', 'tinsta'),
    'section' => 'tinsta_typography',
    'type'    => 'checkbox',
  ]);
  $wp_customize->add_control('typography_text_justify', [
    'label'   => __('Justify Text', 'tinsta'),
    'section' => 'tinsta_typography',
    'type'    => 'checkbox',
  ]);
  $wp_customize->add_control('typography_text_enhancements', [
    'label'   => __('Improve text readability with CSS tweaks', 'tinsta'),
    'section' => 'tinsta_typography',
    'type'    => 'checkbox',
  ]);
  $wp_customize->add_control('typography_form_spacing', [
    'label'       => __('Forms Field Spacing', 'tinsta') . ' (%)',
    'section' => 'tinsta_typography',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 100,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('typography_form_button_style', [
    'label'   => __('Button Style', 'tinsta'),
    'section' => 'tinsta_typography',
    'type'    => 'select',
    'choices' => [
      ''       => __('None', 'tinsta'),
      'fill'   => __('Fill', 'tinsta'),
      'border' => __('Border', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('typography_font_button_text_style', [
    'label'   => __('Button Text Style', 'tinsta'),
    'section' => 'tinsta_typography',
    'type'    => 'select',
    'choices' => [
      ''               => __('Default', 'tinsta'),
      'uppercase'      => __('Uppercase', 'tinsta'),
      'all-small-caps' => __('All Small Caps', 'tinsta'),
      'small-caps'     => __('Small Caps', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('typography_bordering', [
    'label'       => __('Bordering', 'tinsta') . ' (px)',
    'section' => 'tinsta_typography',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 5,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('typography_brightness', [
    'type'        => 'number',
    'label'       => __('Brightness', 'tinsta') . ' (%)',
    'section' => 'tinsta_typography',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 100,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('typography_roundness', [
    'label'       => __('Roundness', 'tinsta'),
    'section' => 'tinsta_typography',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 10,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);

  /*
   * Regions:
   */
  $wp_customize->add_panel('tinsta_regions', [
    'title'    => __('Regions & Layout', 'tinsta'),
    'priority' => 25,
    'description' => __('Regions are main parts of page. They are predefined from the theme, and typically can hold widgets or other content depending grom current page.', 'tinsta'),
  ]);

  // Region: Globals
  $wp_customize->add_section('tinsta_region_root', [
    'title' => __('General', 'tinsta'),
    'panel' => 'tinsta_regions',
  ]);
  $wp_customize->add_control('region_root_height_full', [
    'type'    => 'checkbox',
    'label'   => __('Full-Height Page', 'tinsta'),
    'section' => 'tinsta_region_root',
  ]);
  $wp_customize->add_control('region_root_width', [
    'label'       => __('Page Width', 'tinsta') . ' (px)',
    'section' => 'tinsta_region_root',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 400,
      'max'   => 1440,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('region_root_breakpoint_tablet', [
    'label'   => __('Tablet Breakpoint', 'tinsta'),
    'section' => 'tinsta_region_root',
    'type'    => 'select',
    'choices' => [
      '960px' => '960px',
      '920px' => '920px',
      '800px' => '800px',
      '720px' => '720px',
    ],
  ]);
  $wp_customize->add_control('region_root_breakpoint_mobile', [
    'label'   => __('Mobile Breakpoint', 'tinsta'),
    'section' => 'tinsta_region_root',
    'type'    => 'select',
    'choices' => [
      '640px' => '640px',
      '568px' => '568px',
      '480px' => '480px',
      '320px' => '320px',
    ],
  ]);
  tinsta_customizer_setup_color_controls($wp_customize, 'root');
  tinsta_customizer_setup_background_controls($wp_customize, 'root');

  // Region: Topline
  $wp_customize->add_section('tinsta_region_topline', [
    'title' => __('Topline', 'tinsta'),
    'panel' => 'tinsta_regions',
    'description' => sprintf(
      __('
        <p>
          This region does not holding widgets, but display unfiltered HTML.
          You can edit the content from <a href="%s">here</a>.
        </p>', 'tinsta'),
      'javascript:wp.customize.control(\'component_site_topline\').focus();'
    ),
    'active_callback' => function () {
      return (bool) trim(get_theme_mod('component_site_topline'));
    },
  ]);
  $wp_customize->add_control('region_topline_sticky', [
    'type'    => 'checkbox',
    'label'   => __('Sticky', 'tinsta'),
    'section' => 'tinsta_region_topline',
  ]);
  $wp_customize->add_control('region_topline_sticky2', [
    'type'    => 'text',
    'label'   => __('Sticky', 'tinsta'),
    'section' => 'tinsta_region_topline',
  ]);
  $wp_customize->add_control('region_topline_layout', [
    'label'   => __('Layout', 'tinsta'),
    'section' => 'tinsta_region_topline',
    'description' => __('Noticeable when used colors', 'tinsta'),
    'type'    => 'select',
    'choices' => [
      ''         => __('Default', 'tinsta'),
      'boxed'    => __('Boxed', 'tinsta'),
      'extended' => __('Extended', 'tinsta'),
    ],
  ]);
  tinsta_customizer_setup_color_controls($wp_customize, 'topline');

  // Region: Header
  $wp_customize->add_section('tinsta_region_header', [
    'title' => __('Header', 'tinsta'),
    'panel' => 'tinsta_regions',
    'active_callback' => function () {
      return is_active_sidebar('header');
    },
  ]);
  $wp_customize->add_control('region_header_sticky', [
    'type'    => 'checkbox',
    'label'   => __('Sticky', 'tinsta'),
    'section' => 'tinsta_region_header',
  ]);
  $wp_customize->add_control('region_header_layout', [
    'label'   => __('Layout', 'tinsta'),
    'description' => __('Noticeable when used colors', 'tinsta'),
    'section' => 'tinsta_region_header',
    'type'    => 'select',
    'choices' => [
      ''            => __('Default', 'tinsta'),
      'boxed'       => __('Boxed', 'tinsta'),
      'extended'    => __('Extended', 'tinsta'),
      'highlighted' => __('Highlighted', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('region_header_padding', [
    'label'       => __('Vertical Spacing', 'tinsta') . ' (px)',
    'section' => 'tinsta_region_header',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 120,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  tinsta_customizer_setup_color_controls($wp_customize, 'header');
  tinsta_customizer_setup_background_controls($wp_customize, 'header');

  // Region: Primary Menu (Main Menu)
  $wp_customize->add_section('tinsta_region_primary_menu', [
    'title' => __('Main Menu', 'tinsta'),
    'panel' => 'tinsta_regions',
    'active_callback' => function() {
      return has_nav_menu('main');
    }
  ]);
  $wp_customize->add_control('region_primary_menu_movetop', [
    'label'   => __('Move above Header', 'tinsta'),
    'section' => 'tinsta_region_primary_menu',
    'type'    => 'checkbox',
    'active_callback' => function () {
      return is_active_sidebar('header');
    },
  ]);
  $wp_customize->add_control('region_primary_menu_layout', [
    'label'   => __('Layout', 'tinsta'),
    'description' => __('Noticeable when used colors', 'tinsta'),
    'section' => 'tinsta_region_primary_menu',
    'type'    => 'select',
    'choices' => [
      ''      => __('Default', 'tinsta'),
      'boxed' => __('Boxed', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('region_primary_menu_aligncenter', [
    'label'   => __('Center', 'tinsta'),
    'section' => 'tinsta_region_primary_menu',
    'type'    => 'checkbox',
  ]);
  tinsta_customizer_setup_color_controls($wp_customize, 'primary_menu');

  // Region: Main
  $wp_customize->add_section('tinsta_region_main', [
    'title' => __('Main Content', 'tinsta'),
    'panel' => 'tinsta_regions',
  ]);
  $wp_customize->add_control('region_main_layout', [
    'label'   => __('Layout', 'tinsta'),
    'description' => __('Noticeable when used colors', 'tinsta'),
    'section' => 'tinsta_region_main',
    'type'    => 'select',
    'choices' => [
      ''      => __('Default', 'tinsta'),
      'boxed' => __('Boxed', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('region_main_margin', [
    'label'       => __('Margin', 'tinsta'),
    'section' => 'tinsta_region_margin',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 10,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  tinsta_customizer_setup_color_controls($wp_customize, 'main');
  tinsta_customizer_setup_background_controls($wp_customize, 'main');

  // Region: Sidebar
  $wp_customize->add_section('tinsta_region_sidebar_primary', [
    'title' => __('Primary Sidebar', 'tinsta'),
    'panel' => 'tinsta_regions',
    'active_callback' => function () {
      return is_active_sidebar('primary');
    },
  ]);
  $wp_customize->add_control('region_sidebar_primary_width', [
    'label'       => __('Width', 'tinsta') . ' (px)',
    'section' => 'tinsta_region_sidebar_primary',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 80,
      'max'   => get_theme_mod('region_root_width') - get_theme_mod('region_sidebar_secondary_width'),
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  tinsta_customizer_setup_color_controls($wp_customize, 'sidebar_primary');

  // Region: Sidebar Secondary
  $wp_customize->add_section('tinsta_region_sidebar_secondary', [
    'title' => __('Secondary Sidebar', 'tinsta'),
    'panel' => 'tinsta_regions',
    'active_callback' => function () {
      return is_active_sidebar('secondary');
    },
  ]);
  $wp_customize->add_control('region_sidebar_secondary_width', [
    'label'       => __('Width', 'tinsta') . ' (px)',
    'section' => 'tinsta_region_sidebar_secondary',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 80,
      'max'   => get_theme_mod('region_root_width') - get_theme_mod('region_sidebar_primary_width'),
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  tinsta_customizer_setup_color_controls($wp_customize, 'sidebar_secondary');

  // Region: Footer
  $wp_customize->add_section('tinsta_region_footer', [
    'title' => __('Footer', 'tinsta'),
    'panel' => 'tinsta_regions',
    'active_callback' => function () {
      return is_active_sidebar('footer');
    },
  ]);
  $wp_customize->add_control('region_footer_layout', [
    'label'   => __('Layout', 'tinsta'),
    'description' => __('Noticeable when used colors', 'tinsta'),
    'section' => 'tinsta_region_footer',
    'type'    => 'select',
    'choices' => [
      ''            => __('Default', 'tinsta'),
      'boxed'       => __('Boxed', 'tinsta'),
      'extended'    => __('Extended', 'tinsta'),
      'highlighted' => __('Highlighted', 'tinsta'),
    ],
  ]);
  tinsta_customizer_setup_color_controls($wp_customize, 'footer');
  tinsta_customizer_setup_background_controls($wp_customize, 'footer');


  // Region: Bottomline
  $wp_customize->add_section('tinsta_region_bottomline', [
    'title' => __('Bottomline', 'tinsta'),
    'panel' => 'tinsta_regions',
    'description' => sprintf(
      __('
        <p>
          This region does not holding widgets, but display unfiltered HTML.
          You can edit the content from <a href="%s">here</a>.
        </p>', 'tinsta'),
      'javascript:wp.customize.control(\'component_site_bottomline\').focus();'
    ),
    'active_callback' => function () {
      return (bool) trim(get_theme_mod('component_site_topline'));
    },
  ]);
  $wp_customize->add_control('region_bottomline_sticky', [
    'type'    => 'checkbox',
    'label'   => __('Sticky', 'tinsta'),
    'section' => 'tinsta_region_bottomline',
  ]);
  $wp_customize->add_control('region_bottomline_layout', [
    'label'   => __('Layout', 'tinsta'),
    'description' => __('Noticeable when used colors', 'tinsta'),
    'section' => 'tinsta_region_bottomline',
    'type'    => 'select',
    'choices' => [
      ''      => __('Default', 'tinsta'),
      'boxed' => __('Boxed', 'tinsta'),
    ],
  ]);
  tinsta_customizer_setup_color_controls($wp_customize, 'bottomline');


  /*
   * Theme components.
   */
  $wp_customize->add_panel('tinsta_components', [
    'priority'    => 30,
    'title'       => __('Components', 'tinsta'),
    'description' => __('Configure main components settings and behavior. They also could be and WordPress core components or widgets.', 'tinsta'),
  ]);

  // Component: Site Identity
  $wp_customize->get_section('title_tagline')->panel = 'tinsta_components';

  // Component: Agreement
  $wp_customize->add_section('tinsta_component_site_agreement', [
    'title' => __('Agreement Dialog', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('component_site_agreement_enable', [
    'label'   => __('Enable', 'tinsta'),
    'section' => 'tinsta_component_site_agreement',
    'type'    => 'checkbox',
  ]);
  $wp_customize->add_control('component_site_agreement_style', [
    'label'   => __('Style', 'tinsta'),
    'section' => 'tinsta_component_site_agreement',
    'type'    => 'select',
    'choices' => [
      'center' => __('Center', 'tinsta'),
      'top' => __('Top', 'tinsta'),
      'bottom' => __('Bottom', 'tinsta'),
      'bottomfull' => __('Bottom - Full Width', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('component_site_agreement_text', [
    'label'   => __('Text', 'tinsta'),
    'section' => 'tinsta_component_site_agreement',
    'type'    => 'textarea',
  ]);
  //  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'component_site_agreement_text', [
  //    'section' => 'tinsta_component_site_agreement',
  //    'code_type' => 'text/html',
  //    'label'   => __('Text', 'tinsta'),
  //  ]));
  $wp_customize->add_control('component_site_agreement_agree_button', [
    'label'   => __('Button Text', 'tinsta'),
    'section' => 'tinsta_component_site_agreement',
  ]);
  $wp_customize->add_control('component_site_agreement_cancel_title', [
    'label'   => __('Cancel Text', 'tinsta'),
    'section' => 'tinsta_component_site_agreement',
  ]);
  $wp_customize->add_control('component_site_agreement_cancel_url', [
    'label'   => __('Cancel URL', 'tinsta'),
    'section' => 'tinsta_component_site_agreement',
  ]);

  // Component: Breadcrumbs
  $wp_customize->add_section('tinsta_component_breadcrumbs', [
    'title' => __('Breadcrumbs', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('component_breadcrumbs_include_home', [
    'type'    => 'checkbox',
    'label'   => __('Include Home', 'tinsta'),
    'section' => 'tinsta_component_breadcrumbs',
  ]);

  // Component: Context Header
  $wp_customize->add_section('tinsta_component_context_header', [
    'title' => __('Context Header', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('component_context_header_date_format', [
    'label'       => __('Date format', 'tinsta'),
    'section' => 'tinsta_component_context_header',
    'description' => sprintf(
      __('Use <a href="%s" target="_blank" rel="noopener">PHP date</a> format.', 'tinsta'),
      'http://php.net/manual/function.date.php'),
  ]);

  // Component: Pagination
  $wp_customize->add_section('tinsta_component_pagination', [
    'title' => __('Pagination', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('component_pagination_style', [
    'label'   => __('Style', 'tinsta'),
    'section' => 'tinsta_component_pagination',
    'type'    => 'select',
    'choices' => [
      ''        => __('Plain', 'tinsta'),
      'bordered' => __('Bordered', 'tinsta'),
      'bold'    => __('Bold', 'tinsta'),
    ],
  ]);

  // Component: Scroll Top
  $wp_customize->add_section('tinsta_component_scrolltop', [
    'title' => __('Scroll Top', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('component_scrolltop', [
    'label'   => __('Position', 'tinsta'),
    'section' => 'tinsta_component_scrolltop',
    'type'    => 'select',
    'choices' => [
      ''             => __('None', 'tinsta'),
      'top-right'    => sprintf('%s - %s', __('Top', 'tinsta'), __('Right', 'tinsta')),
      'top-left'     => sprintf('%s - %s', __('Top', 'tinsta'), __('Left', 'tinsta')),
      'bottom-right' => sprintf('%s - %s', __('Bottom', 'tinsta'), __('Right', 'tinsta')),
      'bottom-left'  => sprintf('%s - %s', __('Bottom', 'tinsta'), __('Left', 'tinsta')),
    ],
  ]);

  // Component: Outdated posts
  $wp_customize->add_section('tinsta_component_outdated_post', [
    'title' => __('Outdated Content Notification', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('component_outdated_post_time', [
    'type'        => 'number',
    'label'       => __('Time', 'tinsta'),
    'section' => 'tinsta_component_outdated_post',
    'description' => __('Number of days to display the message.', 'tinsta'),
  ]);
  $wp_customize->add_control('component_outdated_post_message', [
    'type'        => 'textarea',
    'label'       => __('Message', 'tinsta'),
    'section' => 'tinsta_component_outdated_post',
    'description' => __('Use %time% token to show the time ago', 'tinsta'),
  ]);
  // No checkboxes yet, in consideration if to add this options.
  // $component_outdated_post_types = [];
  // foreach (get_post_types(['public' => true], 'objects') as $post_type) {
  //   $component_outdated_post_types[$post_type->name] = $post_type->label;
  // }
  // $wp_customize->add_control('component_outdated_post_types', [
  //   'label'   => __('Post types', 'tinsta'),
  //   'section' => 'component_outdated_post_types',
  //   'type'    => 'checkbox',
  //   'choices' => $component_outdated_post_types,
  // ]);

  // Component: Avatars
  $wp_customize->add_section('tinsta_component_avatar', [
    'title' => __('Avatars', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('component_avatar_size', [
    'label'       => __('Size', 'tinsta') . ' (px)',
    'section' => 'tinsta_component_avatar',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 32,
      'max'   => 128,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('component_avatar_size_small', [
    'label'       => __('Small size', 'tinsta') . ' (px)',
    'section' => 'tinsta_component_avatar',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 24,
      'max'   => 96,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);

  // Component: Comments
  $wp_customize->add_section('tinsta_component_comments', [
    'title' => __('Comments', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('component_comments_style', [
    'label'   => __('Layout', 'tinsta'),
    'section' => 'tinsta_component_comments',
    'type'    => 'select',
    'choices' => [
      ''     => __('Default', 'tinsta'),
      'chat' => __('Chat', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('component_comments_order_selector', [
    'label'   => __('Show order selector', 'tinsta'),
    'section' => 'tinsta_component_comments',
    'type'    => 'checkbox',
  ]);

  // Component: Topline
  $wp_customize->add_section('tinsta_component_topline', [
    'title' => __('Topline', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'component_site_topline', [
    'section' => 'tinsta_component_topline',
    'code_type' => 'text/html',
  ]));
  $wp_customize->selective_refresh->add_partial('component_site_topline', [
    'selector' => '.site-topline-wrapper',
  ]);

  // Component: Bottomline
  $wp_customize->add_section('tinsta_component_bottomline', [
    'title' => __('Bottomline', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'component_site_bottomline', [
    'section' => 'tinsta_component_bottomline',
    'code_type' => 'text/html',
  ]));
  $wp_customize->selective_refresh->add_partial('component_site_bottomline', [
    'selector' => '.site-bottomline-wrapper',
  ]);

  // Component: Social networks code
  $wp_customize->add_section('tinsta_component_social_networks_code', [
    'title'    => __('Social Networks Code', 'tinsta'),
    'panel'    => 'tinsta_components',
    'priority' => 200,
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'component_social_networks_code', [
    'section' => 'tinsta_component_social_networks_code',
    'description' => __('Appears in posts headers. Can be used to put AddThis or social networks like, follow, share or etc.', 'tinsta'),
    'code_type'   => 'text/html',
  ]));

  // Component: Meta HTML
  $wp_customize->add_section('tinsta_component_header_markup', [
    'title'    => __('HTML <head>', 'tinsta'),
    'panel'    => 'tinsta_components',
    'priority' => 200,
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'component_header_markup', [
    'section' => 'tinsta_component_header_markup',
    'description' => __('Useful in case of adding extra 3rd party JS, CSS or HTML tags like analytics or etc.', 'tinsta'),
    'code_type'   => 'text/html',
  ]));

  // Component: Footer HTML
  $wp_customize->add_section('tinsta_component_footer_markup', [
    'title'    => __('HTML <body>', 'tinsta'),
    'panel'    => 'tinsta_components',
    'priority' => 200,
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'component_footer_markup', [
    'section' => 'tinsta_component_footer_markup',
    'description' => __('Useful in case of adding extra 3rd party JS, CSS or HTML tags like analytics or etc.', 'tinsta'),
    'code_type'   => 'text/html',
  ]));

  // Component: Custom CSS
  $wp_customize->get_section('custom_css')->panel    = 'tinsta_components';
  $wp_customize->get_section('custom_css')->priority = 210;


  /**
   * System Pages
   */
  $wp_customize->add_panel('tinsta_system_pages', [
    'title' => __('System Pages', 'tinsta'),
  ]);

  // System Page: Homepage settings
  $wp_customize->get_section('static_front_page')->panel = 'tinsta_system_pages';
  //  $wp_customize->get_section('sidebar-widgets-frontpage')->active_callback = function () {
  //    return (bool) get_theme_mod('system_page_404_widgets_area');
  //  };

  // System Page: Login and Register
  $wp_customize->add_section('tinsta_system_page_login', [
    'title' => __('Login and Register', 'tinsta'),
    'panel' => 'tinsta_system_pages',
  ]);
  $wp_customize->add_control('system_page_login_theming', [
    'label'   => __('Style', 'tinsta'),
    'section' => 'tinsta_system_page_login',
    'type'    => 'select',
    'choices' => [
      ''      => __('Default', 'tinsta'),
      'brand' => __('Simple', 'tinsta'),
      'full'  => __('As a regular page', 'tinsta'),
    ],
  ]);

  // System Page: 404
  $wp_customize->add_section('tinsta_system_page_404', [
    'title' => '404',
    'panel' => 'tinsta_system_pages',
  ]);
  $wp_customize->add_control('system_page_404_theming', [
    'label'   => __('Style', 'tinsta'),
    'section' => 'tinsta_system_page_404',
    'type'    => 'select',
    'choices' => [
      ''     => __('Simple', 'tinsta'),
      'full' => __('As a regular page', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('system_page_404_widgets_area', [
    'label'   => __('Enable Widgets managing', 'tinsta'),
    'section' => 'tinsta_system_page_404',
    'type'    => 'checkbox',
  ]);
  //  $wp_customize->get_section('section-sidebar-widgets-error-404')->active_callback = function () {
  //    return (bool) get_theme_mod('system_page_404_widgets_area');
  //  };

  // System Page: Search
  $wp_customize->add_section('tinsta_system_page_search', [
    'title' => __('Search', 'tinsta'),
    'panel' => 'tinsta_system_pages',
  ]);
  $wp_customize->add_control('system_page_search_hide_widgets', [
    'label'   => __('Hide Search Widgets on search page', 'tinsta'),
    'section' => 'tinsta_system_page_search',
    'type'    => 'checkbox',
  ]);
  $wp_customize->add_control('system_page_search_search_field', [
    'label'   => __('Show theme\'s search box in search page', 'tinsta'),
    'section' => 'tinsta_system_page_search',
    'type'    => 'checkbox',
  ]);

  /**
   * Post Types
   */
  $wp_customize->add_panel('tinsta_post_types', [
    'title' => __('Post Types', 'tinsta'),
  ]);

  // Page Type: {post_type}
  foreach (get_post_types(['public' => true], 'objects') as $post_type) {

    $support_archives = ! ($post_type->has_archive === 0 || $post_type->has_archive === false) || $post_type->name == 'post';

    $region_name              = "tinsta_post_type_{$post_type->name}";
    $post_type_base_control_id = "post_type_{$post_type->name}";
    $singular_label = empty($post_type->labels->singular_name) ? $post_type->label : $post_type->labels->singular_name;

    $wp_customize->add_section($region_name, [
      'title' => $post_type->label,
      'panel' => 'tinsta_post_types',
    ]);

    // If there is some region for post type in format post_type_{post_type}
    // registered by plugin outside page types, then move to tinsta panel.
    if ($wp_customize->get_setting("post_type_{$post_type->name}")) {
      $wp_customize->get_setting("post_type_{$post_type->name}")->panel = 'tinsta_page_types';
    }

    $wp_customize->add_control("{$post_type_base_control_id}_use_defaults", [
      'label'   => __('Use Default Views', 'tinsta'),
      'section' => $region_name,
      'type'    => 'checkbox',
    ]);

    $wp_customize->add_control("{$post_type_base_control_id}_layout", [
      'label'   => sprintf(__('Single %s layout', 'tinsta'), $singular_label),
      'section' => $region_name,
      'type'    => 'select',
      // Mode @see &.site-entries-singular { ... } from _entries.scss
      'choices' => apply_filters('tinsta_post_type_layouts_single', [
        ''                  => __('Default', 'tinsta'),
        'left-thumbnail'    => __('Left Thumbnail', 'tinsta'),
        'right-thumbnail'   => __('Right Thumbnail', 'tinsta'),
        'contextual-header' => __('Contextual Header', 'tinsta'),
        'catalog-item'      => __('Catalog Item', 'tinsta'),
        'widgets-area'      => __('Widgets Area', 'tinsta'),
      ], $post_type->name),
    ]);

    $wp_customize->add_control("{$post_type_base_control_id}_append_authors", [
      'label'   => __('Add author\'s bio at end of content', 'tinsta'),
      'section' => $region_name,
      'type'    => 'checkbox',
    ]);

    $wp_customize->add_control("{$post_type_base_control_id}_append_post_nav", [
      'label'   => __('Add parent/child navigation', 'tinsta'),
      'section' => $region_name,
      'type'    => 'checkbox',
    ]);

    $wp_customize->add_control("{$post_type_base_control_id}_outdated_notification", [
      'label'   => __('Add outdated content notification', 'tinsta'),
      'description' => sprintf(
        __('The notification message could be edited from <a href="%s">the component</a>.', 'tinsta'),
        'javascript:wp.customize.section(\'tinsta_component_outdated_post\').focus();'
      ),
      'section' => $region_name,
      'type'    => 'checkbox',
    ]);

    if ($support_archives) {
      $wp_customize->add_control("{$post_type_base_control_id}_layout_archive", [
        'label'   => __('Archive Layout', 'tinsta'),
        'section' => $region_name,
        'type'    => 'select',
        // Mode @see &.site-entries-archive { ... } from _entries.scss
        'choices' => apply_filters('tinsta_post_type_layouts_archive', [
          ''                => __('Default', 'tinsta'),
          'boxes'           => __('Boxes', 'tinsta'),
          'cover-boxes'     => __('Cover Boxes', 'tinsta'),
          'timeline'        => __('Time-Line', 'tinsta'),
          'poetry'          => __('Poetry', 'tinsta'),
          'left-thumbnail'  => __('Left Thumbnail', 'tinsta'),
          'right-thumbnail' => __('Right Thumbnail', 'tinsta'),
          'widgets-area'    => __('Widgets Area', 'tinsta'),
        ], $post_type->name),
      ]);

      $wp_customize->add_control("{$post_type_base_control_id}_archive_show", [
        'label'   => __('Display Archive Post Content as', 'tinsta'),
        'section' => $region_name,
        'type' => 'select',
        'choices' => [
          'excerpt' => __('Excerpt', 'tinsta'),
          'full' => __('Full Text', 'tinsta'),
        ],
        'active_callback' => function() use ($post_type_base_control_id) {
          return get_theme_mod("{$post_type_base_control_id}_layout_archive") != 'widgets-area';
        },
      ]);

      $wp_customize->add_control("{$post_type_base_control_id}_archive_show_excerpt_words", [
        'label'       => __('Excerpt number of words', 'tinsta'),
        'section' => $region_name,
        'type'        => 'number',
        'input_attrs' => [
          'min'   => 0,
          'step'  => 1,
          'style' => 'width:6em;',
        ],
        'active_callback' => function() use ($post_type_base_control_id) {
          return
            get_theme_mod("{$post_type_base_control_id}_layout_archive") != 'widgets-area'
            && get_theme_mod("{$post_type_base_control_id}_archive_show") == 'excerpt';
        },
      ]);

    }

  }


  /**
   * Tinsta theme misc
   */
  $wp_customize->add_section('tinsta_misc', [
    'title' => __('Miscellaneous', 'tinsta'),
  ]);
  $wp_customize->add_control('effects_smooth_scroll', [
    'type'        => 'checkbox',
    'label'       => __('Smooth Scroll', 'tinsta'),
    'description' => __('Smooth scroll is nice effect, but may decrease the scrolling performance.', 'tinsta'),
    'section' => 'tinsta_misc',
  ]);
  $wp_customize->add_control('effects', [
    'type'        => 'checkbox',
    'label'       => __('Enable theme effects', 'tinsta'),
    'section' => 'tinsta_misc',
    'description' => __('Enable theme effects like shadows, animations and etc.', 'tinsta'),
  ]);
  $wp_customize->add_control('effects_lazyload', [
    'type'        => 'checkbox',
    'label'       => __('Image Lazy Loading', 'tinsta'),
    'section'     => 'tinsta_misc',
  ]);
  $wp_customize->add_control('misc_seo', [
    'type'        => 'checkbox',
    'label'       => __('SEO Helpers', 'tinsta'),
    'section'     => 'tinsta_misc',
  ]);


  // Remove forced mods controls, it is a bit stupid to add and then to remove,
  // but the cases mods are overriden are not supposed to be very often, also it will require,
  // huge effort to wrap every control into if().
  foreach ($forced_theme_mods as $mod => $value) {
    $wp_customize->remove_control($mod);
  }

});
