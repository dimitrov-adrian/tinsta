<?php

//@TODO
//
//add_filter( 'woocommerce_enqueue_styles', function($styles) {
//
//  //  unset( $enqueue_styles['woocommerce-general'] );	// Remove the gloss
//  //  unset( $enqueue_styles['woocommerce-layout'] );		// Remove the layout
//  //  unset( $enqueue_styles['woocommerce-smallscreen'] );	// Remove the smallscreen optimisation
//
//  $styles = [];
//  $styles[] = tinsta_get_stylesheet('woocommerce');
//  return $styles;
//});

// cart widget

//      ssa_woocommerce_cart_count();
//      echo '<div class="widget_shopping_cart_content"></div>';



/**
 * Add description to menu items
 */
add_filter( 'walker_nav_menu_start_el', function ( $item_output, $item, $depth, $args ) {

  if ($item->type == 'tinsta-woocommerce-cart') {
    $item_output = '<a href="%s"> {cartcount} ' . __('Cart', 'tinsta') . '</a>';
  }

  return $item_output;
}, 10, 4);


add_theme_support( 'woocommerce', array(
  'thumbnail_image_width' => 250,
  'single_image_width'    => 350,
  'product_grid'          => array(
    'default_rows'    => 3,
    'min_rows'        => 2,
    'max_rows'        => 8,
    'default_columns' => 4,
    'min_columns'     => 2,
    'max_columns'     => 5,
  ),
  ));


/**
 * Sharing placeholders
 */

add_action('woocommerce_share', array($this, 'woocommerce_share'));

// Fix Jetpack sharing position
if (function_exists('sharing_display')) {
  remove_filter('the_content', 'sharing_display', 19);
  remove_filter('the_excerpt', 'sharing_display', 19);
  add_action('woocommerce_share', function () {
    sharing_display('', TRUE);
  }, 11);
}
function woocommerce_share() {

  if (get_theme_mod('addthis')) {
    ?>
    <div class="addthis_native_toolbox"></div>
    <?php
  }

}


/**
 * Override products per page num
 */
function loop_shop_per_page($num) {
  return 40;
}


/**
 * Override products per row
 */
function loop_shop_columns($num) {
  return 4;
}
/**
 * Alter sale text
 */
function woocommerce_sale_flash($markup, $post, $product) {
  if (!$product->get_regular_price() || !$product->get_sale_price()) {
    return $markup;
  }
  if ($product->product_type == 'variable') {
    $savepercentage = 100-($product->get_variation_sale_price('min') / $product->get_variation_regular_price('max'))*100;
  }
  else {
    $savepercentage = 100-($product->get_sale_price() / $product->get_regular_price())*100;
  }
  $savepercentage = ceil($savepercentage);
  $markup = '<span class="onsale" title="' . esc_attr__('Sale!', 'tinsta') . '">-' . $savepercentage . '%</span>';
  return $markup;
}

/**
 * Override breadcrumbs
 *
 * @param $breadcrumbs
 * @param $object
 *
 * @return mixed
 */
function woocommerce_get_breadcrumb($breadcrumbs, $object) {
  array_pop($breadcrumbs);
  return $breadcrumbs;
}
