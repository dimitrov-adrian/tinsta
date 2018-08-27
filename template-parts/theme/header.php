<a class="skip-link screen-reader-text" href="#main" tabindex="0">
  <?php _e('Skip to content', 'tinsta') ?>
</a>

<?php if ( $topline_text = trim(get_theme_mod('component_site_topline')) ):
  if (defined('TINSTA_PROCESS_PHP') && TINSTA_PROCESS_PHP) {
    ob_start();
    eval('?>' . $topline_text);
    $topline_text = ob_get_clean();
  }
  ?>
  <header role="complementary" class="site-topline-wrapper">
    <div class="site-topline">
      <?php echo $topline_text ?>
    </div>
  </header>
<?php endif ?>

<header role="banner" class="site-header-wrapper">
  <?php if (is_active_sidebar('header')): ?>
    <div class="site-header">
      <?php dynamic_sidebar('header') ?>
    </div>
  <?php endif?>
  <?php wp_nav_menu([
      'menu_class' => 'menu',
      'container_class' => 'site-primary-menu-wrapper',
      'theme_location' => 'main',
      'fallback_cb' => NULL
  ])?>
</header>

<div class="site-container-wrapper">

  <?php if (is_active_sidebar('before-content')): ?>
  <div role="banner" class="site-container-before">
    <?php dynamic_sidebar('before-content') ?>
  </div>
  <?php endif?>

  <div class="site-container">

    <div class="site-main" role="main" id="main">
