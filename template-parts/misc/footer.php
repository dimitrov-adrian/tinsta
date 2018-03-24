    </div>

    <?php get_sidebar('secondary') ?>

  </div>
</div>

<?php do_action('tinsta_theme_before_footer') ?>

<?php if (is_active_sidebar('footer')): ?>
  <footer class="site-footer-wrapper"
          role="complementary"
    <?php echo tinsta_background_image_styles([
      'image'  => get_theme_mod('footer_image'),
      'repeat' => get_theme_mod('footer_image_repeat'),
      'size'   => get_theme_mod('footer_image_size'),
      'fixed'  => ! get_theme_mod('footer_image_attachment_scroll'),
      'x'      => get_theme_mod('footer_image_position_x'),
      'y'      => get_theme_mod('footer_image_position_y'),
    ]) ?>
  >
    <div class="site-footer">
      <?php dynamic_sidebar('footer') ?>
    </div>
  </footer>
<?php endif ?>

<?php if (trim(get_theme_mod('site_bottomline')) || is_customize_preview()): ?>
  <footer class="site-bottomline-wrapper" role="complementary">
    <div class="wrapper">
      <?php echo get_theme_mod('site_bottomline') ?>
    </div>
  </footer>
<?php endif ?>

<?php wp_footer() ?>
