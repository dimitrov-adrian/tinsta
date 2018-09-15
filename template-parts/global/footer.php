</div>

<?php // After post entries sidebar area.
if (have_posts() && is_active_sidebar('after-entries') && tinsta_should_show_beforeafter_entries()) {
  echo '<div class="sidebar-after-entries">';
  dynamic_sidebar('after-entries');
  echo '</div>';
}
?>

<?php if (!tinsta_should_fullscreen()): ?>


  </div>

  <?php if (tinsta_should_show_sidebars() && is_active_sidebar('secondary')): ?>
    <?php get_sidebar('secondary') ?>
  <?php endif ?>

  </div>

  <?php if (is_active_sidebar('after-main')): ?>
    <div role="banner" class="site-after-main">
      <?php dynamic_sidebar('after-main') ?>
    </div>
  <?php endif ?>

  </div>

  <?php if (is_active_sidebar('footer')): ?>
    <footer class="site-footer-wrapper" role="complementary">
      <div class="site-footer">
        <?php dynamic_sidebar('footer') ?>
      </div>
    </footer>
  <?php endif ?>

  <?php if ($bottomline_text = trim(get_theme_mod('options_site_bottomline'))): ?>
    <footer role="complementary" class="site-bottomline-wrapper">
      <div class="site-bottomline">
        <?php echo do_shortcode($bottomline_text) ?>
      </div>
    </footer>
  <?php endif ?>

<?php endif ?>

<?php if (get_theme_mod('region_primary_menu_position') == 'bottom-float'): ?>
  <?php tinsta_primary_menu() ?>
<?php endif ?>

<?php wp_footer() ?>