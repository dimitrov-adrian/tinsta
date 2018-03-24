<?php get_header() ?>

<?php if (is_active_sidebar('frontpage-full')): ?>
  <?php dynamic_sidebar('frontpage-full') ?>
<?php endif ?>

<?php if (is_active_sidebar('frontpage') || (is_active_sidebar('frontpage-primary') && is_active_sidebar('frontpage-secondary'))): ?>
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
