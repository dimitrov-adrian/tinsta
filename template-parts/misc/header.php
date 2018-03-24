<a class="skip-link screen-reader-text" href="#main" tabindex="0">
  <?php _e('Skip to content', 'tinsta') ?>
</a>

<?php if (trim(get_theme_mod('site_topline')) || is_customize_preview()): ?>
  <header role="complementary" class="site-topline-wrapper">
    <div class="wrapper">
      <?php echo get_theme_mod('site_topline') ?>
    </div>
  </header>
<?php endif ?>

<header role="banner"
    class="site-header-wrapper"
    <?php echo tinsta_background_image_styles([
      'image'  => get_theme_mod('header_image'),
      'repeat' => get_theme_mod('header_image_repeat'),
      'size'   => get_theme_mod('header_image_size'),
      'fixed'  => ! get_theme_mod('header_image_attachment_scroll'),
      'x'      => get_theme_mod('header_image_position_x'),
      'y'      => get_theme_mod('header_image_position_y'),
    ]) ?>
  >

  <?php if (is_active_sidebar('header')): ?>
    <div class="site-header">
      <?php dynamic_sidebar('header') ?>
    </div>
  <?php endif?>

  <?php
  //  Main menu has no depth limit
  //  Main menu items accept next classes: button, button-secondary
  wp_nav_menu('container_class=site-primary-menu&theme_location=main&fallback_cb=') ?>

</header>

<?php do_action('tinsta_theme_after_header') ?>

<div class="site-container-wrapper">
  <div class="site-container">

    <?php get_sidebar() ?>

    <div class="site-main" role="main" id="main"
      <?php echo tinsta_background_image_styles([
        'image'  => get_theme_mod('main_image'),
        'repeat' => get_theme_mod('main_image_repeat'),
        'size'   => get_theme_mod('main_image_size'),
        'fixed'  => ! get_theme_mod('main_image_attachment_scroll'),
        'x'      => get_theme_mod('main_image_position_x'),
        'y'      => get_theme_mod('main_image_position_y'),
      ]) ?>
    >

