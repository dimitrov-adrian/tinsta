<?php

add_action('init', function () {

//  register_post_type('tinsta-layouts', [
//    'label' => __('Layouts', 'tinsta'),
//    'labels' => [
//      'name' => __('Layouts', 'tinsta'),
//      'singular_name' => __('Layout', 'tinsta'),
//      'menu_name' => __('Site Layouts', 'tinsta'),
//      'parent_item_colon' => __('Parent layout', 'tinsta'),
//      'all_items' => __('Layouts', 'tinsta'),
//      'view_item' => __('View layout', 'tinsta'),
//      'add_new_item' => __('Create layout', 'tinsta'),
//      'add_new' => __('Design new layout', 'tinsta'),
//      'edit_item' => __('Edit layout', 'tinsta'),
//      'update_item' => __('Update', 'tinsta'),
//      'search_items' => __('Search', 'tinsta'),
//      'not_found' => __('No found', 'tinsta'),
//      'not_found_in_trash' => __('Not found in Trash', 'tinsta'),
//    ],
//    'supports' => [ 'title' ],
//    'hierarchical' => true,
//    'public' => false,
//    'show_ui' => true,
//    'show_in_menu' => current_user_can('customize') ? 'themes.php' : false,
//    'menu_position' => 1000,
//    'show_in_nav_menus' => true,
//    'show_in_admin_bar' => false,
//    'menu_icon' => 'dashicons-schedule',
//    'can_export' => false,
//    'has_archive' => false,
//    'exclude_from_search' => true,
//    'publicly_queryable' => false,
//    'rewrite' => false,
//    'capabilities'     => array(
//      'create_posts'           => 'edit_theme_options',
//      'delete_others_posts'    => 'edit_theme_options',
//      'delete_post'            => 'edit_theme_options',
//      'delete_posts'           => 'edit_theme_options',
//      'delete_private_posts'   => 'edit_theme_options',
//      'delete_published_posts' => 'edit_theme_options',
//      'edit_others_posts'      => 'edit_theme_options',
//      'edit_post'              => 'edit_theme_options',
//      'edit_posts'             => 'edit_theme_options',
//      'edit_private_posts'     => 'edit_theme_options',
//      'edit_published_posts'   => 'edit_theme_options',
//      'publish_posts'          => 'edit_theme_options',
//      'read'                   => 'edit_theme_options',
//      'read_post'              => 'edit_theme_options',
//      'read_private_posts'     => 'edit_theme_options',
//    ),
//  ]);

  add_filter('siteorigin_panels_settings', function ($settings) {
    $settings['home-page'] = true;
//    $settings['home-page'] = true;
//    $settings['home-page-default'] = false;
//    $settings['home-page-default'] = true;
//    $settings['home-template'] = 'index.php';
//
    $settings['title-html'] = '<div class="widgettitle">{{title}}</div>';
    $settings['post-types'][] = 'tinsta-layouts';


    $settings['responsive'] = true;
    $settings['mobile-width'] = (int) get_theme_mod('region_root_breakpoint_mobile');
    $settings['tablet-width'] = (int) get_theme_mod('region_root_breakpoint_tablet');

    $settings['margin-top'] = (int) get_theme_mod('typography_font_size');
    $settings['margin-sides'] = $settings['margin-top'];


    return $settings;
  });

  //  function _tinsta_so_get_layouts($layouts = [])
  //  {
  //    foreach (get_posts('post_type=tinsta-layouts') as $l) {
  //      $layouts['tinsta-layouts-so:' . $l->ID] = '(Layout) ' . $l->post_title;
  //    }
  //
  //    return $layouts;
  //  }
  //
  //  add_filter('tinsta_post_type_layouts_single', '_tinsta_so_get_layouts');
  //  add_filter('tinsta_post_type_layouts_archive', '_tinsta_so_get_layouts');
});