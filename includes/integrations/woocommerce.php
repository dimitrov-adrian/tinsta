<?php

//@TODO

add_filter('tinsta_supported_customizer_post_types', function ($post_types) {
  unset($post_types['product']);
  return $post_types;
});

add_filter('woocommerce_enqueue_styles', function ($styles) {
  //  unset( $styles['woocommerce-general'] );	// Remove the gloss
  //  unset( $styles['woocommerce-layout'] );		// Remove the layout
  unset( $styles['woocommerce-smallscreen'] );	// Remove the smallscreen optimisation
  $styles['tinsta-woocommerce'] = [
    'src' => tinsta_get_stylesheet('integrations/woocommerce'),
    'deps'    => '',
    'version' => WC_VERSION,
    'media'   => 'all',
    'has_rtl' => true,
  ];
  return $styles;
});

remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

add_theme_support('woocommerce', [
//  'thumbnail_image_width' => 250,
//  'single_image_width' => 350,
//  'product_grid' => [
//    'default_rows' => 3,
//    'min_rows' => 2,
//    'max_rows' => 8,
//    'default_columns' => 4,
//    'min_columns' => 2,
//    'max_columns' => 5,
//  ],
]);

/**
 * Add description to menu items
 */
//add_filter( 'walker_nav_menu_start_el', function ( $item_output, $item, $depth, $args ) {
//
//  if ($item->type == 'tinsta-woocommerce-cart') {
//    $item_output = '<a href="%s"> {cartcount} ' . __('Cart', 'tinsta') . '</a>';
//  }
//
//  return $item_output;
//}, 10, 4);

/**
 * Sharing placeholders
 */

//add_action('woocommerce_share', array($this, 'woocommerce_share'));

// Fix Jetpack sharing position
//if (function_exists('sharing_display')) {
//  remove_filter('the_content', 'sharing_display', 19);
//  remove_filter('the_excerpt', 'sharing_display', 19);
//  add_action('woocommerce_share', function () {
//    sharing_display('', TRUE);
//  }, 11);
//}

//function woocommerce_share() {
//  if (get_theme_mod('addthis')) {
//    echo '<div class="addthis_native_toolbox"></div>';
//  }
//}


/**
 * Alter sale text
 */
//function woocommerce_sale_flash($markup, $post, $product) {
//  if (!$product->get_regular_price() || !$product->get_sale_price()) {
//    return $markup;
//  }
//  if ($product->product_type == 'variable') {
//    $savepercentage = 100-($product->get_variation_sale_price('min') / $product->get_variation_regular_price('max'))*100;
//  }
//  else {
//    $savepercentage = 100-($product->get_sale_price() / $product->get_regular_price())*100;
//  }
//  $savepercentage = ceil($savepercentage);
//  $markup = '<span class="onsale" title="' . esc_attr__('Sale!', 'tinsta') . '">-' . $savepercentage . '%</span>';
//  return $markup;
//}

/**
 * Override breadcrumbs
 *
 * @param $breadcrumbs
 * @param $object
 *
 * @return mixed
 */
//function woocommerce_get_breadcrumb($breadcrumbs, $object) {
//  array_pop($breadcrumbs);
//  return $breadcrumbs;
//}
