<?php

/**
 * @file
 * Theme bootstrap.
 */


/**
 * Check if Tinsta theme can run on current environment.
 */
$tinsta_supports_failcheck = [
  'php' => version_compare(phpversion(), '5.4.0', '<'),
  'wp' => version_compare(get_bloginfo('version'), '4.6.0', '<'),
  'phar' => !extension_loaded('phar'),
  // @TODO add check if wp-content is writeable
];

if (count(array_filter($tinsta_supports_failcheck))) {

  if (is_admin()) {

    add_action('admin_notices', '_tinsta_check_notices_admin');
    function _tinsta_check_notices_admin()
    {
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

      echo '<p>';
      _e('Meanwhile, maintenance message is displayed on yor front-end.', 'tinsta');
      echo '</p>';

      echo '</div>';
    }

  } else {

    add_action('template_redirect', '_tinsta_check_notices_frontend');
    function _tinsta_check_notices_frontend()
    {
      wp_die(__('The site is under maintenance. We are working to get it back as soon as possible.', 'tinsta'), '',
        ['response' => '503']);
    }

  }

  return false;
}

/**
 * Path to static css files.
 */
if (!defined('TINSTA_STYLESHEET_CACHE_DIR')) {
  define('TINSTA_STYLESHEET_CACHE_DIR', '/cache/tinsta/css');
}

/**
 * Third-Party integrations.
 */
if (!defined('TINSTA_INTEGRATIONS')) {
  define('TINSTA_INTEGRATIONS', true);
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
if (is_admin()) {
  require __DIR__ . '/includes/admin/admin.php';
} /**
 * Front End related setup.
 */ else {
  require __DIR__ . '/includes/frontend/frontend.php';
}

/**
 * Login/Register related setup.
 */
if (tinsta_is_login_page()) {
  require __DIR__ . '/includes/frontend/login.php';
}

/**
 * Customizer related setup.
 */
if (is_customize_preview()) {
  require __DIR__ . '/includes/admin/customizer.php';
}

/**
 * Setup integrations with other themes and plugins.
 */
if (TINSTA_INTEGRATIONS) {

  foreach ((array)get_option('active_plugins', []) as $active_plugin) {
    $plugin_slug = dirname($active_plugin);
    $integration_include = __DIR__ . '/includes/integrations/' . dirname($active_plugin) . '.php';
    if ($active_plugin && file_exists($integration_include)) {
      include $integration_include;
    }
  }

  if (!defined('TINSTA_POST_WIDGETS_REPLACE_CONTENT')) {
    define('TINSTA_POST_WIDGETS_REPLACE_CONTENT', true);
    require __DIR__ . '/includes/integrations/-no-layouting-plugin.php';
  }

}
