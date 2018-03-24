<!DOCTYPE html>
<html <?php language_attributes() ?> class="no-js">
<head>
  <meta charset="<?php bloginfo('charset') ?>" />

  <link rel="profile" href="http://gmpg.org/xfn/11" />

  <meta name="viewport" content="width=device-width, height=device-height, minimum-scale=1.0, initial-scale=1.0, user-scalable=0" />
  <meta name="MobileOptimized" content="height" />
  <meta name="HandheldFriendly" content="true" />

  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <meta name="theme-color" value="<?php echo get_theme_mod('color_primary')?>" />

  <?php wp_meta() ?>

  <?php wp_head() ?>
</head>
<body <?php body_class() ?>
  <?php echo tinsta_background_image_styles([
    'image'  => get_theme_mod('site_body_image'),
    'repeat' => get_theme_mod('site_body_image_repeat'),
    'size'   => get_theme_mod('site_body_image_size'),
    'fixed'  => ! get_theme_mod('site_body_image_attachment_scroll'),
    'x'      => get_theme_mod('site_body_image_position_x'),
    'y'      => get_theme_mod('site_body_image_position_y'),
  ]) ?>>

  <?php

    if (!(is_singular() && get_page_template_slug() == 'template-fullpage.php')) {
      locate_template('template-parts/misc/header.php', true);
    }


