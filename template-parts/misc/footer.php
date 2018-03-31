    </div>

    <?php get_sidebar() ?>

    <?php get_sidebar('secondary') ?>

  </div>
</div>

<?php do_action('tinsta_theme_before_footer') ?>

<?php if (is_active_sidebar('footer')): ?>
  <footer class="site-footer-wrapper" role="complementary">
    <div class="site-footer">
      <?php dynamic_sidebar('footer') ?>
    </div>
  </footer>
<?php endif ?>

<?php if (trim(get_theme_mod('component_site_bottomline')) || is_customize_preview()): ?>
  <footer class="site-bottomline-wrapper" role="complementary">
    <div class="wrapper">
      <?php echo get_theme_mod('component_site_bottomline') ?>
    </div>
  </footer>
<?php endif ?>

<?php wp_footer() ?>
