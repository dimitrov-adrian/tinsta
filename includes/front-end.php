<?php

/**
 * @file
 * Only hooks that are responsible for front-end only
 */


/**
 * Some internal calls that are in help of the Tinsta theme.
 */
add_action('template_redirect', function () {

  // Handle requests like /?tinsta-resolve-user-avatar=<email|id>[&s=<size>]
  if (!empty($_GET['tinsta-resolve-user-avatar'])) {
    $avatar_url = get_avatar_url($_GET['tinsta-resolve-user-avatar'], [
      'size' => empty($_GET['s']) || !is_numeric($_GET['s']) ? null : $_GET['s'],
    ]);
    wp_redirect($avatar_url);
    exit;
  }

  // Simple ajax search.
  if (isset($_GET['tinsta-ajax-search'])) {
    $args = [
      'posts_per_page' => 10,
    ];
    if ($_GET['tinsta-ajax-search'] != '*' && is_scalar($_GET['tinsta-ajax-search'])) {
      $args['s'] = $_GET['tinsta-ajax-search'];
    }
    $posts = get_posts($args);
    if ($posts) {
      echo '<ul>';
      foreach ($posts as $post) {
        echo '<li><a href="';
        the_permalink($post);
        echo '">' . $post->post_title . '</a></li>';
      }
      echo '</ul>';
    }
    exit;
  }

});

/**
 * Add extra markup in header, SEO/MEO and other hacks to be good for Google.
 */
add_action('wp_head', function () {

  $color = esc_attr(get_theme_mod('region_root_color_primary'));
  $sitename = esc_attr(get_bloginfo('sitename'));
  $sitedescription = esc_attr(substr(get_bloginfo('description'), 0, 160));

  // No need anymore.
  //  if (get_theme_mod('typography_font_google') | get_theme_mod('typography_font_headings_google')) {
  //    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com" />';
  //  }

  echo get_theme_mod('component_header_markup');

  if (get_theme_mod('typography_text_enhancements')) {
    echo '<meta name="ResourceLoaderDynamicStyles" content="on" />';
  }

  // rel=logo
  $logo_id = get_theme_mod( 'custom_logo' );
  if ($logo_id) {
    $logo_url = wp_get_attachment_image_url($logo_id);
    if ($logo_url) {
      printf('<link rel="logo" href="%s" />', esc_attr($logo_url));
    }
  }

  echo "<meta name=\"theme-color\" content=\"{$color}\" />";
  echo "<meta name=\"msapplication-TileColor\" content=\"{$color}\" />";
  echo "<meta name=\"apple-mobile-web-app-title\" content=\"{$sitename}\" />";
  echo "<meta name=\"apple-mobile-web-app-capable\" content=\"yes\" />";
  echo "<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\" />";

  if (get_theme_mod('component_seo_enable')) {

    // Public checkbox in settings.
    if (get_option('blog_public', true)) {
      // Skip SEO friendly metas
      if (tinsta_is_login_page()) {
        echo '<meta name="robots" content="noindex, nofollow" />';
      }
      elseif ( is_tax() || ( is_archive() && ( !empty($_GET['orderby']) || !empty($_GET['order'])) ) ) {
        wp_no_robots();
      }
    }

    $description = '';
    $keywords = [];

    if (is_singular()) {
      the_post();
      printf('<meta name="author" content="%s" />', esc_attr(get_the_author()));
      if (pings_open()) {
        printf('<link rel="pingback" href="%s">', esc_attr(get_bloginfo('pingback_url')));
      }
      $thumbnail = get_the_post_thumbnail_url(get_the_ID());
      if ($thumbnail) {
        echo '<meta content="' . esc_attr($thumbnail) . '" itemprop="thumbnailUrl" />';
      }
      echo '<meta property="article:published_time" content="' . get_the_time('c') . '" />';
      foreach (wp_get_object_terms(get_the_ID(), get_taxonomies(['public' => true])) as $term) {
        $keywords[] = $term->name;
      }
      $description = get_the_excerpt();
    }

    if (is_archive()) {
      $description = category_description();
    }

    // Always set some description.
    if (!trim($description)) {
      $description = $sitedescription;
    }
    else {
      $description = strip_tags($description);
      $description = substr($description, 0, 160);
      $description = esc_attr($description);
    }

    echo "<meta name=\"description\" content=\"{$description}\" />";
    echo "<meta name=\"subject\" content=\"{$sitedescription}\" />";
    echo "<meta name=\"publisher\" content=\"{$sitename}\" />";
    if ($keywords) {
      printf('<meta name="keywords" content="%s" />', esc_attr(implode(', ', $keywords)));
    }

  }

  if (get_theme_mod('component_effects_lazyload')) {
    tinsta_lazyload_start_buffer();
  }

});

/**
 * Fix shortcodes in feeds
 */
add_filter('the_content_rss', function ($content = '') {
  $content = do_shortcode($content);
  return $content;
});

/**
 * Add extra markup in footer.
 */
add_action('wp_footer', function () {

  echo get_theme_mod('component_footer_markup');

  $privacy_policy_page_id = get_option('wp_page_for_privacy_policy');
  if ( ! ($privacy_policy_page_id && is_page($privacy_policy_page_id)) && get_theme_mod('component_site_agreement_enable') ) {
    get_template_part('template-parts/components/agreement');
  }

});

/**
 * Enqueue scripts and styles.
 */
add_action('wp_enqueue_scripts', function () {

  // https://github.com/aFarkas/html5shiv
  wp_enqueue_script('html5shiv', get_template_directory_uri() . '/assets/js/html5shiv.min.js', [], '3.7.3');
  wp_script_add_data('html5shiv', 'conditional', 'lte IE 8');

  // https://github.com/corysimmons/selectivizr2
  wp_enqueue_script('selectivizr', get_template_directory_uri() . '/assets/js/selectivizr2.min.js', [], '1.0.9');
  wp_script_add_data('selectivizr', 'conditional', 'lte IE 8');

  // Seems that this make more mess than benefits, it cause 403 (Forbidden)
  // https://github.com/LeaVerou/prefixfree
  wp_enqueue_script('prefixfree', get_template_directory_uri() . '/assets/js/prefixfree.min.js', [], '1.0.7');
  wp_script_add_data('prefixfree', 'conditional', 'lte IE 8');

  // Rem polyfill
  // https://github.com/nbouvrette/remPolyfill
  wp_enqueue_script('remPolyfill', get_template_directory_uri() . '/assets/js/remPolyfill.js', [], '1.0.0');
  wp_script_add_data('remPolyfill', 'conditional', 'lte IE 8');

  // Respond.js v1.4.2: min/max-width media query polyfill
  wp_enqueue_script('respondjs', get_template_directory_uri() . '/assets/js/respond.min.js', [], '1.4.2');
  wp_script_add_data('respondjs', 'conditional', 'lte IE 8');

  // Tinsta theme hash.
  $theme_hash = substr(md5(is_customize_preview() ? microtime(1) : serialize(get_transient('tinsta_theme'))), 2, 4);

  $fonts_google = [];

  if (get_theme_mod('typography_font_google')) {
    $fonts_google[] = urldecode( trim( get_theme_mod('typography_font_google')));
  }

  if (get_theme_mod('typography_font_headings_google')) {
    $fonts_google[] = urldecode( trim( get_theme_mod('typography_font_headings_google')));
  }

  if ($fonts_google) {
    $fonts_google = implode('|', array_map('urlencode', $fonts_google));
    wp_enqueue_style('tinsta-google-fonts', "//fonts.googleapis.com/css?family={$fonts_google}", [], null);
  }

  // Disable the ugly WP styles for recent comments widget.
  add_filter('show_recent_comments_widget_style', '__return_false');

  // Enqueue stylesheets.
  $stylesheet = tinsta_get_stylesheet('default');
  wp_enqueue_style('tinsta-stylesheet', $stylesheet, [], $theme_hash);

  // Add nice scroll if when enabled.
  if (get_theme_mod('component_effects_smooth_scroll')) {
    wp_enqueue_script('smoothscroll', get_template_directory_uri() . '/assets/js/smoothscroll.min.js', [], '1.4.6', true);
  }

  // Add nice scroll if when enabled.
  if (get_theme_mod('component_effects_lazyload')) {
    wp_enqueue_script('tinsta-lazyload', get_template_directory_uri() . '/assets/js/lazyload.' . ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : 'min.' ) . 'js', [], '1.0', true);
  }

  // Theme's script.
  wp_enqueue_script('tinsta', get_template_directory_uri() . '/assets/js/main.' . ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : 'min.' ) . 'js', [], wp_get_theme()->get('Version'), true);
  wp_localize_script('tinsta', 'tinsta', [
    'siteUrl' => home_url(),
    'assetsDir' => get_template_directory_uri() . '/assets/',
    'fullHeight' => get_theme_mod('region_root_height_full'),
    'scrolltop' => get_theme_mod('component_scrolltop'),
    'strings' => [
      //'editLabel' => __('Edit %s', 'tinsta'),
      //'menuLabel' => __('Menu', 'tinsta'),
      'close' => __('Close', 'tinsta'),
      'top' => __('Top', 'tinsta'),
    ],
    'breakpoints' => [
      'tablet' => get_theme_mod('region_root_breakpoint_tablet'),
      'mobile' => get_theme_mod('region_root_breakpoint_mobile'),
    ],
  ]);

    // Comment respond form reply script.
  if (is_singular()) {
    if (have_comments() || comments_open() || intval(get_comments_number()) > 0) {
      // Enqueue stylesheets.
      wp_enqueue_style('tinsta-comments', tinsta_get_stylesheet('comments'), [], $theme_hash);
    }
    if (comments_open()) {
      wp_enqueue_script('comment-reply');
    }
  }

});

/**
 * Alter body classes.
 */
add_filter('body_class', function ($classes, $class) {

  if (get_theme_mod('component_effects_shadows')) {
    $classes[] = 'effects-shadows';
  }

  if (get_theme_mod('component_effects_animations')) {
    $classes[] = 'effects-animations';
  }

  // Add class of hfeed to non-singular pages.
  if (!is_singular()) {
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

    if (is_singular()) {
      $layout = get_theme_mod("post_type_{$post->post_type}_layout");
      if ($layout) {
        $classes['layout'] = 'layout-' . $layout;
      }
    }
    else {
      $layout = get_theme_mod("post_type_{$post->post_type}_layout_archive");
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
 * @TODO this make troubles with excerpts.
 */
add_filter('the_content', function ($content) {

  if (is_singular()) {
    $post = get_post();

    if (is_user_logged_in() && !is_admin_bar_showing()) {
      ob_start();
      edit_post_link(null, '<p>', '</p> ');
      $content = ob_get_clean() . $content;
    }

    $component_outdated_post_time = get_theme_mod('component_outdated_post_time', 0);
    if ($component_outdated_post_time && (int)get_the_time('U') + ((int)$component_outdated_post_time * 60 * 60 * 24) < time()) {
      $component_outdated_post_message = get_theme_mod('component_outdated_post_message');
      $component_outdated_post_message = str_replace('%time%', human_time_diff(get_the_time('U')), $component_outdated_post_message);
      $content .= "
        <div class=\"message warning\">
          {$component_outdated_post_message}
        </div>";
    }

    if (get_theme_mod("post_type_{$post->post_type}_append_authors")) {
      ob_start();
      locate_template('template-parts/components/post-authors.php', true, false);
      $content .= ob_get_clean();
    }

  }

  return $content;

}, 100, 2);

/**
 * Filter the except length to X words.
 */
add_filter( 'excerpt_length', function ( $length ) {

  $post = get_post();
  $new_length = get_theme_mod("post_type_{$post->post_type}_archive_show_excerpt_words", $length);

  if ($new_length) {
    return $new_length;
  }

  return $length;
});

/**
 * Alter comment fields:
 *  - autocompletion
 *  - avatars
 */
add_filter('comment_form_defaults', function ($defaults) {

  // Enable auto-completion for fields
  if (!empty($defaults['fields']['author'])) {
    $defaults['fields']['author'] = str_replace('<input ', '<input autocomplete="name" ', $defaults['fields']['author']);
  }

  if (!empty($defaults['fields']['url'])) {
    $defaults['fields']['url'] = str_replace('<input ', '<input autocomplete="on" ', $defaults['fields']['url']);
  }

  if (!empty($defaults['fields']['email'])) {
    $defaults['fields']['email'] = str_replace('<input ', '<input autocomplete="email" ', $defaults['fields']['email']);
  }

  // Append avatar to comment post form.
  $defaults['title_reply_after'] .= '
    <div class="comment-form-avatar">
    ' . get_avatar(get_current_user_id(), get_theme_mod('component_avatar_size')) . '
    </div>';

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
    $allowed_html_tags .= "<{$tag}>";
  }

  return strip_tags($comment_text, $allowed_html_tags);

}, 10, 3);

/**
 * Proccess widget's tinsta related settings
 */
add_filter('dynamic_sidebar_params', function ($params) {
  global $wp_registered_widgets;
  if (!empty($wp_registered_widgets[$params[0]['widget_id']]['callback'])) {
    $widget = $wp_registered_widgets[$params[0]['widget_id']]['callback'][0];
    /*** @var $widget \WP_Widget */
    $settings = $widget->get_settings();
    if (isset($settings[$params[1]['number']])) {

      $settings = $settings[$params[1]['number']];

      $style = '';

      if (!empty($settings['tinsta_widget_size'])) {
        $style .= 'width:' . round($settings['tinsta_widget_size'], 2) . '%;';
      }

      if (!empty($settings['tinsta_widget_float'])) {
        $style .= 'float:' . $settings['tinsta_widget_float'];
      }

      $params[0]['before_widget'] = str_replace('class="', " style=\"{$style}\" class=\"", $params[0]['before_widget']);

      if (!empty($settings['tinsta_boxed']) && $settings['tinsta_boxed'] == 'on') {
        $params[0]['before_widget'] = str_replace('class="', 'class="wrapper ', $params[0]['before_widget']);
      }

    }
  }

  return $params;
});

/**
 * Add first level menu items, depth-0 class.
 */
add_filter('nav_menu_css_class', function ($classes, $item, $args, $depth) {
  $classes[] = 'depth-' . $depth;
  return $classes;
}, 10, 4);

/**
 * Add description to menu items
 */
add_filter('walker_nav_menu_start_el', function ($item_output, $item) {

  if (!empty($item->description)) {
    $item_output = str_replace('</a>', '<span class="description">' . $item->description . '</span></a>', $item_output);
  }

  return $item_output;

}, 10, 2);

/**
 * Override post fetching
 */
add_action('pre_get_posts', function ($query) {

  if ( is_admin() ) {
    return;
  }

  if ( $query->is_main_query() ) {

    if ( is_search() ) {
      $post_type = get_theme_mod('system_page_search_search_force_post_type');
      if ($post_type) {
        $query->set('post_type', $post_type);
      }
    }

    if ( !is_search() ) {

      $post_type = $query->get('post_type');
      if (!$post_type && is_home()) {
        $post_type = 'post';
      }
      $per_page = get_theme_mod("post_type_{$post_type}_archive_per_page", 0);
      if ($per_page) {
        $query->set('posts_per_page', $per_page);
      }

    }

  }

});

/**
 * Modifies tag cloud widget arguments to display all tags in the same font size
 * and use list format for better accessibility.
 */
add_filter('widget_tag_cloud_args', function ( $args ) {
  $args['largest']  = 2;
  $args['smallest'] = 0.5;
  $args['unit']     = 'rem';
  return $args;
});

/**
 * Filter user menus
 *
 * Must happen only on front-end
 */
if ( !is_customize_preview() && !is_admin() ) {

  add_filter('wp_get_nav_menu_items', function ($items) {

    $to_remove = [];

    $is_logged_user = wp_get_current_user()->ID;

    foreach ($items as $item_index => $item) {

      if ($item->type == 'tinsta-nav-menu-login-register' && $is_logged_user) {
        $to_remove[$item->ID] = $item->ID;
      }

      if ($item->type == 'tinsta-nav-menu-current-user' && !$is_logged_user) {
        $to_remove[$item->ID] = $item->ID;
      }

      if ($item->type == 'tinsta-nav-menu-widget-area' && !is_active_sidebar('tinsta-menu-' . $item->post_name)) {
        $to_remove[$item->ID] = $item->ID;
      }

    }

    if ($to_remove) {
      foreach ($items as $item_index => $item) {
        foreach ($items as $item_inner_index => $item_inner) {
          if (isset($to_remove[$item_inner->menu_item_parent])) {
            $to_remove[$item_inner->ID] = $item_inner->ID;
          }
        }
      }

      foreach ($items as $item_index => $item) {
        if (isset($to_remove[$item->ID])) {
          unset($items[$item_index]);
        }
      }

    }

    return $items;

  });
}

/**
 * Alter the menus to support theme's customization.
 */
add_filter('walker_nav_menu_start_el', function ($item_output, $item, $depth, $args) {

  if ($item->object === 'tinsta-nav-menu-object') {

    $item_output = null;

    if ($item->type == 'tinsta-nav-menu-frontpage') {
      $item_output = get_custom_logo();

    } elseif ($item->type == 'tinsta-nav-menu-login-register') {
      if (!is_user_logged_in()) {
        $item_output = sprintf('<a href="%s">%s</a>', wp_login_url(),
          (get_option('users_can_register') ? __('Login & Register', 'tinsta') : __('Login', 'tinsta')));
      }

    } elseif ($item->type == 'tinsta-nav-menu-current-user') {
      if (is_user_logged_in()) {
        $title = $item->post_title;
        if (!$title) {
          $title = '%avatar% %name%';
        }
        $title = strtr($title, [
          '%avatar%' => get_avatar(wp_get_current_user()->ID),
          '%name%' => wp_get_current_user()->display_name,
        ]);

        $item_output = sprintf('<a href="%s">%s</a>', empty($item->url) ? get_edit_profile_url() : $item->url, $title);
      }

    } elseif ($item->type == 'tinsta-nav-menu-search-box') {
      if ($depth < 2) {
        $item_output = get_search_form(false);
      }

    } elseif ($item->type == 'tinsta-nav-menu-widget-area') {
      $item_output = null;
      if ($depth < 3) {
        if (is_customize_preview() || is_active_sidebar('tinsta-menu-' . $item->post_name)) {
          ob_start();
          dynamic_sidebar('tinsta-menu-' . $item->post_name);
          $widgets = ob_get_clean();
          if (trim($widgets)) {
            $item_output = '
        <a href="#menu-item-' . $item->ID . '" >' . $item->post_title . '</a>
        <div class="sub-menu">
          <div class="sub-menu-inner">
            ' . $widgets . '
          </div>
        </div>';
          }
        }
      }
    }

  }

  $item_output = preg_replace('#\@icon\(([\w\-]+)\)#i', '<i class="la $1"></i>', $item_output);

  return $item_output;

}, 10, 4);
