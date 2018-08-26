<?php

/**
 * Filter user menus
 */
//add_filter('wp_get_nav_menu_items', function ($items, $menu, $args) {
//
//  $to_remove = [];
//  foreach ($items as $item_index => $item) {
//
//    // Remove menu itemans submenu items under logged user, when no user is logged.
//    if ($item->type == 'tinsta-current-logged-user' && !wp_get_current_user()->ID) {
//      $to_remove[$item->ID] = $item->ID;
//    }
//
//    // Remove menu itemans submenu items under anonymous user, when user is logged.
//    if ($item->type == 'tinsta-anonymous-user' && wp_get_current_user()->ID) {
//      $to_remove[$item->ID] = $item->ID;
//    }
//  }
//
//  if ($to_remove) {
//    foreach ($items as $item_index => $item) {
//      foreach ($items as $item_index_i => $item_i) {
//        if (isset($to_remove[$item_i->menu_item_parent])) {
//          $to_remove[$item_i->ID] = $item_i->ID;
//        }
//      }
//    }
//
//    foreach ($items as $item_index => $item) {
//      if (isset($to_remove[$item->ID])) {
//        unset($items[$item_index]);
//      }
//    }
//
//  }
//
//  return $items;
//
//}, 10, 3);


/**
 * Alter the menus to support theme's customization.
 */
add_filter('walker_nav_menu_start_el', function ($item_output, $item, $depth, $args) {

  if ($item->type == 'tinsta-nav-menu-frontpage') {
    return get_custom_logo();
  }

  if ($item->type == 'tinsta-nav-menu-current-user') {
    if (is_user_logged_in()) {
      return '<a href="' . get_edit_profile_url() . '">' .
              get_avatar(wp_get_current_user()->ID) .
              wp_get_current_user()->display_name .
              '</a>';
    }
    else {
      return '<a href="' . wp_login_url() . '">' .
        ( get_option( 'users_can_register' )
          ? __('Login or Register', 'tinsta')
          : __('Login', 'tinsta')
        ) .
        '</a>';
    }
    $item_output = ob_get_clean();
  }

  if ($item->type == 'tinsta-nav-menu-search-box') {
    if ($depth < 2) {
      return get_search_form(false);
    }
    return null;
  }

  if ($item->type == 'tinsta-nav-menu-widget-area') {
    if ($depth < 3) {
      if (is_active_sidebar('tinsta-menu-' . $item->post_name)) {
        ob_start();
        dynamic_sidebar('tinsta-menu-' . $item->post_name);
        $sidebar_submenu = ob_get_clean();
        if (trim($sidebar_submenu)) {
          $item_output .= "<div class=\"sub-menu\">{$sidebar_submenu}</div>";
          return $item_output;
        }
      }
    }
    return null;
  }

  return $item_output;

}, 10, 4);


/**
 * Make tinsta-nav-menu-widget-area available as sidebar.
 */
add_action('widgets_init', function () {

  // Register menu sidebars
  $menu_sidebars = get_posts([
    'post_type' => 'nav_menu_item',
    'meta_query' => [
      [
        'key' => '_menu_item_type',
        'value' => 'tinsta-nav-menu-widget-area',
      ]
    ]
  ]);

  foreach ($menu_sidebars as $sidebar) {
    register_sidebar([
      'name' => sprintf(__('Menu "%s"', 'tinsta'), $sidebar->post_title),
      'description' => sprintf(__('Appears only in %s as sub-menu', 'tinsta'), $sidebar->post_title),
      'id' => 'tinsta-menu-' . $sidebar->post_name,
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widgettitle">',
      'after_title' => '</div>',
    ]);
  }

});


/**
 * Helper function that returns Tinsta's custom menu items.
 *
 * @return array
 */
function tinsta_nav_menu_items()
{
  $items = [];

  $items[] = [
    'id' => null,
    'type' => 'tinsta-nav-menu-frontpage',
    'title' => __('Frontpage (with logo)', 'tinsta'),
    'type_label' => __('Frontpage (with logo)', 'tinsta'),
    'object' => null,
    'url' => null,
  ];

  $items[] = [
    'id' => null,
    'type' => 'tinsta-nav-menu-widget-area',
    'title' => __('Widget Area', 'tinsta'),
    'type_label' => __('Widget Area', 'tinsta'),
    'object' => null,
    'url' => null,
  ];

  // Doesn't looks good when added as first level.
  // @TODO fix design
  //  $items[] = [
  //    'id' => null,
  //    'type' => 'tinsta-nav-menu-search-box',
  //    'title' => __('Search Box', 'tinsta'),
  //    'type_label' => __('Search Box', 'tinsta'),
  //    'object' => null,
  //    'url' => null,
  //  ];

  $items[] = [
    'id' => NULL,
    'type' => 'tinsta-nav-menu-current-user',
    'title' => __('Current User', 'tinsta'),
    'type_label' => __('Current User', 'tinsta'),
    'object' => 'tinsta-nav-menu-current-user:anonymous',
    'url' => NULL,
  ];

  return $items;
}

/**
 * Add metabox for Tinsta's items in the wp-admin/nav-menus.php
 */
add_action( 'admin_head-nav-menus.php', function() {
  add_meta_box('tinsta_nav_menu_items', __('Dynamic Content', 'tinsta'), function () {
    //    // may be next is not required anymore
    //    global $_nav_menu_placeholder, $nav_menu_selected_id;
    //    $_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;
    global $nav_menu_selected_id;
  ?>
    <div id="tinsta-menu-items-div">
      <div class="tabs-panel tabs-panel-active">
        <ul class="categorychecklist form-no-clear" >
          <?php $index = 0; foreach (tinsta_nav_menu_items() as $item): $index++; ?>
          <li>
            <label class="menu-item-title">
              <input type="checkbox" class="menu-item-checkbox"
                     name="menu-item[<?php echo esc_attr($index)?>][menu-item-object-id]"
                     value="<?php echo esc_attr($index); ?>" />
              <?php echo esc_html($item['title'])?>
            </label>

            <input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr($index)?>][menu-item-type]"
                   value="<?php echo esc_html($item['type'])?>" />

            <input type="hidden" class="menu-item-object" name="menu-item[<?php echo esc_attr($index)?>][menu-item-object]"
                   value="<?php echo esc_html($item['object'])?>" />

            <input type="hidden" class="menu-item-title"
                   name="menu-item[<?php echo esc_attr($index)?>][menu-item-title]"
                   value="<?php echo esc_html($item['title'])?>" />

            <?php if (!empty($item['url'])):?>
            <input type="hidden" class="menu-item-url" name="menu-item[<?php echo esc_attr($index); ?>][menu-item-url]"
                   value="<?php echo esc_url($item['url']); ?>" />
            <?php endif?>

            <input type="hidden" class="menu-item-classes"
                   name="menu-item[<?php echo esc_attr($index)?>][menu-item-classes]" />
          </li>
          <?php endforeach?>
      </ul>
    </div>
    <p class="button-controls">
      <span class="add-to-menu">
        <input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id )?>
               class="button-secondary submit-add-to-menu right"
               value="<?php esc_attr_e( 'Add to Menu', 'tinsta' )?>"
               name="add-tinsta-menu-item" id="submit-tinsta-menu-items-div" />
        <span class="spinner"></span>
      </span>
    </p>
    <p>
      <?php _e('Works only when item is placed as first or second level.', 'tinsta')?>
    </p>
  </div>
    <?php
  }, 'nav-menus', 'side', 'low');
});


/**
 * Add metabox for Tinsta's items in the customizer.
 */
add_filter('customize_nav_menu_available_item_types', function($menu_types) {
  $menu_types[] = [
    'title' => __('Dynamic Content', 'tinsta'),
    'type_label' => __('Dynamic Content', 'tinsta'),
    'type' => 'tinsta-menu-item',
    'object' => 'tinsta-menu-item-object',
  ];
  return $menu_types;
});


/**
 * Add Tinsta's custom menu item to customizer's metabox.
 */
add_filter( 'customize_nav_menu_available_items', function( $items = array(), $type = '', $object = '', $page = 0 ) {
  if ( 'tinsta-menu-item-object' !== $object ) {
    return $items;
  }
  return array_merge( $items, tinsta_nav_menu_items() );
}, 10, 4);
