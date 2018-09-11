<?php get_header() ?>

<?php tinsta_render_posts_loop() ?>

<?php if (is_singular()): ?>
  <?php comments_template('/comments.php', true) ?>
<?php endif ?>

<?php get_footer() ?>
