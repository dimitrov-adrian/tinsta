<?php

/**
 * @file
 *
 * This is WordPress's default functions.php file,
 * if simple theme
 */


// TINSTA_STYLESHEET_CACHE_DIR should be relative to WP_CONTENT_DIR and must starts with slash
if ( ! defined( 'TINSTA_STYLESHEET_CACHE_DIR' ) ) {
  define( 'TINSTA_STYLESHEET_CACHE_DIR', '/cache' );
}

// Tinsta's core functions.
require __DIR__ . '/includes/functions.php';

// Base theme setup.
require_once __DIR__ . '/includes/theme.php';

// Admin panel related setup.
if ( is_admin() ) {
  require __DIR__ . '/includes/admin.php';
}

// Front End related setup.
else {
  require __DIR__ . '/includes/front-end.php';
}

// Login/Register related setup.
if ( tinsta_is_login_page() ) {
  require __DIR__ . '/includes/login.php';
}

// Customizer related setup.
if ( is_customize_preview() ) {
  require __DIR__ . '/includes/customizer.php';
}

// Setup integrations with other themes and plugins.
foreach ( (array) get_option('active_plugins', []) as $active_plugin ) {
  $integration_include = __DIR__ . '/includes/integrations/' . dirname($active_plugin) . '.php';
  if ( $active_plugin && file_exists ( $integration_include ) ) {
    include $integration_include;
  }
}
include __DIR__ . '/includes/integrations/menu-widgets.php';


// @TODO remove before production
add_filter('tinsta_force_options', function ($opts) {

  return [
//    'system_page_login_theming' => 'brand',
//    'typography_roundness'             => 50,
//
//    'typography_form_button_style' => 'fill',
//    'typography_font_family' => '"Segoe UI",SegoeUI,"Helvetica Neue",Helvetica,Arial,sans-serif',
//
//    'region_root_height_full'      => false,
//    'region_root_color_background' => '#eeeeee',
//    'region_root_color_primary'    => '#5ca910',
//    'region_root_color_secondary'  => '#e55a19',
//
//    'region_topline_layout'      => 'boxed',
//    'region_topline_background' => '#eeeeee',
//    'region_topline_foreground' => '#999999',
//
//    'region_header_layout'       => '',
//    'region_header_color_background' => '#23292e',
//    'region_header_color_foreground' => '#ffffff',
//    'region_header_color_primary'    => '#4285f4',
//    'region_header_color_secondary'  => '#e55a19',
//
//    'region_main_layout'         => 'boxed',
//    'region_main_margin' => 0,
//
//    'region_primary_menu_layout' => 'boxed',
//    'region_primary_menu_color_background' => '#2e363d',
//    'region_primary_menu_color_foreground' => '#ffffff',
//
//    'region_sidebar_primary_primary'   => '#4285f4',
//    'region_sidebar_primary_secondary' => '#e55a19',
//
//    'region_sidebar_secondary_primary'   => '#4285f4',
//    'region_sidebar_secondary_secondary' => '#e55a19',
//
//    'region_footer_color_background' => '#2e363d',
//    'region_footer_color_foreground' => '#ffffff',
//    'region_footer_layout'       => 'highlighted', // boxed
//
//    'region_bottomline_layout'   => '',
//    'region_bottomline_color_background' => '#2e363d',
//    'region_bottomline_color_foreground' => '#ffffff',
//
//    'component_site_topline' => get_bloginfo('name') . ' | ' . get_bloginfo('description'),
  ];
});
