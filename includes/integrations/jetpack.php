<?php

// Enqueue scripts and styles.
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('tinsta-bbpress', tinsta_get_stylesheet('integrations/jetpack'));
}, 20);

// Infinite scroll.
add_theme_support('infinite-scroll', [
  'type' => 'scroll',
  'footer_widgets' => false,
  'container' => 'site-entries',
  'wrapper' => false,
  'posts_per_page' => false,
  'render' => 'tinsta_render_posts_loop',
]);

// Add theme support for Responsive Videos.
add_theme_support('jetpack-responsive-videos');