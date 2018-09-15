<?php if (!tinsta_should_fullscreen()): ?>

  <?php if ($topline_text = trim(get_theme_mod('options_site_topline'))): ?>
    <header role="complementary" class="site-topline-wrapper">
      <div class="site-topline">
        <?php echo do_shortcode($topline_text) ?>
      </div>
    </header>
  <?php endif ?>

  <?php $region_primary_menu_position = get_theme_mod('region_primary_menu_position') ?>

  <?php if ($region_primary_menu_position == 'before-header'): ?>
    <?php tinsta_primary_menu() ?>
  <?php endif ?>

  <header role="banner" class="site-header-wrapper">
    <?php if (is_active_sidebar('header')): ?>
      <div class="site-header">
        <?php dynamic_sidebar('header') ?>
      </div>
    <?php endif ?>
    <?php if ($region_primary_menu_position == 'prepend-header' || $region_primary_menu_position == 'append-header'): ?>
      <?php tinsta_primary_menu() ?>
    <?php endif ?>
  </header>

  <?php if ($region_primary_menu_position == 'after-header'): ?>
    <?php tinsta_primary_menu() ?>
  <?php endif ?>

  <div class="site-container-wrapper">

  <?php if (is_active_sidebar('before-main')): ?>
    <div role="banner" class="site-before-main">
      <?php dynamic_sidebar('before-main') ?>
    </div>
  <?php endif ?>

  <div class="site-container">

  <?php if (tinsta_should_show_sidebars() && is_active_sidebar('primary')): ?>
    <?php get_sidebar() ?>
  <?php endif ?>

  <div class="site-main" role="main" id="main">

<?php endif ?>

<?php // Before post entries sidebar area.
if (have_posts() && is_active_sidebar('before-entries') && tinsta_should_show_beforeafter_entries()) {
  echo '<div class="sidebar-before-entries">';
  dynamic_sidebar('before-entries');
  echo '</div>';
}
?>

<?php

$post_type = get_post_type();
$display_mode = is_singular() ? 'single' : 'archive';

echo "<div id=\"site-entries\" class=\"site-entries site-entries-type-{$post_type} site-entries-{$display_mode}\">";
