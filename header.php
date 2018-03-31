<!DOCTYPE html>
<html <?php language_attributes() ?> class="no-js">
<head>
  <meta charset="<?php bloginfo('charset') ?>" />

  <link rel="profile" href="http://gmpg.org/xfn/11" />

  <meta name="viewport" content="width=device-width, user-scalable=no" />
  <meta name="MobileOptimized" content="height" />
  <meta name="HandheldFriendly" content="true" />

  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <meta name="theme-color" content="<?php echo get_theme_mod('section_root_color_primary') ?>" />

  <?php wp_meta() ?>

  <?php wp_head() ?>
</head>
<body <?php body_class() ?>>

<?php

if ( ! (is_singular() && get_page_template_slug() == 'template-fullscreen.php')) {
  locate_template('template-parts/misc/header.php', true);
}


