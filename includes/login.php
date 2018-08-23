<?php

/**
 * @file
 * Only hooks that are responsible for login/register pages.
 */


/**
 * Theming login pages.
 *
 * Theming modes:
 * empty   - no changes at all
 * brand   - only titles, colors and logo
 * full    - integrate into theme
 */
add_action('login_init', function () {

    $system_page_login_theming = get_theme_mod('system_page_login_theming');

    // Is we have no theming mode then just do nothing.
    if (!$system_page_login_theming) {
      return;
    }

    // Force simple/brand mode for interim logins.
    if (!empty($_REQUEST['interim-login'])) {
      $system_page_login_theming = 'brand';
    }

    // Brand mode (Simple)
    // Only changes should be in the colors, and the logo (replacing the WP logo).
    if ($system_page_login_theming == 'brand') {

      wp_enqueue_style('tinsta-login-brand', tinsta_get_stylesheet('login'));

      add_action('login_head', function () {
          $custom_logo_id = get_theme_mod('custom_logo');
          if ($custom_logo_id) {
          $custom_logo_image = wp_get_attachment_image_src($custom_logo_id, 'full');
          echo '
          <style>
          body #login h1:before {
              content:"";
              background-image: url("' . $custom_logo_image[0] . '") !important; 
          }
          </style>
          ';
          }
      });

      add_filter('login_headerurl', function () {
          return home_url();
      });

      add_filter('login_headertitle', function () {
          return get_bloginfo('blogname');
      });

    }

    // When full styling, then login/register/forgoten password pages should look like regular front-end pages.
    elseif ($system_page_login_theming == 'full') {

      wp_deregister_style('login');

      add_filter('login_body_class', 'get_body_class');
      add_filter('login_headerurl', '__return_null');
      add_filter('login_headertitle', '__return_null');

      add_action('login_head', function () {

          // shake effect fall in troubles when login form is not the first form in the document.
          remove_action('login_head', 'wp_shake_js', 12);

          wp_meta();

          wp_head();
      });

      add_action('login_header', function () {
          locate_template('template-parts/theme/header.php', true);
      });

      add_action('login_footer', function () {
          locate_template('template-parts/theme/footer.php', true);
      });
    }

});