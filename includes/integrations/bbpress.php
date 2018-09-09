<?php

add_action('bbp_enqueue_scripts', function() {
  //wp_enqueue_style(tinsta_get_stylesheet('integrations/bbpress'));
});

//
if (0) {
  if (function_exists('is_bbpress') && is_bbpress()) {
    $bbp_breadcrumbs = bbp_get_breadcrumb([
      'before'          => '<div class="breadcrumbs">',
      'after'           => '</div>',
      'sep'             => '',
      'pad_sep'         => 1,
      'sep_before'      => '',
      'sep_after'       => '',
      'crumb_before'    => '',
      'crumb_after'     => '',
      'include_home'    => get_theme_mod('component_breadcrumbs_include_home'),
      'home_text'       => get_bloginfo('name'),
      'include_root'    => true,
      'include_current' => true,
      'current_before'  => '',//<span class="bbp-breadcrumb-current">',
      'current_after'   => '',//</span>',
    ]);
    echo str_replace('class="', 'class="trail ', $bbp_breadcrumbs);
    return;
  }
}
