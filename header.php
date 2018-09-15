<!DOCTYPE html>
<html <?php language_attributes() ?> class="no-js">
<head>
  <meta charset="<?php bloginfo('charset') ?>" />
  <link rel="profile" href="http://gmpg.org/xfn/11" />
  <meta name="MobileOptimized" content="width" />
  <meta name="HandheldFriendly" content="true" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <?php wp_head() ?>
</head>
<body <?php body_class() ?>>

<a class="skip-link screen-reader-text" href="#main" tabindex="0">
  <?php _e('Skip to content', 'tinsta') ?>
</a>

<?php locate_template('template-parts/global/header.php', true) ?>