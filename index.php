<?php get_header() ?>

<?php if (is_home() && get_option('show_on_front') == 'widgets'):?>

  <?php dynamic_sidebar('frontpage')?>

<?php else:?>

  <?php tinsta_render_posts_loop()?>

  <?php if (is_singular()):?>
    <?php comments_template('/comments.php', true)?>
  <?php endif?>

<?php endif?>

<?php get_footer() ?>
