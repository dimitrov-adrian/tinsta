<?php

/**
 * @file
 * Only hooks that are responsible for front-end and NOT for admin/back-end interface.
 */


/**
 * Add extra markup in header.
 */
add_action('wp_head', function () {
  echo get_theme_mod('component_header_markup');
  if (is_singular() && pings_open()) {
    printf('<link rel="pingback" href="%s">' . "\n", get_bloginfo('pingback_url'));
  }
});

/**
 * Add extra markup in footer.
 */
add_action('wp_footer', function () {
  echo get_theme_mod('component_footer_markup');
  if (get_theme_mod('component_site_agreement_enable')) {
    get_template_part('template-parts/misc/agreement');
  }
});

/**
 * Enqueue scripts and styles.
 */
add_action('wp_enqueue_scripts', function () {

  $fonts_google = [];

  if (get_theme_mod('typography_font_google')) {
    $fonts_google[] = urldecode(trim(get_theme_mod('typography_font_google')));
  }

  if (get_theme_mod('typography_font_headings_google')) {
    $fonts_google[] = urldecode(trim(get_theme_mod('typography_font_headings_google')));
  }

  if ($fonts_google) {
    $fonts_google = implode('|', array_map('urlencode', $fonts_google));
    wp_enqueue_style('tinsta-google-fonts', "//fonts.googleapis.com/css?family={$fonts_google}", [], null);
  }

  // Disable the ugly WP styles for recent comments widget.
  add_filter('show_recent_comments_widget_style', '__return_false');

  // Enqueue stylesheets.
  $stylesheet = tinsta_get_stylesheet('default');
  $stylesheet_hash = is_customize_preview() ? microtime(1) : substr(md5(serialize(get_transient('tinsta_theme'))), 2, 4);
  wp_enqueue_style('tinsta-stylesheet', $stylesheet, [], $stylesheet_hash);

  // Legacy supports.
  if (get_theme_mod('misc_legacy_support')) {

    wp_enqueue_script('jquery');

    // https://github.com/jonathantneal/flexibility
    wp_enqueue_script('flexibility', get_template_directory_uri() . '/assets/scripts/flexibility.js', [], '2.0.1');
    wp_script_add_data('flexibility', 'conditional', 'let IE 10');

    // https://github.com/wilddeer/stickyfill
    wp_enqueue_script('stickyfill', get_template_directory_uri() . '/assets/scripts/stickyfill.min.js', [], '2.0.3');
    wp_script_add_data('stickyfill', 'conditional', 'lte IE 11');

    // https://github.com/aFarkas/html5shiv
    wp_enqueue_script('html5shiv', get_template_directory_uri() . '/assets/scripts/html5shiv.min.js', [], '3.7.3');
    wp_script_add_data('html5shiv', 'conditional', 'lte IE 9');

    // https://github.com/corysimmons/selectivizr2
    wp_enqueue_script('selectivizr', get_template_directory_uri() . '/assets/scripts/selectivizr2.min.js', ['jquery'], '1.0.9');
    wp_script_add_data('selectivizr', 'conditional', 'lte IE 9');

    // Seems that this make more mess than benefits, it cause 403 (Forbidden)
    // https://github.com/LeaVerou/prefixfree
    // wp_enqueue_script('prefixfree', get_template_directory_uri() . '/assets/scripts/prefixfree.min.js', [], '1.0.7');
  }

  if (get_theme_mod('misc_nice_scroll')) {
    wp_enqueue_script('jquery-nicescroll', get_template_directory_uri() . '/assets/scripts/jquery.nicescroll.min.js', ['jquery'], '3.7.6', true);
  }

  wp_register_script('masonry', get_template_directory_uri() . '/assets/scripts/masonry.pkgd.min.js', [], '4.2.1', true);

  // Theme's script.
  wp_enqueue_script('tinsta', get_template_directory_uri() . '/assets/scripts/main.js', [], wp_get_theme()->get('Version'), true);
  wp_localize_script('tinsta', 'tinsta', [
    'menuLabel'   => __('Menu', 'tinsta'),
    'closeLabel'  => __('Close', 'tinsta'),
    'top'         => __('Top', 'tinsta'),
    'scrolltop'   => get_theme_mod('component_scrolltop'),
    'breakpoints' => [
      'desktop' => get_theme_mod('section_root_breakpoint_desktop'),
      'tablet'  => get_theme_mod('section_root_breakpoint_tablet'),
      'mobile'  => get_theme_mod('section_root_breakpoint_mobile'),
    ],
  ]);

  // Comment respond form reply script.
  if (is_singular() && comments_open()) {
    wp_enqueue_script('comment-reply');
  }

});

/**
 * Alter body classes.
 */
add_filter('body_class', function ($classes, $class) {

  if (get_theme_mod('section_root_height_full')) {
    $classes[] = 'full-height';
  }

  if (get_theme_mod('section_header_sticky')) {
    $classes[] = 'sticky-header';
  }

  if (get_theme_mod('section_header_disable')) {
    $classes[] = 'disable-header';
  }

  if (get_theme_mod('effects')) {
    $classes[] = 'effects';
  }

  if (get_theme_mod('section_root_layout')) {
    $classes[] = get_theme_mod('section_root_layout');
  }

  // Add class of hfeed to non-singular pages.
  if ( ! is_singular()) {
    $classes[] = 'hfeed';
  }

  return array_diff($classes, [
    'single',
    'archive',
  ]);

}, 10, 2);

/**
 * Alter post classes
 */
add_filter('post_class', function ($classes, $class, $post_id) {

  $post = get_post($post_id);

  if (get_theme_mod("post_type_{$post->post_type}_use_defaults")) {
    $classes['default'] = 'default';
  }

  if ($post) {

    if ( ! is_singular()) {
      $layout = get_theme_mod("post_type_{$post->post_type}_layout_archive");
      if ($layout) {
        $classes['layout'] = 'layout-' . $layout;
      }

      if (get_theme_mod("post_type_{$post->post_type}_masonry")) {
        $classes['masonry'] = 'masonry';
        wp_enqueue_script('masonry');
      }

    }

    if (is_singular()) {
      $layout = get_theme_mod("post_type_{$post->post_type}_layout");
      if ($layout) {
        $classes['layout'] = 'layout-' . $layout;
      }
    }

  }

  $classes = array_unique($classes);

  return $classes;

}, 6, 3);

/**
 * Wrap videos in embed HTML in media container to allow better aspect ratio with responsive.
 */
add_filter('embed_oembed_html', function ($html, $url, $attr) {

  // Wrap embed videos within a media-container div element.
  if (preg_match('#(\<video)#i', $html)) {
    $html = '<div class="media-container">' . $html . '</div>';
  }

  return $html;

}, 10, 3);

/**
 * Alter post contents
 *
 * @TODO this make troubles.
 */
add_filter('the_content', function ($content) {

  if (is_singular()) {
    $post = get_post();

    $component_outdated_post_time = get_theme_mod('component_outdated_post_time', 0);
    if ($component_outdated_post_time && (int)get_the_time('U') + ((int)$component_outdated_post_time * 60 * 60 * 24) < time()) {
      $component_outdated_post_message = get_theme_mod('component_outdated_post_message');
      $component_outdated_post_message = str_replace('%time%', human_time_diff(get_the_time('U')), $component_outdated_post_message);
      $content               .= '
        <div class="message warning">
          ' . $component_outdated_post_message . '
        </div>';
    }

    if (get_theme_mod("post_type_{$post->post_type}_append_authors")) {
      ob_start();
      locate_template('template-parts/misc/post-authors.php', true, false);
      $content .= ob_get_clean();
    }

    if ( ! is_admin_bar_showing() ) {

      if ( function_exists('is_woocommerce') && is_page() && is_woocommerce() ) {
        return $content;
      }

      // When use edit_post_link() no need to add one more translation.
      ob_start();
      edit_post_link(null, '<p>', '</p>');
      $content = ob_get_clean() . $content;
    }
  }

  return $content;

}, 100, 2);

/**
 * Override the excerpt more char at ends.
 */
add_filter('excerpt_more', function ($read_more = '') {
  $mod_read_more = get_theme_mod('misc_excerpt_more', $read_more);
  if ($mod_read_more) {
    $read_more = ' ' . $mod_read_more;
  }

  return $read_more;
});

/**
 * Alter comment fields:
 *  - autocompletion
 *  - avatars
 */
add_filter('comment_form_defaults', function ($defaults) {

  // Enable auto-completion for fields

  if ( ! empty($defaults['fields']['author'])) {
    $defaults['fields']['author'] = str_replace('<input ', '<input autocomplete="name" ', $defaults['fields']['author']);
  }

  if ( ! empty($defaults['fields']['url'])) {
    $defaults['fields']['url'] = str_replace('<input ', '<input autocomplete="on" ', $defaults['fields']['url']);
  }

  if ( ! empty($defaults['fields']['email'])) {
    $defaults['fields']['email'] = str_replace('<input ', '<input autocomplete="email" ', $defaults['fields']['email']);
  }

  // Append avatar to comment post form.
  $defaults['title_reply_after'] .= '<div class="comment-form-avatar">' . get_avatar(get_current_user_id(), get_theme_mod('component_avatar_size')) . '</div>';

  return $defaults;

}, 1000);

/**
 * Security reasons, Format comment text.
 */
add_filter('comment_text', function ($comment_text, $comment, $args) {

  if (!empty($comment->user_id) && user_can($comment->user_id, 'unfiltered_html')) {
    return $comment_text;
  }

  global $allowedtags;

  $allowed_html_tags = '';
  foreach (array_keys($allowedtags) as $tag) {
    $allowed_html_tags .= "<$tag>";
  }

  return strip_tags($comment_text, $allowed_html_tags);

}, 10, 3);

/**
 * Allow widgets to be included
 */
if ( ! shortcode_exists('widget')) {
  add_shortcode('widget', function ($atts) {
    if (class_exists($atts['type'], false) && is_subclass_of($atts['type'], 'WP_Widget')) {
      ob_start();
      the_widget($atts['type'], $atts);

      return ob_get_clean();
    }

    return ' <!-- Inactive Widget --> ';
  });
}

/**
 * Proccess widget's tinsta related settings
 */
add_filter('dynamic_sidebar_params', function ($params) {
  global $wp_registered_widgets;
  if ( ! empty($wp_registered_widgets[$params[0]['widget_id']]['callback'])) {
    $widget = $wp_registered_widgets[$params[0]['widget_id']]['callback'][0];
    /*** @var $widget \WP_Widget */
    $settings = $widget->get_settings();
    if (isset($settings[$params[1]['number']])) {
      $settings = $settings[$params[1]['number']];
      if ( ! empty($settings['tinsta_widget_size_enable']) && $settings['tinsta_widget_size_enable'] == 'on' && ! empty($settings['tinsta_widget_size'])) {
        $replacement = "style=\"width:{$settings['tinsta_widget_size']}%;\"";
        $params[0]['before_widget'] = str_replace('class="', " $replacement class=\"", $params[0]['before_widget']);
      }
    }
  }

  return $params;
});

/**
 * Theming login pages.
 *
 * Theming modes:
 * empty   - no changes at all
 * brand   - only titles, colors and logo
 * full    - integrate into theme
 */
if ( !empty($GLOBALS['pagenow']) && in_array($GLOBALS['pagenow'], [ 'wp-login.php', 'wp-register.php' ])):
add_action('login_init', function () {

  $system_page_login_theming = get_theme_mod('system_page_login_theming');

  // Is we have no theming mode then just do nothing.
  if ( ! $system_page_login_theming ) {
    return;
  }

  // For interim login, fallback to brand integration mode.
  if ( !empty($_REQUEST['interim-login']) ) {
    $system_page_login_theming = 'brand';
  }

  // Brand mode
  if ($system_page_login_theming == 'brand') {

    wp_enqueue_style('tinsta-login-brand', tinsta_get_stylesheet('login'));

    add_action('login_head', function() {
      $custom_logo_id = get_theme_mod( 'custom_logo' );
      if ($custom_logo_id) {
        $custom_logo_image = wp_get_attachment_image_src($custom_logo_id, 'full');
        echo '
        <style>
          body #login h1:before {
            content:"";
            background-image: url("' . $custom_logo_image[0]  . '") !important; 
          }
        </style>
        ';
      }
    });

    add_filter('login_headerurl', function () {
      return home_url();
    });

    add_filter('login_headertitle', function () {
      return get_bloginfo('blogname');
    });

  } // Full mode.
  elseif ($system_page_login_theming == 'full') {

    wp_deregister_style('login');

    add_filter('login_body_class', 'get_body_class');
    add_filter('login_headerurl', '__return_null');
    add_filter('login_headertitle', '__return_null');

    add_action('login_head', function () {

      // shake effect fall in troubles when login form is not the first form in the document.
      remove_action( 'login_head', 'wp_shake_js', 12 );

      wp_meta();

      wp_head();
    });

    add_action('login_header', function () {
      locate_template('template-parts/misc/header.php', true);
    });

    add_action('login_footer', function () {
      locate_template('template-parts/misc/footer.php', true);
    });
  }

});
endif;
