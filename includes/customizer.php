<?php

/**
 * @file
 * Setup customizer.
 */


/**
 * Enqueue scripts and styles.
 */
if (!empty($_POST['customized'])) {
  add_action('wp_enqueue_scripts', function () {
    wp_localize_script('tinsta', 'tinstaCustomized', json_decode(wp_unslash($_POST['customized']), TRUE));
  });
}

/**
 * Add metabox for Tinsta's items in the customizer.
 */
add_filter('customize_nav_menu_available_item_types', function($menu_types) {
  $menu_types[] = [
    'title' => __('Dynamic Content', 'tinsta'),
    'type_label' => __('Dynamic Content', 'tinsta'),
    'type' => 'tinsta-menu-item',
    'object' => 'tinsta-nav-menu-object',
  ];
  return $menu_types;
});

/**
 * Add Tinsta's custom menu item to customizer's metabox.
 */
add_filter( 'customize_nav_menu_available_items', function( $items = [], $type = '', $object = '', $page = 0 ) {
  if ( 'tinsta-nav-menu-object' !== $object ) {
    return $items;
  }
  return array_merge( $items, array_values(tinsta_nav_menu_items()) );
}, 10, 4);


/**
 * Setup base region setting controls
 *
 * @param \WP_Customize_Manager
 * @param string $region_slug
 */
function tinsta_customizer_setup_color_controls($wp_customize, $region_slug)
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
      return get_theme_mod($region_slug . '_image') ? true : false;
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
      return get_theme_mod($region_slug . '_image') ? true : false;
    },
  ]);
  $wp_customize->add_control("{$region_slug}_image_repeat", [
    'label'           => __('Repeat Background Image', 'tinsta'),
    'section' => $customizer_region_name,
    'type'            => 'checkbox',
    'active_callback' => function () use ($region_slug) {
      return get_theme_mod($region_slug . '_image') ? true : false;
    },
  ]);
  $wp_customize->add_control("{$region_slug}_image_attachment_scroll", [
    'label'           => __('Scroll with Page', 'tinsta'),
    'section' => $customizer_region_name,
    'type'            => 'checkbox',
    'active_callback' => function () use ($region_slug) {
      return get_theme_mod($region_slug . '_image') ? true : false;
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

  $public_post_types = apply_filters('tinsta_supported_customizer_post_types', get_post_types(['public' => true], 'objects'));

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

      // Basic sanitization.
      'sanitize_callback' => function ($value) {
        return strval($value);
      },

      // Basic validation. There is no universal formula to validate values,
      // so until found some magic recipe or create more robust variable registering,
      // we will have no such validation
      'validation_callback' => function ($value) {
        return is_scalar($value);
      },

    ]);
  }

  // Typography
  $wp_customize->add_panel('tinsta_typography', [
    'title'    => __('Basics', 'tinsta'),
    'description' => __('Adjust typography parameters and some other design basics.', 'tinsta'),
    'priority' => 20,
  ]);
  $wp_customize->add_section('tinsta_typography', [
    'title'    => __('Text', 'tinsta'),
    'panel' => 'tinsta_typography',
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
  $wp_customize->add_control('typography_font_google', [
    'label'       => __('Include Google Font', 'tinsta'),
    'section' => 'tinsta_typography',
    'description' => sprintf(
      __('Use font name from %s', 'tinsta'),
      '<a target="_blank" href="https://fonts.google.com/" rel="noopener">google fonts</a>'),
  ]);
  $wp_customize->add_control('typography_font_family', [
    'label'   => __('Font-Family', 'tinsta'),
    'section' => 'tinsta_typography',
    'type'    => 'textarea',
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


  // Typography: Headings
  $wp_customize->add_section('tinsta_typography_headings', [
    'title'    => __('Headings', 'tinsta'),
    'panel' => 'tinsta_typography',
    'priority' => 20,
  ]);
  $wp_customize->add_control('typography_font_headings_style', [
    //    'label'   => __('Headings Style', 'tinsta'),
    'label' => __('Style', 'tinsta'),
    'section' => 'tinsta_typography_headings',
    'type'    => 'select',
    'choices' => [
      ''               => __('Default', 'tinsta'),
      'uppercase'      => __('Uppercase', 'tinsta'),
      'all-small-caps' => __('All Small Caps', 'tinsta'),
      'small-caps'     => __('Small Caps', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('typography_font_headings_google', [
    'label'       => __('Include Google Font', 'tinsta'),
    'section' => 'tinsta_typography_headings',
    'description' => sprintf(
      __('Use font name from %s', 'tinsta'),
      '<a target="_blank" href="https://fonts.google.com/" rel="noopener">google fonts</a>'),
  ]);
  $wp_customize->add_control('typography_font_family_headings', [
    'label'   => __('Font-Family', 'tinsta'),
    'section' => 'tinsta_typography_headings',
    'description' => __('Leave blank to inherit global font-family from text.', 'tinsta'),
    'type'    => 'textarea',
  ]);

  // Typography: Other Elements
  $wp_customize->add_section('tinsta_typography_forms', [
    'title'    => __('Forms', 'tinsta'),
    'panel' => 'tinsta_typography',
    'priority' => 20,
  ]);
  $wp_customize->add_control('typography_form_button_style', [
    'label'   => __('Button Style', 'tinsta'),
    'section' => 'tinsta_typography_forms',
    'type'    => 'select',
    'choices' => [
      ''       => __('None', 'tinsta'),
      'fill'   => __('Fill', 'tinsta'),
      'border' => __('Border', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('typography_font_button_text_style', [
    'label'   => __('Button Text Style', 'tinsta'),
    'section' => 'tinsta_typography_forms',
    'type'    => 'select',
    'choices' => [
      ''               => __('Default', 'tinsta'),
      'uppercase'      => __('Uppercase', 'tinsta'),
      'all-small-caps' => __('All Small Caps', 'tinsta'),
      'small-caps'     => __('Small Caps', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('typography_form_spacing', [
    'label'       => __('Forms Field Spacing', 'tinsta') . ' (%)',
    'section' => 'tinsta_typography_forms',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 100,
      'style' => 'width:6em;',
    ],
  ]);

  // Typography/Other elements
  $wp_customize->add_section('tinsta_typography_elements', [
    'title'    => __('Features', 'tinsta'),
    'panel' => 'tinsta_typography',
    'priority' => 20,
  ]);
  $wp_customize->add_control('typography_brightness', [
    'type'        => 'number',
    'label'       => __('Brightness', 'tinsta') . ' (%)',
    'section' => 'tinsta_typography_elements',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 100,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('typography_bordering', [
    'label'       => __('Bordering', 'tinsta') . ' (px)',
    'section' => 'tinsta_typography_elements',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 5,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('typography_roundness', [
    'label'       => __('Roundness', 'tinsta'),
    'section' => 'tinsta_typography_elements',
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
    'title'    => __('Regions Design', 'tinsta'),
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
    'label'   => __('Full Height', 'tinsta'),
    'description' => __('If page is shorten than a window height, this will apply JS fix to make it full height. Most noticeable if have footer or bottomline', 'tinsta'),
    'section' => 'tinsta_region_root',
  ]);
  $wp_customize->add_control('region_root_width', [
    'label'       => __('Page Width', 'tinsta') . ' (px)',
    'section' => 'tinsta_region_root',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 720,
      'max'   => 1440,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('region_root_breakpoint_tablet', [
    'label'   => __('Tablet Breakpoint', 'tinsta'),
    'description' => __('Upper boundary to switch tablet view.', 'tinsta'),
    'section' => 'tinsta_region_root',
    'type'    => 'select',
    'choices' => [
      '1100px' => '1100px',
      '1024px' => '1024px',
      '960px' => '960px',
      '920px' => '920px',
      '800px' => '800px',
    ],
  ]);
  $wp_customize->add_control('region_root_breakpoint_mobile', [
    'label'   => __('Mobile Breakpoint', 'tinsta'),
    'description' => __('Upper boundary to switch mobile view.', 'tinsta'),
    'section' => 'tinsta_region_root',
    'type'    => 'select',
    'choices' => [
      '768px' => '768px',
      '720px' => '720px',
      '640px' => '640px',
      '568px' => '568px',
      '480px' => '480px',
    ],
  ]);
  tinsta_customizer_setup_color_controls($wp_customize, 'root');
  tinsta_customizer_setup_background_controls($wp_customize, 'root');

  // Region: Topline
  $wp_customize->add_section('tinsta_region_topline', [
    'title' => __('Topline', 'tinsta'),
    'panel' => 'tinsta_regions',
    'description' => '<p>'
      . sprintf(
        __('This region does not holding widgets, but display unfiltered HTML. You can edit the content from <a href="%s">here</a>', 'tinsta'),
        'javascript:wp.customize.control(\'component_site_topline\').focus();')
      . '</p>',
  ]);
  $wp_customize->add_control('region_topline_sticky', [
    'type'    => 'checkbox',
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
  $wp_customize->add_control('region_topline_alignment', [
    'type'    => 'select',
    'label'   => __('Alignment', 'tinsta'),
    'section' => 'tinsta_region_topline',
    'choices' => [
      '' => __('Auto', 'tinsta'),
      'center' => __('Center', 'tinsta'),
      'left' => __('Left', 'tinsta'),
      'right' => __('Right', 'tinsta'),
    ]
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
  $wp_customize->add_control('region_header_alignment', [
    'type'    => 'select',
    'label'   => __('Alignment', 'tinsta'),
    'section' => 'tinsta_region_header',
    'choices' => [
      '' => __('Auto', 'tinsta'),
      'center' => __('Center', 'tinsta'),
      'left' => __('Left', 'tinsta'),
      'right' => __('Right', 'tinsta'),
    ]
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
    'title' => __('Primary Site Menu', 'tinsta'),
    'panel' => 'tinsta_regions',
    'description' => '
        <p>
          <a href="javascript:wp.customize.control(\'nav_menu_locations[main]\').focus();">
            ' . __('Edit <strong>Primary Menu</strong>.', 'tinsta') . '
          </a>
        </p>
    ',
    'active_callback' => function () {
      return has_nav_menu('main');
    }
  ]);
  $wp_customize->add_control('region_primary_menu_sticky', [
    'type'    => 'checkbox',
    'label'   => __('Sticky', 'tinsta'),
    'section' => 'tinsta_region_primary_menu',
    'active_callback' => function () {
      $position = get_theme_mod('region_primary_menu_position');
      return in_array($position, [ 'before-header', 'after-header' ])
        && !get_theme_mod('region_header_sticky')
        && $position != 'bottom-float';
    },
  ]);
  $wp_customize->add_control('region_primary_menu_position', [
    'label'   => __('Position', 'tinsta'),
    'section' => 'tinsta_region_primary_menu',
    'type'    => 'select',
    'choices' => [
      'before-header' => __('Before Header', 'tinsta'),
      'after-header' => __('After Header', 'tinsta'),
      'prepend-header' => __('Prepend to Header', 'tinsta'),
      'append-header' => __('Append to Header', 'tinsta'),
      'bottom-float' => __('Stuck to Bottom', 'tinsta'),
    ],
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
  $wp_customize->add_control('region_primary_menu_alignment', [
    'type'    => 'select',
    'label'   => __('Alignment', 'tinsta'),
    'section' => 'tinsta_region_primary_menu',
    'choices' => [
      '' => __('Auto', 'tinsta'),
      'center' => __('Center', 'tinsta'),
      'left' => __('Left', 'tinsta'),
      'right' => __('Right', 'tinsta'),
    ]
  ]);
  $wp_customize->add_control('region_primary_menu_highlight_root', [
    'label'   => __('Active Root Items Style', 'tinsta'),
    'section' => 'tinsta_region_primary_menu',
    'type'    => 'select',
    'choices' => [
      '' => __('None', 'tinsta'),
      'underline' => __('Underline', 'tinsta'),
      'border-top' => __('Border', 'tinsta') . ' - ' . __('Top', 'tinsta'),
      'border-bottom' => __('Border', 'tinsta') . ' - ' . __('Bottom', 'tinsta'),
      'background' => __('Background', 'tinsta'),
      'bold' => __('Bold', 'tinsta'),
      'color-primary' => __('Primary Color', 'tinsta'),
      'color-secondary' => __('Secondary Color', 'tinsta'),
    ],
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
  $wp_customize->add_control('region_footer_alignment', [
    'type'    => 'select',
    'label'   => __('Alignment', 'tinsta'),
    'section' => 'tinsta_region_footer',
    'choices' => [
      '' => __('Auto', 'tinsta'),
      'center' => __('Center', 'tinsta'),
      'left' => __('Left', 'tinsta'),
      'right' => __('Right', 'tinsta'),
    ]
  ]);

  tinsta_customizer_setup_color_controls($wp_customize, 'footer');
  tinsta_customizer_setup_background_controls($wp_customize, 'footer');


  // Region: Bottomline
  $wp_customize->add_section('tinsta_region_bottomline', [
    'title' => __('Bottomline', 'tinsta'),
    'panel' => 'tinsta_regions',
    'description' => '<p>'
      . sprintf(
        __('This region does not holding widgets, but display unfiltered HTML. You can edit the content from <a href="%s">here</a>', 'tinsta'),
        'javascript:wp.customize.control(\'component_site_bottomline\').focus();')
      . '</p>'
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
  $wp_customize->add_control('region_bottomline_alignment', [
    'type'    => 'select',
    'label'   => __('Alignment', 'tinsta'),
    'section' => 'tinsta_region_bottomline',
    'choices' => [
      '' => __('Auto', 'tinsta'),
      'center' => __('Center', 'tinsta'),
      'left' => __('Left', 'tinsta'),
      'right' => __('Right', 'tinsta'),
    ]
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
      'top' => __('Top, Modal', 'tinsta'),
      'topfull' => __('Top, Full Width', 'tinsta'),
      'bottom' => __('Bottom, Modal', 'tinsta'),
      'bottomfull' => __('Bottom, Full Width', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('component_site_agreement_text', [
    'label'   => __('Text', 'tinsta'),
    'section' => 'tinsta_component_site_agreement',
    'type'    => 'textarea',
  ]);
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
  $wp_customize->add_control('component_breadcrumbs_title', [
    'type'    => 'text',
    'label'   => __('Label', 'tinsta'),
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

  // Component: Avatars
  $wp_customize->add_section('tinsta_component_avatar', [
    'title' => __('Avatars', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('component_avatar_size', [
    'label' => __('Size', 'tinsta') . ' (px)',
    'section' => 'tinsta_component_avatar',
    'type' => 'number',
    'input_attrs' => [
      'min' => 32,
      'max' => 128,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('component_avatar_size_small', [
    'label' => __('Small Size', 'tinsta') . ' (px)',
    'section' => 'tinsta_component_avatar',
    'type' => 'number',
    'input_attrs' => [
      'min' => 24,
      'max' => 96,
      'step' => 1,
      'style' => 'width:6em;',
    ],
  ]);

  // Component: Comments
  $wp_customize->add_section('tinsta_component_comments', [
    'title' => __('Comments', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('component_comments_style', [
    'label'   => __('Style', 'tinsta'),
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
    'description' => __('Appears in posts headers. Can be used to put AddThis or social networks buttons (like, follow, share, etc&hellip;)', 'tinsta'),
    'code_type'   => 'text/html',
  ]));

  // Component: Meta HTML
  $wp_customize->add_section('tinsta_component_header_markup', [
    'title'    => sprintf(__('Custom %s HTML', 'tinsta'), '<head>'),
    'panel'    => 'tinsta_components',
    'priority' => 200,
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'component_header_markup', [
    'section' => 'tinsta_component_header_markup',
    'description' => sprintf(__('Useful in case of adding extra 3rd party JS, CSS or HTML tags like analytics or etc. Insert just before %s tag.', 'tinsta'), esc_html('</head>')),
    'code_type'   => 'text/html',
  ]));

  // Component: Footer HTML
  $wp_customize->add_section('tinsta_component_footer_markup', [
    'title'    => sprintf(__('Custom %s HTML', 'tinsta'), '<body>'),
    'panel'    => 'tinsta_components',
    'priority' => 200,
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'component_footer_markup', [
    'section' => 'tinsta_component_footer_markup',
    'description' => sprintf(__('Useful in case of adding extra 3rd party JS, CSS or HTML tags like analytics or etc. Insert just before %s tag.', 'tinsta'), esc_html('</body>')),
    'code_type'   => 'text/html',
  ]));

  // Component: Custom CSS
  $wp_customize->get_section('custom_css')->panel    = 'tinsta_components';
  $wp_customize->get_section('custom_css')->priority = 210;

  // Component: Effects
  $wp_customize->add_section('tinsta_component_effects', [
    'title'    => __('Effects', 'tinsta'),
    'panel'    => 'tinsta_components',
    'priority' => 190,
  ]);
  $wp_customize->add_control('component_effects_lazyload', [
    'section'     => 'tinsta_component_effects',
    'type'        => 'checkbox',
    'label'       => __('Image Lazy Loading', 'tinsta'),
  ]);
  $wp_customize->add_control('component_effects_smooth_scroll', [
    'type'        => 'checkbox',
    'label'       => __('Smooth Scroll', 'tinsta'),
    'description' => __('Smooth scroll is nice effect, but may decrease the scrolling performance.', 'tinsta'),
    'section' => 'tinsta_component_effects',
  ]);
  $wp_customize->add_control('component_effects_animations', [
    'section'     => 'tinsta_component_effects',
    'type'        => 'select',
    'label'       => __('(TODO) Animations', 'tinsta'),
    'description' => __('Enable animation effect for some elements.', 'tinsta'),
    'choices' => [
      0 => __('Disabled', 'tinsta'),
      150 => __('Fast', 'tinsta'),
      250 => __('Normal', 'tinsta'),
      500 => __('Slow', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('component_effects_shadows', [
    'section'     => 'tinsta_component_effects',
    'type'        => 'select',
    'label'       => __('(TODO) Shadows', 'tinsta'),
    'description' => __('Enable shadow effect for some elements.', 'tinsta'),
    'choices' => [
      0 => __('Disabled', 'tinsta'),
      2 => __('Light', 'tinsta'),
      10 => __('Big', 'tinsta'),
    ],
  ]);


  // Component: SEO
  $wp_customize->add_section('tinsta_component_seo', [
    'title' => __('SEO', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('component_seo_enable', [
    'type'        => 'checkbox',
    'label'       => __('Enable SEO Helpers', 'tinsta'),
    'section'     => 'tinsta_component_seo',
  ]);


  /**
   * System Pages
   */
  $wp_customize->add_panel('tinsta_system_pages', [
    'title' => __('System Pages', 'tinsta'),
  ]);

  // System Page: Homepage settings
  $wp_customize->get_section('static_front_page')->panel = 'tinsta_system_pages';

  // System Page: Login and Register
  $wp_customize->add_section('tinsta_system_page_login', [
    'title' => __('Login & Register', 'tinsta'),
    'panel' => 'tinsta_system_pages',
  ]);
  $wp_customize->add_control('system_page_login_theming', [
    'label'   => __('Style', 'tinsta'),
    'section' => 'tinsta_system_page_login',
    'type'    => 'select',
    'choices' => [
      ''      => 'WordPress',
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
  $wp_customize->add_control('system_page_404_content', [
    'label'   => __('Content', 'tinsta'),
    'section' => 'tinsta_system_page_404',
    'type'    => 'select',
    'choices' => [
      '' => __('Default', 'tinsta'),
      'widgets' => __('Widgets Area', 'tinsta'),
    ],
  ]);

  // System Page: Search
  $wp_customize->add_section('tinsta_system_page_search', [
    'title' => __('Search', 'tinsta'),
    'panel' => 'tinsta_system_pages',
  ]);
  $wp_customize->add_control('system_page_search_disable_search', [
    'label'   => __('Globally disable search', 'tinsta'),
    'section' => 'tinsta_system_page_search',
    'type'    => 'checkbox',
  ]);
  $post_types = [
    '' => '&mdash; ' . __('All', 'tinsta') . ' &mdash;',
  ];
  foreach ($public_post_types as $post_type) {
    $post_types["{$post_type->name}"] = $post_type->label;
  }
  $wp_customize->add_control('system_page_search_force_post_type', [
    'label'   => __('Force search to find only', 'tinsta'),
    'section' => 'tinsta_system_page_search',
    'type'    => 'select',
    'choices' => $post_types,
  ]);

  /**
   * Post Types
   */
  $wp_customize->add_panel('tinsta_post_types', [
    'title' => __('Post Types', 'tinsta'),
  ]);

  // Page Type: {post_type}
  foreach ($public_post_types as $post_type) {

    $support_archives = ! ($post_type->has_archive === 0 || $post_type->has_archive === false) || $post_type->name == 'post';

    $region_name  = "tinsta_post_type_{$post_type->name}";
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

    // experiment
    //    $wp_customize->add_control("{$post_type_base_control_id}_elements_", [
    //      'label'   => sprintf(__('Single %s layout', 'tinsta'), $singular_label),
    //      'section' => $region_name,
    //      'type'    => 'select',
    //      // Mode @see &.site-entries-singular { ... } from _entries.scss
    //      'choices' => apply_filters('tinsta_post_type_layouts_single', [
    //        ''                  => __('Default', 'tinsta'),
    //        'left-thumbnail'    => __('Left Thumbnail', 'tinsta'),
    //        'right-thumbnail'   => __('Right Thumbnail', 'tinsta'),
    //        'contextual-header' => __('Contextual Header', 'tinsta'),
    //        'catalog-item'      => __('Catalog Item', 'tinsta'),
    //        'widgets-area'      => __('Widgets Area', 'tinsta'),
    //      ], $post_type->name),
    //    ]);
    // /experiment

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
        'active_callback' => function () use ($post_type_base_control_id) {
          return get_theme_mod("{$post_type_base_control_id}_layout_archive") != 'widgets-area';
        },
      ]);

      $wp_customize->add_control("{$post_type_base_control_id}_archive_show_excerpt_words", [
        'label'       => __('Number of words in excerpts', 'tinsta'),
        'section' => $region_name,
        'type'        => 'number',
        'input_attrs' => [
          'min'   => 0,
          'step'  => 1,
          'style' => 'width:6em;',
        ],
        'active_callback' => function () use ($post_type_base_control_id) {
          return
            get_theme_mod("{$post_type_base_control_id}_layout_archive") != 'widgets-area'
            && get_theme_mod("{$post_type_base_control_id}_archive_show") == 'excerpt';
        },
      ]);

      $wp_customize->add_control("{$post_type_base_control_id}_archive_per_page", [
        'label'       => __('Per Page', 'tinsta'),
        'section' => $region_name,
        'type'        => 'number',
        'input_attrs' => [
          'min'   => 0,
          'step'  => 1,
          'style' => 'width:6em;',
        ],
      ]);

    }

  }

  // Remove forced mods controls, it is a bit stupid to add and then to remove,
  // but the cases mods are overriden are not supposed to be very often (only on child theme or specific plugin)
  // also it will require huge effort to wrap every control into if().
  foreach ($forced_theme_mods as $mod => $value) {
    $wp_customize->remove_control($mod);
  }

  // Re-arrange third-party plugin's controls and sections.
  if ($wp_customize->get_section('tt_font_typography')) {
    $wp_customize->get_section('tt_font_typography')->panel = 'tinsta_typography';
  }


});
