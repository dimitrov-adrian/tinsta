<?php

/**
 * @file
 *
 * This is WordPress's default functions.php file,
 * if simple theme
 */


/**
 * Check if Tinsta theme can run on current environment.
 */
global $wp_version;
$tinsta_supports_failcheck = [
  'php' => version_compare( phpversion(), '5.4.0', '<' ),
  'wp' => version_compare( $wp_version, '4.4.0', '<' ),
  'phar' => !extension_loaded('phar'),
  // @TODO add check if wp-content is writeable
];

if ( count( array_filter( $tinsta_supports_failcheck) ) ) {

  if ( is_admin() ) {

    add_action('admin_notices', '_tinsta_check_notices_admin');
    function _tinsta_check_notices_admin() {
      global $tinsta_supports_failcheck;
      echo '<div class="error">';

      if ($tinsta_supports_failcheck['php']) {
        echo '<p>';
        printf(__('Tinsta theme requires PHP >= 5.4.0, but you have %s. Upgrade it or contact your hosting provider. Otherwise the theme will not work.',
          'tinsta'), phpversion());
        echo '</p>';
      }
      if ($tinsta_supports_failcheck['wp']) {
        global $wp_version;
        echo '<p>';
        printf(__('Tinsta theme requires WordPress >= 4.4.0, but you have %s. Upgrade it or contact your hosting provider. Otherwise the theme will not work.',
          'tinsta'), $wp_version);
        echo '</p>';
      }
      if ($tinsta_supports_failcheck['phar']) {
        echo '<p>';
        _e('Tinsta theme requires Phar support for PHP, but you don\'t have it enabled. Enable Phar for PHP or contact your hosting provider. Otherwise the theme will not work.',
          'tinsta');
        echo '</p>';
      }

      echo '</div>';
    }

  }

  else {

    add_action('template_redirect', '_tinsta_check_notices_frontend');
    function _tinsta_check_notices_frontend() {
      wp_die(__('The site is under maintenance. We are working to get it back as soon as possible.', 'tinsta'));
    }

  }

  return false;
}

/**
 * Path to static css files.
 */
if ( ! defined('TINSTA_STYLESHEET_CACHE_DIR') ) {
  define( 'TINSTA_STYLESHEET_CACHE_DIR', '/cache/tinsta/css' );
}

/**
 * Third-Party integrations.
 */
if ( ! defined('TINSTA_INTEGRATIONS') ) {
  define('TINSTA_INTEGRATIONS', true);
}

/**
 * Experimental functions.
 */
if ( ! defined('TINSTA_EXPERIMENTAL') ) {
  define('TINSTA_EXPERIMENTAL', false);
}

/**
 * Tinsta's core functions.
 */
require __DIR__ . '/includes/functions.php';

/**
 * Base theme setup.
 */
require __DIR__ . '/includes/theme.php';

/**
 * Admin panel related setup.
 */
if ( is_admin() ) {
  require __DIR__ . '/includes/admin.php';
}

/**
 * Front End related setup.
 */
else {
  require __DIR__ . '/includes/front-end.php';
}

/**
 * Login/Register related setup.
 */
if ( tinsta_is_login_page() ) {
  require __DIR__ . '/includes/login.php';
}

/**
 * Customizer related setup.
 */
if ( is_customize_preview() ) {
  require __DIR__ . '/includes/customizer.php';
}

/**
 * Setup integrations with other themes and plugins.
 */
if ( TINSTA_INTEGRATIONS ) {
  foreach ( (array) get_option('active_plugins', []) as $active_plugin ) {
    $integration_include = __DIR__ . '/includes/integrations/' . dirname($active_plugin) . '.php';
    if ($active_plugin && file_exists($integration_include)) {
      include $integration_include;
    }
  }
}

/**
 * Include experiments.
 */
if ( TINSTA_EXPERIMENTAL ) {
  include __DIR__ . '/includes/experimental/nav-menu-items.php';
}
