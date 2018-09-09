<?php

/**
 * Clear all caches upon a Tinsta's CSS regeneration.
 */
add_action('tinsta_css_regenerated', function () {
  if (class_exists('autoptimizeCache')) {
    autoptimizeCache::clearall();
  }
});