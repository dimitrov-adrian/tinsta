<?php

// Disable for some of the templates.
if ( is_singular() ) {
  $page_template = get_page_template_slug();
  if ( in_array($page_template, [
    'template-nosidebars.php',
    'template-content-only.php',
    'template-thin.php'
  ] ) ) {
    return;
  }
}

if ( is_404() ) {
  return;
}

if ( !is_active_sidebar('primary') ) {
  return;
}

?>

<aside class="sidebar sidebar-primary" role="complementary">
  <?php dynamic_sidebar('primary') ?>
</aside>
