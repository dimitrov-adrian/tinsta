<?php get_header() ?>

<?php if (is_active_sidebar('frontpage-full')): ?>
  <?php dynamic_sidebar('frontpage-full') ?>
<?php endif ?>

<?php

  // Do this because customize need to found these sidebars to show options menu.
  $widgetize_frontpage = false;
  $widgetize_frontpage = is_active_sidebar('frontpage') || $widgetize_frontpage;
  $widgetize_frontpage = is_active_sidebar('frontpage-primary') || $widgetize_frontpage;
  $widgetize_frontpage = is_active_sidebar('frontpage-secondary') || $widgetize_frontpage;

  if (0&&$widgetize_frontpage):
?>

  <?php dynamic_sidebar('frontpage') ?>

  <div class="frontpage-under">

    <div class="frontpage-under-primary">
      <?php dynamic_sidebar('frontpage-primary') ?>
    </div>

    <div class="frontpage-under-secondary">
      <?php dynamic_sidebar('frontpage-secondary') ?>
    </div>

  </div>

<?php else: ?>
  <?php tinsta_render_posts() ?>
<?php endif ?>

<?php get_footer() ?>
