<?php

/**
 * @file
 * Setup customizer.
 */


/**
 * Check if Tinsta theme should add customizer
 *
 * @return bool
 */
function tinsta_is_customizer_enabled()
{
  return apply_filters('tinsta_is_customizer_enabled', true);
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
      $args['variables'] = $customizer_patched_data_values;
      $args['preview_is_updated'] = true;
    }

  }

  // @TODO make this works because it filter vars that are not in the defaults, such like post type related.
  $args['preview_is_updated'] = true;

  $args['preview'] = true;

  return $args;
}, 1000);

/**
 * Register setting controls
 */
add_action('customize_register', function ($wp_customize) {

  /** @var $wp_customize \WP_Customize_Manager */

  // Remove built-in color customizer.
  $wp_customize->remove_section('colors');

  if ( ! tinsta_is_customizer_enabled()) {
    return;
  }

  // Forced theme options.
  $forced_theme_mods = apply_filters('tinsta_force_options', []);

  // Register all theme mods as settings in customizer.
  foreach (tinsta_get_options_defaults() as $option_name => $value) {

    // Do not add settings for forced options.
    if (array_key_exists($option_name, $forced_theme_mods)) {
      continue;
    }

    $wp_customize->add_setting($option_name, [
      'type'      => 'theme_mod',
      'default'   => $value,
      'transport' => 'refresh',
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
    'label'       => __('Base font size (px)', 'tinsta'),
    'section'     => 'tinsta_typography',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 10,
      'max'   => 20,
      'step' => 0.5,
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
    'label'   => __('Base font family', 'tinsta'),
    'section' => 'tinsta_typography',
    'type'    => 'textarea',
  ]);
  $wp_customize->add_control('typography_font_family_headings', [
    'label'   => sprintf(__('(Headings) %s', 'tinsta'), __('Base font family', 'tinsta')),
    'section' => 'tinsta_typography',
    'type'    => 'textarea',
  ]);

  // Use select for google font names, but if there is some problem, fallback to text input.
  $wp_customize->add_control('typography_font_google', [
    'label'       => __('Google Font', 'tinsta'),
    'section'     => 'tinsta_typography',
    'description' => sprintf(__('Use font name from %s', 'tinsta'), '<a target="_blank" href="https://fonts.google.com/" rel="noopener">google fonts</a>'),
  ]);
  $wp_customize->add_control('typography_font_headings_google', [
    'label'       => sprintf(__('(Headings) %s', 'tinsta'), __('Google Font', 'tinsta')),
    'section'     => 'tinsta_typography',
    'description' => sprintf(__('Use font name from %s', 'tinsta'), '<a target="_blank" href="https://fonts.google.com/" rel="noopener">google fonts</a>'),
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
  $wp_customize->add_control('typography_form_spacing', [
    'label'       => __('Forms field spacing', 'tinsta'),
    'section'     => 'tinsta_typography',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 30,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('typography_form_borders', [
    'label'       => __('Forms field borders', 'tinsta'),
    'section'     => 'tinsta_typography',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 4,
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

  /*
   * Sections:
   */
  $wp_customize->add_panel('tinsta_sections', [
    'title'       => __('Sections', 'tinsta'),
    'priority'    => 25,
  ]);

  // Section: Globals
  $wp_customize->add_section('tinsta_section_root', [
    'title' => __('Site Globals', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('section_root_layout', [
    'type'    => 'select',
    'label'   => __('Layout', 'tinsta'),
    'section' => 'tinsta_section_root',
    'choices' => [
      '' => __('Default', 'tinsta'),
      'boxed' => __('Boxed', 'tinsta'),
      'fullwidth' => __('Full Width', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('section_root_height_full', [
    'type'    => 'checkbox',
    'label'   => __('Full-height Page', 'tinsta'),
    'section' => 'tinsta_section_root',
  ]);
  $wp_customize->add_control('section_root_width', [
    'label'       => __('Content Width', 'tinsta'),
    'section'     => 'tinsta_section_root',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 600,
      'max'   => 1440,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('section_root_breakpoint_desktop', [
    'label'   => __('Desktop Breakpoint', 'tinsta'),
    'section' => 'tinsta_section_root',
    'type'    => 'select',
    'choices' => [
      '1920px' => '1920px',
      '1600px' => '1600px',
      '1400px' => '1400px',
      '1200px' => '1200px',
      '1024px' => '1024px',
    ],
  ]);
  $wp_customize->add_control('section_root_breakpoint_tablet', [
    'label'   => __('Tablet Breakpoint', 'tinsta'),
    'section' => 'tinsta_section_root',
    'type'    => 'select',
    'choices' => [
      '960px' => '960px',
      '920px' => '920px',
      '800px' => '800px',
      '720px' => '720px',
    ],
  ]);
  $wp_customize->add_control('section_root_breakpoint_mobile', [
    'label'   => __('Mobile Breakpoint', 'tinsta'),
    'section' => 'tinsta_section_root',
    'type'    => 'select',
    'choices' => [
      '640px' => '640px',
      '568px' => '568px',
      '480px' => '480px',
      '320px' => '320px',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_root_color_primary', [
    'label'   => __('Primary Color', 'tinsta'),
    'section' => 'tinsta_section_root',
  ]));
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_root_color_primary_inverted', [
    'label'   => sprintf(__('Inverted %s', 'tinsta'), __('Primary Color', 'tinsta')),
    'section' => 'tinsta_section_root',
  ]));
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_root_color_secondary', [
    'label'   => __('Secondary Color', 'tinsta'),
    'section' => 'tinsta_section_root',
  ]));
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_root_color_secondary_inverted', [
    'label'   => sprintf(__('Inverted %s', 'tinsta'), __('Secondary Color', 'tinsta')),
    'section' => 'tinsta_section_root',
  ]));
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_root_color_background', [
    'label'   => __('Background', 'tinsta'),
    'section' => 'tinsta_section_root',
  ]));
  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'section_root_image', [
    'label'   => __('Background Image', 'tinsta'),
    'section' => 'tinsta_section_root',
  ]));
  $wp_customize->add_control(new WP_Customize_Background_Position_Control($wp_customize, 'section_root_image_background_position', [
    'label'    => __('Background Image Position', 'tinsta'),
    'section'  => 'tinsta_section_root',
    'settings' => [
      'x' => 'section_root_image_position_x',
      'y' => 'section_root_image_position_y',
    ],
  ]));
  $wp_customize->add_control('section_root_image_size', [
    'label'   => __('Background Image Size', 'tinsta'),
    'section' => 'tinsta_section_root',
    'type'    => 'select',
    'choices' => [
      'auto'    => __('Original', 'tinsta'),
      'contain' => __('Fit to Screen', 'tinsta'),
      'cover'   => __('Fill Screen', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('section_root_image_repeat', [
    'label'   => __('Repeat Background Image', 'tinsta'),
    'section' => 'tinsta_section_root',
    'type'    => 'checkbox',
  ]);
  $wp_customize->add_control('section_root_image_attachment_scroll', [
    'label'   => __('Scroll with Page', 'tinsta'),
    'section' => 'tinsta_section_root',
    'type'    => 'checkbox',
  ]);

  // Section: Topline
  $wp_customize->add_section('tinsta_section_topline', [
    'title' => __('Topline', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('section_topline_sticky', [
    'type'    => 'checkbox',
    'label'   => __('Sticky', 'tinsta'),
    'section' => 'tinsta_section_topline',
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_topline_color_background', [
    'label'   => __('Background', 'tinsta'),
    'section' => 'tinsta_section_topline',
  ]));
  $wp_customize->add_control('section_topline_color_background_opacity', [
    'label'       => __('Background Opacity', 'tinsta'),
    'section'     => 'tinsta_section_topline',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 100,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_topline_color_foreground', [
    'label'   => __('Foreground', 'tinsta'),
    'section' => 'tinsta_section_topline',
  ]));

  // Section: Header
  $wp_customize->add_section('tinsta_section_header', [
    'title' => __('Header', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('section_header_padding', [
    'label'       => __('Padding', 'tinsta'),
    'section'     => 'tinsta_section_header',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 120,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('section_header_sticky', [
    'type'    => 'checkbox',
    'label'   => __('Sticky', 'tinsta'),
    'section' => 'tinsta_section_header',
  ]);
  $wp_customize->add_control('section_header_background_wrapper', [
    'type'    => 'checkbox',
    'label'   => __('Background on Wrapper', 'tinsta'),
    'section' => 'tinsta_section_header',
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_header_color_background', [
    'label'   => __('Background', 'tinsta'),
    'section' => 'tinsta_section_header',
  ]));
  $wp_customize->add_control('section_header_color_background_opacity', [
    'label'       => __('Background Opacity', 'tinsta'),
    'section'     => 'tinsta_section_header',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 100,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_header_color_foreground', [
    'label'   => __('Foreground', 'tinsta'),
    'section' => 'tinsta_section_header',
  ]));
  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'section_header_image', [
    'label'   => __('Background Image', 'tinsta'),
    'section' => 'tinsta_section_header',
  ]));
  $wp_customize->add_control(new WP_Customize_Background_Position_Control($wp_customize, 'section_header_image_background_position', [
    'label'    => __('Background Image Position', 'tinsta'),
    'section'  => 'tinsta_section_header',
    'settings' => [
      'x' => 'section_header_image_position_x',
      'y' => 'section_header_image_position_y',
    ],
  ]));
  $wp_customize->add_control('section_header_image_size', [
    'label'   => __('Background Image Size', 'tinsta'),
    'section' => 'tinsta_section_header',
    'type'    => 'select',
    'choices' => [
      'auto'    => __('Original', 'tinsta'),
      'contain' => __('Fit to Screen', 'tinsta'),
      'cover'   => __('Fill Screen', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('section_header_image_repeat', [
    'label'   => __('Repeat Background Image', 'tinsta'),
    'section' => 'tinsta_section_header',
    'type'    => 'checkbox',
  ]);
  $wp_customize->add_control('section_header_image_attachment_scroll', [
    'label'   => __('Scroll with Page', 'tinsta'),
    'section' => 'tinsta_section_header',
    'type'    => 'checkbox',
  ]);

  // Section: Primary Menu
  $wp_customize->add_section('tinsta_section_primary_menu', [
    'title' => __('Primary Site Menu', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('section_primary_menu_movetop', [
    'label'   => __('Move above Header', 'tinsta'),
    'section' => 'tinsta_section_primary_menu',
    'type'    => 'checkbox',
  ]);
  $wp_customize->add_control('section_primary_menu_aligncenter', [
    'label'   => __('Center', 'tinsta'),
    'section' => 'tinsta_section_primary_menu',
    'type'    => 'checkbox',
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_primary_menu_color_background', [
    'label'   => __('Background', 'tinsta'),
    'section' => 'tinsta_section_primary_menu',
  ]));
  $wp_customize->add_control('section_primary_menu_color_background_opacity', [
    'label'       => __('Background Opacity', 'tinsta'),
    'section'     => 'tinsta_section_primary_menu',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 100,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_primary_menu_color_foreground', [
    'label'   => __('Foreground', 'tinsta'),
    'section' => 'tinsta_section_primary_menu',
  ]));

  // Section: Main
  $wp_customize->add_section('tinsta_section_main', [
    'title' => __('Main', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_main_color_background', [
    'label'   => __('Background', 'tinsta'),
    'section' => 'tinsta_section_main',
  ]));
  $wp_customize->add_control('section_main_color_background_opacity', [
    'label'       => __('Background Opacity', 'tinsta'),
    'section'     => 'tinsta_section_main',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 100,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_main_color_foreground', [
    'label'   => __('Foreground', 'tinsta'),
    'section' => 'tinsta_section_main',
  ]));
  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'section_main_image', [
    'label'   => __('Background Image', 'tinsta'),
    'section' => 'tinsta_section_main',
  ]));
  $wp_customize->add_control(new WP_Customize_Background_Position_Control($wp_customize, 'section_main_image_background_position', [
    'label'    => __('Background Image Position', 'tinsta'),
    'section'  => 'tinsta_section_main',
    'settings' => [
      'x' => 'section_main_image_position_x',
      'y' => 'section_main_image_position_y',
    ],
  ]));
  $wp_customize->add_control('section_main_image_size', [
    'label'   => __('Background Image Size', 'tinsta'),
    'section' => 'tinsta_section_main',
    'type'    => 'select',
    'choices' => [
      'auto'    => __('Original', 'tinsta'),
      'contain' => __('Fit to Screen', 'tinsta'),
      'cover'   => __('Fill Screen', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('section_main_image_repeat', [
    'label'   => __('Repeat Background Image', 'tinsta'),
    'section' => 'tinsta_section_main',
    'type'    => 'checkbox',
  ]);
  $wp_customize->add_control('section_main_image_attachment_scroll', [
    'label'   => __('Scroll with Page', 'tinsta'),
    'section' => 'tinsta_section_main',
    'type'    => 'checkbox',
  ]);

  // Section: Sidebar
  $wp_customize->add_section('tinsta_section_sidebar_primary', [
    'title' => __('Primary Sidebar', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('section_sidebar_primary_width', [
    'label'       => __('Width', 'tinsta'),
    'section'     => 'tinsta_section_sidebar_primary',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 600,
      'max'   => 1440,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_sidebar_primary_color_background', [
    'label'   => __('Background', 'tinsta'),
    'section' => 'tinsta_section_sidebar_primary',
  ]));
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_sidebar_primary_color_foreground', [
    'label'   => __('Foreground', 'tinsta'),
    'section' => 'tinsta_section_sidebar_primary',
  ]));

  // Section: Sidebar Secondary
  $wp_customize->add_section('tinsta_section_sidebar_secondary', [
    'title' => __('Secondary Sidebar', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('section_sidebar_secondary_width', [
    'label'       => __('Width', 'tinsta'),
    'section'     => 'tinsta_section_sidebar_secondary',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 100,
      'max'   => 300,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_sidebar_secondary_color_background', [
    'label'   => __('Background', 'tinsta'),
    'section' => 'tinsta_section_sidebar_secondary',
  ]));
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_sidebar_secondary_color_foreground', [
    'label'   => __('Foreground', 'tinsta'),
    'section' => 'tinsta_section_sidebar_secondary',
  ]));

  // Section: Footer
  $wp_customize->add_section('tinsta_section_footer', [
    'title' => __('Footer', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('section_footer_background_highlight_inner', [
    'type'    => 'checkbox',
    'label'   => __('Highlight inner Background', 'tinsta'),
    'section' => 'tinsta_section_footer',
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_footer_color_background', [
    'label'   => __('Background', 'tinsta'),
    'section' => 'tinsta_section_footer',
  ]));
  $wp_customize->add_control('section_footer_color_background_opacity', [
    'label'       => __('Background Opacity', 'tinsta'),
    'section'     => 'tinsta_section_footer',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 100,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_footer_color_foreground', [
    'label'   => __('Foreground', 'tinsta'),
    'section' => 'tinsta_section_footer',
  ]));
  $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'section_footer_image', [
    'label'   => __('Background Image', 'tinsta'),
    'section' => 'tinsta_section_footer',
  ]));
  $wp_customize->add_control(new WP_Customize_Background_Position_Control($wp_customize, 'section_footer_image_background_position', [
    'label'    => __('Background Image Position', 'tinsta'),
    'section'  => 'tinsta_section_footer',
    'settings' => [
      'x' => 'section_footer_image_position_x',
      'y' => 'section_footer_image_position_y',
    ],
  ]));
  $wp_customize->add_control('section_footer_image_size', [
    'label'   => __('Background Image Size', 'tinsta'),
    'section' => 'tinsta_section_footer',
    'type'    => 'select',
    'choices' => [
      'auto'    => __('Original', 'tinsta'),
      'contain' => __('Fit to Screen', 'tinsta'),
      'cover'   => __('Fill Screen', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('section_footer_image_repeat', [
    'label'   => __('Repeat Background Image', 'tinsta'),
    'section' => 'tinsta_section_footer',
    'type'    => 'checkbox',
  ]);
  $wp_customize->add_control('section_footer_image_attachment_scroll', [
    'label'   => __('Scroll with Page', 'tinsta'),
    'section' => 'tinsta_section_footer',
    'type'    => 'checkbox',
  ]);

  // Section: Bottomline
  $wp_customize->add_section('tinsta_section_bottomline', [
    'title' => __('Bottomline', 'tinsta'),
    'panel' => 'tinsta_sections',
  ]);
  $wp_customize->add_control('section_bottomline_sticky', [
    'type'    => 'checkbox',
    'label'   => __('Sticky', 'tinsta'),
    'section' => 'tinsta_section_bottomline',
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_bottomline_color_background', [
    'label'   => __('Background', 'tinsta'),
    'section' => 'tinsta_section_bottomline',
  ]));
  $wp_customize->add_control('section_bottomline_color_background_opacity', [
    'label'       => __('Background Opacity', 'tinsta'),
    'section'     => 'tinsta_section_bottomline',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 100,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'section_bottomline_color_foreground', [
    'label'   => __('Foreground', 'tinsta'),
    'section' => 'tinsta_section_bottomline',
  ]));


  /*
   * Theme components.
   */
  $wp_customize->add_panel('tinsta_components', [
    'priority'    => 30,
    'title'       => __('Components', 'tinsta'),
    'description' => __('Configure theme components', 'tinsta'),
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

  // Component: Context Header
  $wp_customize->add_section('tinsta_component_context_header', [
    'title' => __('Context Header', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('component_context_header_date_format', [
    'label'       => __('Date format', 'tinsta'),
    'section'     => 'tinsta_component_context_header',
    'description' => __('Use <a href="http://php.net/manual/bg/function.date.php" target="_blank" rel="noopener">PHP date</a> format.', 'tinsta'),
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
      'borders' => __('Borders', 'tinsta'),
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
      ''             => __('Hidden', 'tinsta'),
      'top-right'    => sprintf('%s - %s', __('Top', 'tinsta'), __('Right', 'tinsta')),
      'top-left'     => sprintf('%s - %s', __('Top', 'tinsta'), __('Left', 'tinsta')),
      'bottom-right' => sprintf('%s - %s', __('Bottom', 'tinsta'), __('Right', 'tinsta')),
      'bottom-left'  => sprintf('%s - %s', __('Bottom', 'tinsta'), __('Left', 'tinsta')),
    ],
  ]);

  // Component: Outdated posts
  $wp_customize->add_section('tinsta_component_outdated_post', [
    'title' => __('Outdated Post Notification', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('component_outdated_post_time', [
    'type'        => 'number',
    'label'       => __('Time', 'tinsta'),
    'section'     => 'tinsta_component_outdated_post',
    'description' => __('In days, 0 or empty to disable.', 'tinsta'),
  ]);
  $wp_customize->add_control('component_outdated_post_message', [
    'type'        => 'textarea',
    'label'       => __('Message', 'tinsta'),
    'section'     => 'tinsta_component_outdated_post',
    'description' => __('Use %time% token to show the time ago', 'tinsta'),
  ]);

  // Component: Avatars
  $wp_customize->add_section('tinsta_component_avatar', [
    'title' => __('Avatars', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control('component_avatar_size', [
    'label'       => __('Size', 'tinsta'),
    'section'     => 'tinsta_component_avatar',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 32,
      'max'   => 128,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);
  $wp_customize->add_control('component_avatar_size_small', [
    'label'       => __('Small size', 'tinsta'),
    'section'     => 'tinsta_component_avatar',
    'type'        => 'number',
    'input_attrs' => [
      'min'   => 24,
      'max'   => 96,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);

  // Component: Topline
  $wp_customize->add_section('tinsta_component_topline', [
    'title' => __('Topline', 'tinsta'),
    'panel' => 'tinsta_components',
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'component_site_topline', [
    'section'   => 'tinsta_component_topline',
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
    'section'   => 'tinsta_component_bottomline',
    'code_type' => 'text/html',
  ]));
  $wp_customize->selective_refresh->add_partial('component_site_bottomline', [
    'selector' => '.site-bottomline-wrapper',
  ]);

  // Component: Social networks code
  $wp_customize->add_section('tinsta_component_social_networks_code', [
    'title'    => __('Social networks code', 'tinsta'),
    'panel'    => 'tinsta_components',
    'priority' => 200,
  ]);
  $wp_customize->add_control(new WP_Customize_Code_Editor_Control($wp_customize, 'component_social_networks_code', [
    'section'     => 'tinsta_component_social_networks_code',
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
    'section'     => 'tinsta_component_header_markup',
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
    'section'     => 'tinsta_component_footer_markup',
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

  // System Page: Login and Register
  $wp_customize->add_section('tinsta_system_page_login', [
    'title' => __('Login and Register', 'tinsta'),
    'panel' => 'tinsta_system_pages',
  ]);
  $wp_customize->add_control('system_page_login_theming', [
    'label'   => __('Theming', 'tinsta'),
    'section' => 'tinsta_system_page_login',
    'type'    => 'select',
    'choices' => [
      ''      => __('None', 'tinsta'),
      'brand' => __('Brand Only', 'tinsta'),
      'full'  => __('Full theme integration', 'tinsta'),
    ],
  ]);

  // System Page: 404
  $wp_customize->add_section('tinsta_system_page_404', [
    'title' => __('404', 'tinsta'),
    'panel' => 'tinsta_system_pages',
  ]);
  $wp_customize->add_control('system_page_404_theming', [
    'label'   => __('Theming', 'tinsta'),
    'section' => 'tinsta_system_page_404',
    'type'    => 'select',
    'choices' => [
      ''      => __('None', 'tinsta'),
      'full'  => __('Full theme integration', 'tinsta'),
    ]
  ]);

  // System Page: Search
  $wp_customize->add_section('tinsta_system_page_search', [
    'title' => __('Search', 'tinsta'),
    'panel' => 'tinsta_system_pages',
  ]);
  $wp_customize->add_control('system_page_search_hide_widgets', [
    'label'   => __('Hide Widgets when on Search', 'tinsta'),
    'section' => 'tinsta_system_page_search',
    'type'    => 'checkbox',
  ]);
  $wp_customize->add_control('system_page_search_search_field', [
    'label'   => __('Search Box', 'tinsta'),
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

    $section_name = "tinsta_post_type_{$post_type->name}";

    $wp_customize->add_section($section_name, [
      'title' => $post_type->label,
      'panel' => 'tinsta_post_types',
    ]);

    // @TODO in consideration next.
    // If there is some section for post type in format post_type_{post_type}
    // registered by plugin outside page types, then move to tinsta panel.
    if ( $wp_customize->get_setting("post_type_{$post_type->name}") ) {
      $wp_customize->get_setting("post_type_{$post_type->name}")->panel = 'tinsta_page_types';
    }

//    $sidebar_variant_sidebar_variants = [
//      ''       => __('Use global', 'tinsta'),
//      'shared' => __('Use own', 'tinsta'),
//    ];
//
//    if ($support_archives) {
//      $sidebar_variant_sidebar_variants['separated'] = __('Use own for Singulars and Archives', 'tinsta');
//    }
//
//    $sidebar_variant_sidebar_variants['none'] = __('None', 'tinsta');
//
//    foreach (tinsta_get_post_type_sidebar_names() as $variant => $variant_label) {
//      $wp_customize->add_setting("post_type_{$post_type->name}_sidebars_{$variant}");
//      $wp_customize->add_control("post_type_{$post_type->name}_sidebars_{$variant}", [
//        'label'   => sprintf(__('Sidebar: %s', 'tinsta'), $variant_label),
//        'section' => $section_name,
//        'type'    => 'select',
//        'choices' => $sidebar_variant_sidebar_variants,
//      ]);
//    }


    $wp_customize->add_control("post_type_{$post_type->name}_use_defaults", [
      'label'   => __('Use default views', 'tinsta'),
      'section' => $section_name,
      'type'    => 'checkbox',
    ]);

    $wp_customize->add_control("post_type_{$post_type->name}_layout", [
      'label'   => __('Singular Layout', 'tinsta'),
      'section' => $section_name,
      'type'    => 'select',
      // Mode @see &.site-entries-singular { ... } from _entries.scss
      // left-thumbnail
      'choices' => [
        ''                  => __('Default', 'tinsta'),
        'left-thumbnail'    => __('Left Thumbnail', 'tinsta'),
        'right-thumbnail'   => __('Right Thumbnail', 'tinsta'),
        'contextual-header' => __('Contextual Header', 'tinsta'),
        'widgets'           => __('Widgets', 'tinsta'),
        'page'              => __('Page', 'tinsta'),
      ],
    ]);

    // @TODO must be visible only when post_type_{$post_type->name}_layout is set to page.
    $wp_customize->add_control("post_type_{$post_type->name}_layout_page_id", [
      'label'   => __('Layout Page ID', 'tinsta'),
      'section' => $section_name,
      'type' => 'dropdown-pages',
    ]);

    $wp_customize->add_control("post_type_{$post_type->name}_append_authors", [
      'label'   => __('Append Authors Bio at end of content', 'tinsta'),
      'section' => $section_name,
      'type'    => 'checkbox',
    ]);

    if (is_post_type_hierarchical($post_type->name)) {
      $wp_customize->add_control("post_type_{$post_type->name}_append_post_nav", [
        'label'   => __('Append post navigation', 'tinsta'),
        'section' => $section_name,
        'type'    => 'checkbox',
      ]);
    }

    if ($support_archives) {
      $wp_customize->add_control("post_type_{$post_type->name}_layout_archive", [
        'label'   => __('Archive Layout', 'tinsta'),
        'section' => $section_name,
        'type'    => 'select',
        // Mode @see &.site-entries-archive { ... } from _entries.scss
        // left-thumbnail
        // right-thumbnail
        // boxes
        // timeline
        // poetry
        'choices' => [
          ''                => __('Default', 'tinsta'),
          'boxes'           => __('Boxes', 'tinsta'),
          'cover-boxes'     => __('Cover Boxes', 'tinsta'),
          'timeline'        => __('Timeline', 'tinsta'),
          'poetry'          => __('Poetry', 'tinsta'),
          'left-thumbnail'  => __('Left Thumbnail', 'tinsta'),
          'right-thumbnail' => __('Right Thumbnail', 'tinsta'),
        ],
      ]);

    }

  }


  /**
   * Tinsta theme misc
   */
  $wp_customize->add_section('tinsta_misc', [
    'title' => __('Miscellaneous', 'tinsta'),
  ]);
  $wp_customize->add_control('misc_legacy_support', [
    'type'        => 'checkbox',
    'label'       => __('Legacy browser support.', 'tinsta'),
    'description' => __('Enable legacy browsers support, it could heart the performance but will add support for old browser like IE < 10, and Chrome, Firefox and Opera versions few years.', 'tinsta'),
    'section'     => 'tinsta_misc',
  ]);
  $wp_customize->add_control('misc_excerpt_more', [
    'label'   => __('Read More Style', 'tinsta'),
    'section' => 'tinsta_misc',
    'type'    => 'select',
    'choices' => [
      ''         => __('Default', 'tinsta'),
      '&hellip;' => __('Hellip', 'tinsta'),
      '&rarr;'   => __('Arrow', 'tinsta'),
      '&#9657;'  => __('Triangle', 'tinsta'),
    ],
  ]);
  $wp_customize->add_control('misc_nice_scroll', [
    'type'    => 'checkbox',
    'label'   => __('Nice Scroll', 'tinsta'),
    'section' => 'tinsta_misc',
  ]);
  $wp_customize->add_control('effects', [
    'type'        => 'checkbox',
    'label'       => __('Enable theme effects', 'tinsta'),
    'section'     => 'tinsta_misc',
    'description' => __('Enable theme effects like shadows, animations and etc.', 'tinsta'),
  ]);
  $wp_customize->add_control('effects_roundness', [
    'type'        => 'number',
    'label'       => __('Roundness', 'tinsta'),
    'section'     => 'tinsta_misc',
    'input_attrs' => [
      'min'   => 0,
      'max'   => 10,
      'step'  => 1,
      'style' => 'width:6em;',
    ],
  ]);


});
