<!DOCTYPE html>
<html <?php language_attributes() ?> class="no-js">
<head>
  <meta charset="<?php bloginfo('charset') ?>" />
  <link rel="profile" href="http://gmpg.org/xfn/11" />
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
  <meta name="MobileOptimized" content="width" />
  <meta name="HandheldFriendly" content="true" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="ResourceLoaderDynamicStyles" content=""/>
  <?php wp_meta() ?>
  <?php wp_head() ?>
</head>
<body <?php body_class() ?>>

<?php

if ( ! ( is_singular() && get_page_template_slug() == 'template-fullscreen.php' ) ) {
  locate_template('template-parts/theme/header.php', true);
}
