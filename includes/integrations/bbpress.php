<?php

// Customizer.
if (is_customize_preview()) {
  add_filter('tinsta_supported_customizer_post_types', function ($post_types) {
    unset($post_types['forum'], $post_types['topic'], $post_types['reply']);

    return $post_types;
  });
}

// Admin.
if (is_admin()) {

} // Front-End.
else {

  // Override user profile edit url.
  add_filter('edit_profile_url', function ($url, $user_id, $scheme) {
    return bbp_get_user_profile_url($user_id);
  }, 10, 3);

  // Some stuff...
  add_action('wp', function () {

    if (is_bbpress()) {

      // Enqueue scripts and styles.
      add_action('bbp_enqueue_scripts', function () {
        wp_enqueue_style('tinsta-bbpress', tinsta_get_stylesheet('integrations/bbpress'));
      }, 20);

      // Disable breadcrumbs.
      add_filter('bbp_no_breadcrumb', '__return_true', 100);

      // Override template.
      add_filter('template_include', function ($template) {
        return locate_template('template-content-only.php', false);
      });

    }
  });
}
