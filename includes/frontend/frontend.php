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
  if (isset($_GET['tinsta-ajax-search']) && !get_theme_mod('system_page_search_disable_search')) {
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
 * Parse query hook
 */
add_action('parse_query', function ($query, $error = true) {
  if (get_theme_mod('system_page_search_disable_search') && is_main_query() && is_search()) {
    $query->is_search = false;
    $query->query_vars['s'] = false;
    $query->query['s'] = false;
    // to error
    if ($error == true) {
      $query->is_404 = true;
    }
  }
});

/**
 * Override post fetching
 */
add_action('pre_get_posts', function ($query) {

  if (is_admin()) {
    return;
  }

  if ($query->is_main_query()) {

    if (is_search()) {
      $post_type = get_theme_mod('system_page_search_force_post_type');
      if ($post_type) {
        $query->set('post_type', $post_type);
      }
    }

    if (!is_search()) {

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
 * Add extra markup in header.
 */
add_action('wp_head', function () {

  $is_public = get_option('blog_public', true);
  $color = esc_attr(get_theme_mod('region_root_color_primary'));
  $sitename = esc_attr(get_bloginfo('sitename'));

  echo get_theme_mod('options_header_markup');

  if (get_theme_mod('typography_text_enhancements')) {
    echo '<meta name="ResourceLoaderDynamicStyles" content="on" />';
  }

  // rel=logo
  $logo_id = get_theme_mod('custom_logo');
  if ($logo_id) {
    $logo_url = wp_get_attachment_image_url($logo_id);
    if ($logo_url) {
      printf('<link rel="logo" href="%s" />', esc_attr($logo_url));
    }
  }

  if ($is_public && tinsta_is_login_page()) {
    echo '<meta name="robots" content="noindex, nofollow" />';
  }

  echo "<meta name=\"theme-color\" content=\"{$color}\" />";
  echo "<meta name=\"msapplication-TileColor\" content=\"{$color}\" />";
  echo "<meta name=\"apple-mobile-web-app-title\" content=\"{$sitename}\" />";
  echo "<meta name=\"apple-mobile-web-app-capable\" content=\"yes\" />";
  echo "<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\" />";

  if (get_theme_mod('options_seo_enable')) {

    // Public checkbox in settings.
    if ($is_public) {
      if (is_tax() || ((!empty($_GET['orderby']) || !empty($_GET['order']))) && is_archive()) {
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
      rewind_posts();
    } elseif (is_category()) {
      $description = category_description();
    }

    // Always set some description.
    $sitedescription = esc_attr(substr(strip_tags(get_bloginfo('description')), 0, 160));
    if ($sitedescription) {
      echo "<meta name=\"subject\" content=\"{$sitedescription}\" />";
    }

    if (!$description) {
      $description = $sitedescription;
    }

    if ($description) {
      $description = esc_attr(substr(strip_tags(get_bloginfo('description')), 0, 160));
      echo "<meta name=\"description\" content=\"{$description}\" />";
    }

    echo "<meta name=\"publisher\" content=\"{$sitename}\" />";
    if ($keywords) {
      printf('<meta name="keywords" content="%s" />', esc_attr(implode(', ', $keywords)));
    }

  }

  if (get_theme_mod('options_effects_lazyload')) {
    tinsta_lazyload_start_buffer();
  }

});

/**
 * Add extra markup in footer.
 */
add_action('wp_footer', function () {

  // Output footer markup.
  echo get_theme_mod('options_footer_markup');

  // Add agreement dialog markup.
  if (!is_user_logged_in() && get_theme_mod('options_site_agreement_enable')) {
    $privacy_policy_page_id = get_option('wp_page_for_privacy_policy');
    if (!($privacy_policy_page_id && is_page($privacy_policy_page_id))) {
      locate_template('template-parts/components/agreement.php', true, true);
    }
  }

});

/**
 * Enqueue scripts and styles.
 */
add_action('wp_enqueue_scripts', function () {

  $template_directory_uri = get_template_directory_uri();

  $min_suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : 'min.');

  // https://github.com/aFarkas/html5shiv
  wp_enqueue_script('html5shiv', $template_directory_uri . '/assets/js/html5shiv.min.js', [], '3.7.3');
  wp_script_add_data('html5shiv', 'conditional', 'lte IE 8');

  // https://github.com/corysimmons/selectivizr2
  wp_enqueue_script('selectivizr', $template_directory_uri . '/assets/js/selectivizr2.min.js', [], '1.0.9');
  wp_script_add_data('selectivizr', 'conditional', 'lte IE 8');

  // Seems that this make more mess than benefits, it cause 403 (Forbidden)
  // https://github.com/LeaVerou/prefixfree
  wp_enqueue_script('prefixfree', $template_directory_uri . '/assets/js/prefixfree.min.js', [], '1.0.7');
  wp_script_add_data('prefixfree', 'conditional', 'lte IE 8');

  // Rem polyfill
  // https://github.com/nbouvrette/remPolyfill
  wp_enqueue_script('remPolyfill', $template_directory_uri . '/assets/js/remPolyfill.js', [], '1.0.0');
  wp_script_add_data('remPolyfill', 'conditional', 'lte IE 8');

  // Respond.js v1.4.2: min/max-width media query polyfill
  wp_enqueue_script('respondjs', $template_directory_uri . '/assets/js/respond.min.js', [], '1.4.2');
  wp_script_add_data('respondjs', 'conditional', 'lte IE 8');

  // Tinsta theme hash.
  $theme_hash = substr(md5(is_customize_preview() ? microtime(1) : json_encode(get_transient('tinsta_theme'))), 2, 4);

  $fonts_google = [];

  if (get_theme_mod('typography_font_google')) {
    $fonts_google[] = urldecode(trim(get_theme_mod('typography_font_google')));
  }

  if (get_theme_mod('typography_font_headings_google')) {
    $fonts_google[] = urldecode(trim(get_theme_mod('typography_font_headings_google')));
  }

  if ($fonts_google) {
    $fonts_google = implode('|', array_map('urlencode', $fonts_google));
    wp_enqueue_style('tinsta-google-fonts', '//fonts.googleapis.com/css?family=' . $fonts_google);
  }

  // Enqueue stylesheets.
  $stylesheet = tinsta_get_stylesheet('default');
  wp_enqueue_style('tinsta-stylesheet', $stylesheet, [], $theme_hash);

  // Add nice scroll if when enabled.
  if (get_theme_mod('basics_effects_smooth_scroll')) {
    wp_enqueue_script('smoothscroll', $template_directory_uri . '/assets/js/smoothscroll.min.js', [], '1.4.6', true);
  }

  // Add nice scroll if when enabled.
  if (get_theme_mod('options_effects_lazyload')) {
    wp_enqueue_script('tinsta-lazyload', $template_directory_uri . '/assets/js/lazyload.' . $min_suffix . 'js', [],
      $theme_hash, true);
  }

  // Theme's script.
  wp_enqueue_script('tinsta', $template_directory_uri . '/assets/js/main.' . $min_suffix . 'js', [], $theme_hash, true);
  wp_localize_script('tinsta', 'tinsta', [
    'siteUrl' => home_url(),
    'assetsDir' => $template_directory_uri . '/assets/',
    'fullHeight' => get_theme_mod('region_root_height_full'),
    'scrolltop' => get_theme_mod('options_scrolltop'),
    'strings' => [
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
    $comments_open = comments_open();

    if (have_comments() || $comments_open || intval(get_comments_number()) > 0) {
      // Enqueue stylesheets.
      wp_enqueue_style('tinsta-comments', tinsta_get_stylesheet('comments'), [], $theme_hash);
    }

    if ($comments_open) {
      wp_enqueue_script('comment-reply');
    }

  }

  // Disable the ugly WP styles for recent comments widget.
  add_filter('show_recent_comments_widget_style', '__return_false');
  rewind_posts();

});

/**
 * Alter body classes.
 */
add_filter('body_class', function ($classes, $class) {

  static $cached_vars = null;
  if ($cached_vars === null) {
    $cached_vars = [
      'basics_effects_shadows' => get_theme_mod('basics_effects_shadows'),
      'basics_effects_animations' => get_theme_mod('basics_effects_animations'),
    ];
  }

  if ($cached_vars['basics_effects_shadows']) {
    $classes[] = 'effects-shadows';
  }

  if ($cached_vars['basics_effects_animations']) {
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
    } else {
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
 * Excerpt rewrite
 */
// @TODO This seems to be implemented out of the box, check to ensure.
//add_filter('the_excerpt', function ( $excerpt ) {
//  $excerpt = strip_shortcodes( $excerpt );
//  return $excerpt;
//});

/**
 * Alter post contents
 *
 * @TODO this make troubles with excerpts.
 */
add_filter('the_content', function ($content) {

  static $cached_vars = null;
  if ($cached_vars === null) {
    $cached_vars = [
      'is_singular' => is_singular(),
      'is_user_logged_in' => is_user_logged_in(),
      'is_admin_bar_showing' => is_admin_bar_showing(),
      'options_outdated_post_time' => (int)get_theme_mod('options_outdated_post_time', 0) * 60 * 60 * 24,
      'options_outdated_post_message' => get_theme_mod('options_outdated_post_message'),
    ];
  }

  if ($cached_vars['is_singular']) {
    $post = get_post();

    if ($cached_vars['is_user_logged_in'] && !$cached_vars['is_admin_bar_showing']) {
      ob_start();
      edit_post_link(null, '<p>', '</p> ');
      $content = ob_get_clean() . $content;
    }

    if ($cached_vars['options_outdated_post_time'] && (int)get_the_time('U') + $cached_vars['options_outdated_post_time'] < time()) {
      $content .= '<div class="message warning">';
      $content .= str_replace('%time%', human_time_diff(get_the_time('U')),
        $cached_vars['options_outdated_post_message']);
      $content .= '</div>';
    }

    if (!isset($cached_vars["post_type_{$post->post_type}_append_authors"])) {
      $cached_vars["post_type_{$post->post_type}_append_authors"] = get_theme_mod("post_type_{$post->post_type}_append_authors");
    }

    if ($cached_vars["post_type_{$post->post_type}_append_authors"]) {
      ob_start();
      locate_template('template-parts/components/post-authors.php', true, false);
      $content .= ob_get_clean();
    }

  }

  return $content;

}, 100, 2);

/**
 * Fix shortcodes in feeds
 */
add_filter('the_content_rss', function ($content = '') {
  $content = do_shortcode($content);

  return $content;
});

/**
 * Filter the except length to X words.
 */
add_filter('excerpt_length', function ($length) {

  $post = get_post();
  $new_length = get_theme_mod("post_type_{$post->post_type}_archive_show_excerpt_words", $length);

  if ($new_length) {
    return $new_length;
  }

  return $length;
});

/**
 * Wrap videos in embed HTML in media container to allow better aspect ratio with responsive.
 */
add_filter('embed_oembed_html', function ($html) {

  // Wrap embed videos within a media-container div element.
  if (preg_match('#(\<video)#i', $html)) {
    $html = '<div class="media-container">' . $html . '</div>';
  }

  return $html;

});

/**
 * Safely add oEmbed media to a comment
 */
add_filter('get_comment_text', function ( $comment_text ) {
  global $wp_embed;
  // Automatic discovery would be a security risk, safety first
  add_filter( 'embed_oembed_discover', '__return_false', 999 );
  $comment_text = $wp_embed->autoembed( $comment_text );
  // ...but don't break your posts if you use it
  remove_filter( 'embed_oembed_discover', '__return_false', 999 );
  return $comment_text;
});

/**
 * Alter comment fields:
 *  - autocompletion
 *  - avatars
 */
add_filter('comment_form_defaults', function ($defaults) {

  // Enable auto-completion for fields
  if (!empty($defaults['fields']['author'])) {
    $defaults['fields']['author'] = str_replace('<input ', '<input autocomplete="name" ',
      $defaults['fields']['author']);
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
    ' . get_avatar(get_current_user_id(), get_theme_mod('options_avatar_size')) . '
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
 * Add first level menu items, depth-0 class.
 */
add_filter('nav_menu_css_class', function ($classes, $item, $args, $depth) {
  $classes[] = 'depth-' . $depth;

  return $classes;
}, 10, 4);

/**
 * Filter user menus
 *
 * Must happen only on front-end
 */
if (!is_customize_preview() && !is_admin()) {

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

      if ($item->type == 'tinsta-nav-menu-widget-area' && !is_active_sidebar('tinsta-menu-' . $item->ID)) {
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

    static $wp_current_user = null;
    if ($wp_current_user === null) {
      $wp_current_user = wp_get_current_user();
    }

    $item_output = null;

    if ($item->type == 'tinsta-nav-menu-frontpage') {
      $item_output = get_custom_logo();

    } elseif ($item->type == 'tinsta-nav-menu-login-register') {
      if (!$wp_current_user->ID) {
        $item_output = sprintf('<a href="%s">%s</a>', wp_login_url(),
          (get_option('users_can_register') ? __('Login & Register', 'tinsta') : __('Login', 'tinsta')));
      }

    } elseif ($item->type == 'tinsta-nav-menu-current-user') {
      if ($wp_current_user->ID) {
        $title = $item->post_title;
        if (!$title) {
          $title = '%avatar% %name%';
        }
        $title = strtr($title, [
          '%avatar%' => get_avatar($wp_current_user->ID),
          '%name%' => $wp_current_user->display_name,
        ]);

        $item_output = sprintf('<a href="%s">%s</a>', empty($item->url) ? get_edit_profile_url() : $item->url, $title);
      }

    } elseif ($item->type == 'tinsta-nav-menu-search-box') {
      if ($depth === 0) {
        $item_output = get_search_form(false);
      }

    } elseif ($item->type == 'tinsta-nav-menu-widget-area') {
      if ($depth === 0) {
        $sidebar_id = 'tinsta-menu-' . $item->ID;
        if (is_customize_preview() || is_active_sidebar($sidebar_id)) {
          ob_start();
          dynamic_sidebar($sidebar_id);
          $widgets = ob_get_clean();
          if (trim($widgets)) {
            $item_output = '
              <a href="#menu-item-' . $item->ID . '" >' . $item->post_title . '</a>
              <div class="sub-menu">
                <div class="sub-menu-inner">
                  ' . $widgets . '
                </div>
              </div>
              ';
          }
        }
      }
    }

  }

  if (strpos($item_output, '@icon(') !== false) {
    $item_output = preg_replace('#\@icon\(([\w\-]+)\)#i', '<i class="la $1"></i>', $item_output);
  }

  if (!empty($item->description)) {
    $item_output = str_replace('</a>', '<span class="description">' . $item->description . '</span></a>', $item_output);
  }

  return $item_output;

}, 10, 4);

/**
 * Modifies tag cloud widget arguments to display all tags in the same font size
 * and use list format for better accessibility.
 */
add_filter('widget_tag_cloud_args', function ($args) {
  $args['largest'] = 2;
  $args['smallest'] = 0.5;
  $args['unit'] = 'rem';

  return $args;
});