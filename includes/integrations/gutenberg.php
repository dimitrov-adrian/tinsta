<?php
/**
 * @file
 * Gutenberg integrations.
 */

add_theme_support( 'editor-styles' );

// Adding support for core block visual styles.
add_theme_support('wp-block-styles');

// Add support for full and wide align images.
add_theme_support('align-wide');

// Add support for custom color scheme.
add_theme_support('editor-color-palette', [

  [
    'name' => __('Background Color', 'tinsta'),
    'slug' => 'region_root_color_background',
    'color' => get_theme_mod('region_root_color_background'),
  ],

  [
    'name' => __('Foreground Color', 'tinsta'),
    'slug' => 'region_root_color_foreground',
    'color' => get_theme_mod('region_root_color_foreground'),
  ],

  [
    'name' => __('Primary Color', 'tinsta'),
    'slug' => 'region_root_color_primary',
    'color' => get_theme_mod('region_root_color_primary'),
  ],

  [
    'name' => __('Secondary Color', 'tinsta'),
    'slug' => 'region_root_color_secondary',
    'color' => get_theme_mod('region_root_color_secondary'),
  ],

]);
