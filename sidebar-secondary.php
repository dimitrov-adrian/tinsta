<?php

// Get the sidebar slug id.
$sidebar = tinsta_get_post_type_sidebar('secondary');

if ( ! $sidebar || is_404() ) {
  return;
}

// Disable for some of the templates.
if (is_singular()) {
  $page_template = get_page_template_slug();
  if (in_array($page_template, ['template-nosidebars.php', 'template-content-only.php', 'template-thin.php'])) {
    return;
  }
}

?>

<aside class="sidebar sidebar-secondary" role="complementary">
  <?php dynamic_sidebar($sidebar) ?>
</aside>
