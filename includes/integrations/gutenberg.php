<?php

/**
 * Gutenberg compatibility.
 *
 * @package    Compatibility
 * @subpackage Gutenberg
 * @category   Core
 * @author     GoDaddy
 * @since      1.8.5
 */

/**
 * Enable Gutenberg features.
 *
 * @since 1.8.5
 */
add_action( 'after_setup_theme', function () {

  add_theme_support( 'wp-block-styles' );
  add_theme_support( 'align-wide' );

});

/**
 * Enqueue styles for Gutenberg editor.
 *
 * @since 1.8.5
 */
add_action( 'enqueue_block_editor_assets', function () {

  global $post;

  $suffix = SCRIPT_DEBUG ? '' : '.min';

  wp_enqueue_style( 'primer-gutenberg-editor', get_template_directory_uri() . "/assets/css/admin/gutenberg-editor{$suffix}.css", true, defined( 'PRIMER_CHILD_VERSION' ) ? PRIMER_CHILD_VERSION : PRIMER_VERSION, 'all' );

});
