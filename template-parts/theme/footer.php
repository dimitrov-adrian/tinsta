    </div>

    <?php get_sidebar() ?>

    <?php get_sidebar('secondary') ?>

  </div>

  <?php if (is_active_sidebar('after-content')): ?>
  <div role="banner" class="site-container-after">
    <?php dynamic_sidebar('after-content') ?>
  </div>
  <?php endif?>

</div>

<?php if (is_active_sidebar('footer')): ?>
  <footer class="site-footer-wrapper" role="complementary">
    <div class="site-footer">
      <?php dynamic_sidebar('footer') ?>
    </div>
  </footer>
<?php endif ?>

<?php if ( $bottomline_text = trim(get_theme_mod('component_site_bottomline')) ): ?>
  <footer role="complementary" class="site-bottomline-wrapper">
    <div class="site-bottomline">
      <?php echo do_shortcode($bottomline_text)?>
    </div>
  </footer>
<?php endif ?>

<?php wp_footer() ?>
