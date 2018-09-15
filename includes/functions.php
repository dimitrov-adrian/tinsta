<?php

/**
 * @file
 * Theme helpers.
 */


/**
 * Default theme mod array
 *
 * @return array
 */
function tinsta_get_options_defaults()
{

  // @TODO cache settings. Right now it's not possible because, there is calls
  //       that register post types after the first tinsta_get_options_defaults() call.
  //    static $settings = null;
  //    if ($settings !== null) {
  //      return $settings;
  //    }

  $privacy_policy_link_post = get_option('wp_page_for_privacy_policy');
  if ($privacy_policy_link_post) {
    $privacy_policy_link = home_url('?p=' . get_option('wp_page_for_privacy_policy'));
  } else {
    $privacy_policy_link = '';
  }

  // Add all theme mods.
  $settings = [

    // Basics
    'basics_effects_animations' => 250,
    'basics_effects_shadows' => 0,
    'basics_effects_smooth_scroll' => false,
    'basics_roundness' => 0,
    'basics_brightness' => 50,
    'basics_bordering' => 1,

    'options_effects_lazyload' => false,
    'options_seo_enable' => true,
    'options_seo_manifest' => true,

    'basics_breakpoint_tablet' => 980,
    'basics_breakpoint_mobile' => 768,
    'basics_mobile_user_scale' => 'yes',

    // Typography
    'typography_font_size' => 13,
    'typography_font_line_height' => 170,
    'typography_font_family' => 'medium-content-sans-serif-font, -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif',
    'typography_font_family_headings' => '',
    'typography_font_google' => '',
    'typography_font_headings_google' => '',
    'typography_font_headings_style' => '',
    'typography_text_justify' => false,
    'typography_text_wordbreak' => false,
    'typography_text_enhancements' => false,
    'typography_form_spacing' => 30,
    'typography_form_button_style' => 'fill',
    'typography_font_button_text_style' => 'small-caps',
    'typography_link_style' => '',

    // Regions
    'region_root_height_full' => true,
    'region_root_width_full' => false,
    'region_root_width' => 980,
    'region_root_color_background' => '#ffffff',
    'region_root_color_foreground' => '#222222',
    'region_root_color_primary' => '#4285f4',
    'region_root_color_secondary' => '#e55a19',
    'region_root_image' => '',
    'region_root_image_size' => 'auto',
    'region_root_image_repeat' => true,
    'region_root_image_attachment_scroll' => true,
    'region_root_image_position_x' => 'center',
    'region_root_image_position_y' => 'center',
    'region_topline_layout' => '',
    'region_topline_sticky' => false,
    'region_topline_alignment' => '',
    'region_topline_color_background' => '#eeeeee',
    'region_topline_color_background_opacity' => 100,
    'region_topline_color_foreground' => '#555555',
    'region_header_layout' => '',
    'region_header_padding_vertical' => 5,
    'region_header_sticky' => false,
    'region_header_alignment' => '',
    'region_header_image' => '',
    'region_header_image_size' => 'auto',
    'region_header_image_repeat' => true,
    'region_header_image_attachment_scroll' => true,
    'region_header_image_position_x' => 'center',
    'region_header_image_position_y' => 'center',
    'region_header_color_background' => '#4285f4',
    'region_header_color_background_opacity' => 100,
    'region_header_color_foreground' => '#ffffff',
    'region_header_color_primary' => '#eeeeee',
    'region_header_color_secondary' => '#ffffff',

    'region_before_main_padding_vertical' => 0,
    'region_before_main_image' => '',
    'region_before_main_image_size' => 'auto',
    'region_before_main_image_repeat' => true,
    'region_before_main_image_attachment_scroll' => true,
    'region_before_main_image_position_x' => 'center',
    'region_before_main_image_position_y' => 'center',
    'region_before_main_color_background' => '#eeeeee',
    'region_before_main_color_background_opacity' => 100,
    'region_before_main_color_foreground' => '#777777',
    'region_before_main_color_primary' => '#4285f4',
    'region_before_main_color_secondary' => '#e55a19',

    'region_after_main_padding_vertical' => 0,
    'region_after_main_image' => '',
    'region_after_main_image_size' => 'auto',
    'region_after_main_image_repeat' => true,
    'region_after_main_image_attachment_scroll' => true,
    'region_after_main_image_position_x' => 'center',
    'region_after_main_image_position_y' => 'center',
    'region_after_main_color_background' => '#eeeeee',
    'region_after_main_color_background_opacity' => 100,
    'region_after_main_color_foreground' => '#777777',
    'region_after_main_color_primary' => '#4285f4',
    'region_after_main_color_secondary' => '#e55a19',

    'region_primary_menu_layout' => '',
    'region_primary_menu_position' => 'append-header',
    'region_primary_menu_sticky' => false,
    'region_primary_menu_alignment' => '',
    'region_primary_menu_highlight_root' => 'background',
    'region_primary_menu_color_background' => '#000000',
    'region_primary_menu_color_background_opacity' => 20,
    'region_primary_menu_color_foreground' => '#ffffff',
    'region_main_layout' => '',
    'region_main_margin' => 0,
    'region_main_color_background' => '#ffffff',
    'region_main_color_background_opacity' => 100,
    'region_main_image' => '',
    'region_main_image_size' => 'auto',
    'region_main_image_repeat' => true,
    'region_main_image_attachment_scroll' => true,
    'region_main_image_position_x' => 'center',
    'region_main_image_position_y' => 'center',
    'region_sidebar_primary_sticky' => false,
    'region_sidebar_primary_width' => 220,
    'region_sidebar_primary_color_background' => '#ffffff',
    'region_sidebar_primary_color_background_opacity' => 100,
    'region_sidebar_primary_color_foreground' => '#222222',
    'region_sidebar_primary_color_primary' => '#4285f4',
    'region_sidebar_primary_color_secondary' => '#e55a19',
    'region_sidebar_secondary_sticky' => false,
    'region_sidebar_secondary_width' => 160,
    'region_sidebar_secondary_color_background' => '#ffffff',
    'region_sidebar_secondary_color_background_opacity' => 100,
    'region_sidebar_secondary_color_foreground' => '#222222',
    'region_sidebar_secondary_color_primary' => '#4285f4',
    'region_sidebar_secondary_color_secondary' => '#e55a19',
    'region_footer_layout' => '',
    'region_footer_alignment' => '',
    'region_footer_image' => '',
    'region_footer_image_size' => 'auto',
    'region_footer_image_repeat' => true,
    'region_footer_image_attachment_scroll' => true,
    'region_footer_image_position_x' => 'center',
    'region_footer_image_position_y' => 'center',
    'region_footer_color_background' => '#263238',
    'region_footer_color_background_opacity' => 100,
    'region_footer_color_foreground' => '#cfd8dc',
    'region_footer_color_primary' => '#ffffff',
    'region_footer_color_secondary' => '#e55a19',
    'region_bottomline_layout' => '',
    'region_bottomline_sticky' => false,
    'region_bottomline_alignment' => 'center',
    'region_bottomline_color_background' => '#455a64',
    'region_bottomline_color_background_opacity' => 100,
    'region_bottomline_color_foreground' => '#eeeeee',

    // Options
    'options_site_topline' => '',
    'options_site_bottomline' => get_bloginfo('name') . ' - ' . date('Y'),
    'options_header_markup' => '',
    'options_footer_markup' => '',
    'options_social_networks_code' => '',
    'options_breadcrumbs_title' => '',
    'options_breadcrumbs_include_home' => true,
    'options_breadcrumbs_separator' => '/',
    'options_outdated_post_time' => 0,
    'options_outdated_post_message' => __('This article is older than %time%, the content might not be relevant anymore.',
      'tinsta'),
    'options_pagination_style' => 'bordered',
    'options_pagination_prevnext_style' => 'angle',
    'options_scrolltop' => 'bottom-right',
    'options_scrolltop_style' => "\\f113",
    'options_scrolltop_offset_horizontal' => 32,
    'options_scrolltop_offset_vertical' => 18,

    'options_reading_progress' => 'bottom',
    'options_reading_progress_offset_horizontal' => 0,
    'options_reading_progress_offset_vertical' => 0,

    'options_site_agreement_enable' => true,
    'options_site_agreement_style' => 'center',
    'options_site_agreement_text' => sprintf(__('I agree with site <a href="%s">terms</a>.', 'tinsta'),
      $privacy_policy_link),
    'options_site_agreement_agree_button' => __('Agree', 'tinsta'),
    'options_site_agreement_cancel_url' => 'http://google.com/',
    'options_site_agreement_cancel_title' => __('Cancel', 'tinsta'),
    'options_context_header_date_format' => translate_with_gettext_context('F Y', 'textual', 'tinsta'),
    'options_avatar_size' => 72,
    'options_avatar_size_small' => 42,
    'options_comments_style' => '',
    'options_comments_order_selector' => true,

    // Page Types
    'system_page_login_theming' => 'brand',
    'system_page_404_theming' => '',
    'system_page_404_content' => '',
    'system_page_search_force_post_type' => '',
    'system_page_search_disable_search' => false,

  ];

  foreach (get_post_types(['public' => true], 'objects') as $post_type) {
    $settings["post_type_{$post_type->name}_use_theme_styles"] = (bool)$post_type->_builtin;
    $settings["post_type_{$post_type->name}_layout"] = '';
    $settings["post_type_{$post_type->name}_append_authors"] = false;
    $settings["post_type_{$post_type->name}_reading_progress_indicator"] = $post_type->name == 'post' ? true : false;
    $settings["post_type_{$post_type->name}_append_post_nav"] = true;
    $settings["post_type_{$post_type->name}_outdated_notification"] = true;
    if (!($post_type->has_archive === 0 || $post_type->has_archive === false) || $post_type->name == 'post') {
      $settings["post_type_{$post_type->name}_layout_archive"] = '';
      $settings["post_type_{$post_type->name}_archive_show"] = 'excerpt';
      $settings["post_type_{$post_type->name}_archive_show_excerpt_words"] = 30;
      $settings["post_type_{$post_type->name}_archive_per_page"] = get_option('posts_per_page', 10);
    }
  }

  $settings = apply_filters('tinsta_default_options', $settings);

  return $settings;
}

/**
 * Check if current request is in login page.
 *
 * @return bool
 */
function tinsta_is_login_page()
{
  return !empty($GLOBALS['pagenow']) && in_array($GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php']);
}

/**
 * Build pagination links.
 *
 * @TODO add prev next rels
 *
 * @param string $type
 */
function tinsta_pagination($type = '')
{

  $open = '
    <nav class="pagination pagination-' . $type . ' navigation" role="navigation">
      <span class="screen-reader-text">' . __('Post navigation', 'tinsta') . '</span>';
  $close = '
    </nav>';
  $nav_links_open = '
      <div class="nav-links">';
  $nav_links_close = '
      </div>';
  $mid_size = 2;
  $prev_text = __('Prev', 'tinsta');
  $next_text = __('Next', 'tinsta');

  if ($type == 'comments') {
    if (get_comment_pages_count() > 1 && get_option('page_comments')) {
      $args = [
        'type' => 'plain',
        'mid_size' => $mid_size,
        'prev_text' => $prev_text,
        'next_text' => $next_text,
      ];
      echo $open;
      echo $nav_links_open;
      paginate_comments_links($args);
      echo $nav_links_close;
      echo $close;
    }
  } // Single post navigation (content is separated to pages).
  elseif ($type == 'singular' || $type == 'single') {
    $defaults = [
      'before' => $open . $nav_links_open,
      'after' => $nav_links_close . $close,
      'next_or_number' => 'next',
      'separator' => ' ',
      'nextpagelink' => $next_text,
      'previouspagelink' => $prev_text,
      'pagelink' => '<span>%</span>',
      'echo' => 0,
    ];
    echo strtr(wp_link_pages($defaults), [
      '<a' => '<a class="page-numbers"',
    ]);
  } // Next/Prev posts.
  elseif ($type == 'prevnext') {
    if (get_previous_post_link() || get_next_post_link()) {
      echo $open;
      echo $nav_links_open;
      ob_start();
      next_post_link('%link');
      previous_post_link('%link');
      echo strtr(ob_get_clean(), [
        '<a' => '<a class="page-numbers"',
      ]);
      echo $nav_links_close;
      echo $close;
    }
  } // Within the loop pagination.
  elseif ($type == 'archive') {
    if ($GLOBALS['wp_query']->max_num_pages > 1) {
      $args = [
        'type' => 'plain',
        'mid_size' => $mid_size,
        'prev_text' => $prev_text,
        'next_text' => $next_text,
      ];
      echo $open;
      echo $nav_links_open;
      echo paginate_links($args);
      echo $nav_links_close;
      echo $close;
    }
  }
}

/**
 * Check if current page should show before/after entries widget areas
 *
 * @return bool
 */
function tinsta_should_show_beforeafter_entries()
{

  static $result = null;
  if ($result === null) {
    $result = true;

    if (is_singular()) {
      $page_template = get_page_template_slug();
      if (in_array($page_template, [
        'template-content-only.php',
      ])) {
        $result = false;
      }
    } elseif (is_404()) {
      $result = false;
    }

    $result = apply_filters('tinsta_should_show_beforeafter_entries', $result);

  }

  return $result;
}

/**
 * Check if current page should show sidebars
 *
 * @return bool
 */
function tinsta_should_show_sidebars()
{

  static $result = null;
  if ($result === null) {
    $result = true;

    if (is_singular()) {
      $page_template = get_page_template_slug();
      if (in_array($page_template, [
        'template-nosidebars.php',
        'template-fullwidth-nosidebars.php',
        'template-content-only.php',
        'template-thin.php',
      ])) {
        $result = false;
      }
    } elseif (is_404()) {
      $result = false;
    }

    $result = apply_filters('tinsta_should_show_sidebars', $result);

  }

  return $result;
}

/**
 * Check if current page should goes fullscreen
 *
 * @return bool
 */
function tinsta_should_fullscreen()
{
  static $result = null;
  if ($result === null) {
    $result = (is_singular() && get_page_template_slug() == 'template-fullscreen.php');
    $result = apply_filters('tinsta_should_fullscreen', $result);
  }

  return $result;
}

/**
 * Render theme's primary menu
 */
function tinsta_primary_menu()
{
  echo '<div class="site-primary-menu-wrapper">';
  wp_nav_menu([
    'menu_class' => 'menu site-primary-menu',
    'container_class' => 'site-primary-menu-inner-wrapper',
    'theme_location' => 'main',
    'fallback_cb' => null,
  ]);
  echo '</div>';
}

/**
 * @param $display_mode
 * @param $post_type
 *
 * @return string
 */
function tinsta_render_posts_loop_post($display_mode, $post_type)
{

  // Cache possible templates.
  static $founded_templates = [];

  // Cache already known templates.
  static $post_type_layouts = [];

  // Cache tinsta_render_posts_loop_template filter.
  static $has_template_filter = null;

  if ($has_template_filter === null) {
    $has_template_filter = has_filter('tinsta_render_posts_loop_template');
  }

  $post_id = get_the_ID();

  if (post_type_supports($post_type, 'page-attributes')) {
    $page_template = get_page_template_slug($post_id);
  } else {
    $page_template = null;
  }

  // Cached.
  if (!isset($post_type_layouts[$post_type])) {
    if ($display_mode == 'single') {
      $post_type_layouts[$post_type] = get_theme_mod("post_type_{$post_type}_layout");
    } else {
      $post_type_layouts[$post_type] = get_theme_mod("post_type_{$post_type}_layout_archive");
    }
  }

  $template_key = "{$post_type}:{$display_mode}:{$page_template}:{$post_type_layouts[$post_type]}";

  // Cache located templates for faster finding.
  // If tinsta_render_posts_loop_template filter is implemented, the cache won't be relevant so it's disabled.
  if (!$has_template_filter && !empty($founded_templates[$template_key])) {
    load_template($founded_templates[$template_key], false);
    return $founded_templates[$template_key];
  }

  if ($page_template && in_array($page_template, ['template-content-only.php', 'template-fullscreen.php'])) {
    $templates = [
      "template-parts/entries/{$post_type}-embed.php",
      "template-parts/entries/post-embed.php",
    ];
  } else {
    $templates = [
      "template-parts/entries/{$post_type}-{$display_mode}-{$post_type_layouts[$post_type]}.php",
      "template-parts/entries/post-{$display_mode}-{$post_type_layouts[$post_type]}.php",
      "template-parts/entries/{$post_type}-{$display_mode}.php",
      "template-parts/entries/{$post_type}.php",
      "template-parts/entries/post-{$display_mode}.php",
      "template-parts/entries/post.php",
    ];
  }

  if ($has_template_filter) {
    $templates = apply_filters('tinsta_render_posts_loop_template', $templates, get_post(), $display_mode,
      $post_type, $post_type_layouts[$post_type]);
  }

  $founded_templates[$template_key] = locate_template($templates, true, false);
  return $founded_templates[$template_key];
}

/**
 * Render posts from current request (useful when serving posts with AJAX)
 *
 * @param $display_mode
 * @param $post_type
 */
function tinsta_render_posts_loop($display_mode = '', $post_type = '')
{

  rewind_posts();

  if (have_posts()) {

    if (!$post_type) {
      $post_type = get_post_type();
    }

    if (!$display_mode) {
      $display_mode = is_singular() ? 'single' : 'archive';
    }

    while (have_posts()) {
      the_post();
      tinsta_render_posts_loop_post($display_mode, $post_type);
    }

    // Pagination.
    if ($display_mode !== 'single') {
      tinsta_pagination('archive');
    }

  } else {

    locate_template('template-parts/global/no-entries.php');
  }

}

/**
 * Comment renderer callback
 *
 * @param $comment
 * @param $args
 * @param $depth
 */
function tinsta_comment_callback($comment, $args, $depth)
{
  static $current_user_can_moderate_comments = null;
  if ($current_user_can_moderate_comments === null) {
    $current_user_can_moderate_comments = current_user_can('moderate_comments');
  }

  if ($comment->comment_approved || $current_user_can_moderate_comments) {
    if ($comment->comment_type == 'pingback' || $comment->comment_type == 'trackback') {
      locate_template('template-parts/comments/pingback.php', true, false);
    } else {
      $args['depth'] = $depth > 1 ? $depth - 1 : $depth;
      locate_template('template-parts/comments/comment.php', true, false);
    }
  }
}

/**
 * Render social network's code
 */
function tinsta_the_social_code()
{
  $social_code = get_theme_mod('options_social_networks_code');
  if ($social_code) {
    echo "<div class=\"social-networks-code\">{$social_code}</div>";
  }
}

/**
 * Get post cover image from object (WP_Post or WP_Term).
 *
 * @param int $object
 * @param string $size
 * @param array $attr
 *
 * @return string
 */
function tinsta_get_category_cover_image($object = null, $size = '', $attr = [])
{

  if (!$object) {
    $object = get_queried_object();
  }

  $cover_id = 0;

  // Check if it is post.
  if (is_a($object, 'WP_Post')) {
    $cover_id = get_post_meta($object->ID, '_cover_id', true);
    $terms = wp_get_post_terms($object->ID);
    if ($terms) {
      foreach ($terms as $term) {
        $term_cover_id = get_term_meta($term->term_id, '_cover_id', true);
        if ($term_cover_id) {
          $cover_id = $term_cover_id;
        }
      }
    }
  } // Check if it is term.
  elseif (is_a($object, 'WP_Term')) {
    // @TODO add parent terms to the list too.
    $term_cover_id = get_term_meta($object->term_id, '_cover_id', true);
    if ($term_cover_id) {
      $cover_id = $term_cover_id;
    }
  }

  $cover_id = apply_filters('tinsta_get_category_cover_image', $cover_id, $object, $size, $attr);

  if ($cover_id) {
    return wp_get_attachment_image($cover_id, $size, false, $attr);
  }

  return '';
}

/**
 * Build|Rebuild stylesheet file from SCSS source and return as URI,
 * $scss_file must be extension-free
 *
 * @param string $scss_file
 * @param bool $include_tinsta_includes
 *
 * @return string
 */
function tinsta_get_stylesheet($scss_file, $include_tinsta_includes = false)
{

  $template_directory_uri = get_template_directory_uri();
  $template_directory = get_template_directory();

  $source_file = $scss_file . '.scss';
  if (file_exists($template_directory . '/assets/scss/' . $source_file)) {
    $source_file = $template_directory . '/assets/scss/' . $source_file;
  }
  $source_file_hash = md5($source_file);

  // @TODO make this inside stylesheet generation, move the hash calculation here,
  // this could improve the performance a bit.
  $args = apply_filters('tinsta_stylesheet_args', [
    'preview' => false,
    'preview_is_updated' => false,
    'variables' => [
      'tinsta_theme_dir_url' => '"' . $template_directory_uri . '"',
      'stylesheet_directory_uri' => '"' . get_stylesheet_directory_uri() . '"',
    ],
  ], $scss_file);

  $suffix = empty($args['preview']) ? ( SCRIPT_DEBUG ? '' : '.min' ) : '.preview';

  // WP_CONTENT_DIR relative
  $compiled_css_file = sprintf(TINSTA_STYLESHEET_CACHE_DIR . '/%d/%s-%s-%s%s.css', get_current_blog_id(),
    basename($scss_file), substr($source_file_hash, 0, 8), get_stylesheet(), $suffix);
  $compiled_css_uri = content_url($compiled_css_file);
  $compiled_css_filepath = WP_CONTENT_DIR . $compiled_css_file;

  // Stylesheet hashes.
  $stylesheet_hashes = get_transient('tinsta_theme');
  $stylesheet_hash_stored = empty($stylesheet_hashes[$source_file_hash]) ? null : $stylesheet_hashes[$source_file_hash];
  $stylesheet_hash_current = md5(json_encode($args['variables'])) . ( SCRIPT_DEBUG ? '1' : '0' );

  // Rebuild only when:
  if (// File not exists.
    !is_readable($compiled_css_filepath)

    // Force rebuild when debugging and no-cache is sent.
    || (SCRIPT_DEBUG && !empty($_SERVER['HTTP_CACHE_CONTROL']) && stripos($_SERVER['HTTP_CACHE_CONTROL'],
        'no-cache') !== false)

    // Preview mode.
    || (!empty($args['preview']) && !empty($args['preview_is_updated']))

    // Hash is changed.
    || $stylesheet_hash_current != $stylesheet_hash_stored

  ) {

    // WP Filesystem init.
    global $wp_filesystem;
    if (!$wp_filesystem) {
      require_once ABSPATH . '/wp-admin/includes/file.php';
      WP_Filesystem();
    }

    // Setup import paths.
    $import_paths = [dirname($source_file)];
    if ($include_tinsta_includes) {
      $import_paths[] = $template_directory . '/assets/scss';
    }

    // Init SCSS compiler.
    require_once 'phar://' . __DIR__ . '/vendor/scssphp-0.7.7.phar/scss.inc.php';
    $compiler = new \Leafo\ScssPhp\Compiler();

    if (SCRIPT_DEBUG) {
      $compiler->setIgnoreErrors(false);
      $compiler->setSourceMap(\Leafo\ScssPhp\Compiler::SOURCE_MAP_INLINE);
      $compiler->setSourceMapOptions([
        'sourceMapBasepath' => $import_paths[0],
      ]);
      $compiler->setFormatter('Leafo\ScssPhp\Formatter\Expanded');
      $compiler->setLineNumberStyle(\Leafo\ScssPhp\Compiler::LINE_COMMENTS);
    } else {
      $compiler->setIgnoreErrors(true);
      $compiler->setSourceMap(\Leafo\ScssPhp\Compiler::SOURCE_MAP_NONE);
      $compiler->setFormatter('Leafo\ScssPhp\Formatter\Crunched');
      $compiler->setLineNumberStyle(null);
    }

    $compiler->setImportPaths($import_paths);

    // Set variables.
    $compiler->setVariables($args['variables']);

    try {

      $dir = dirname($compiled_css_filepath);

      if (!$wp_filesystem->is_dir($dir)) {
        // Because direct fs actions are not allowed in themes, we should do like this.
        // so $wp_filesystem does not provide wp_mkdir_p(), we need to do it manually.
        $creation_current_path = '';
        $creation_status = true;
        foreach (explode('/', trim(dirname($compiled_css_filepath), '/')) as $creation_current_path_segment) {
          $creation_current_path .= '/' . $creation_current_path_segment;
          if (!$wp_filesystem->is_dir($creation_current_path) && !$wp_filesystem->mkdir($creation_current_path)) {
            $creation_status = false;
          }
        }

        if (!$creation_status) {
          throw new \Exception("Cannot prepare storage for stylesheets \"{$dir}\"");
        }
      }

      if (!is_readable($source_file)) {
        throw new \Exception("Cannot locate stylesheet source \"{$source_file}\"");
      }

      $output = $compiler->compile($wp_filesystem->get_contents($source_file));

      if ($wp_filesystem->put_contents($compiled_css_filepath, $output) === false) {
        throw new \Exception('Cannot build stylesheets');
      }

      // Saving hash value only when NOT in preview mode.
      if (empty($args['preview'])) {
        $stylesheet_hashes[$source_file_hash] = $stylesheet_hash_current;
        set_transient('tinsta_theme', $stylesheet_hashes);
      }

      do_action('tinsta_css_regenerated', $scss_file, $stylesheet_hash_current);

    } catch (\Exception $e) {
      error_log(__FUNCTION__ . '(): ' . $e->getMessage());
      if (SCRIPT_DEBUG) {
        wp_die(__FUNCTION__ . '(): ' . $e->getMessage());
      }
    }
  }

  return $compiled_css_uri;
}

/**
 * Get all colors from theme settings as palette
 *
 * @return array
 */
function tinsta_get_color_palette()
{
  $palette = null;
  if ($palette === null) {
    $palette = [];
    foreach (get_theme_mods() as $k => $val) {
      if ($val && is_scalar($val) && $val{0} === '#' && (strlen($val) === 7 || strlen($val) === 4)) {
        $palette[] = $val;
      }
    }
    $palette = array_unique($palette);
  }

  return $palette;
}

/**
 * Starts the lazyload buffer.
 *
 * @return bool
 */
function tinsta_lazyload_start_buffer()
{
  return ob_start(function ($content) {
    $callback = function ($matches) {
      static $skip_first_count = 2;
      if ($skip_first_count > 0) {
        $skip_first_count--;

        return $matches[0];
      }
      $matches[0] = str_ireplace(' src=', ' src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACAA==" data-src=',
        $matches[0]);
      $matches[0] = str_ireplace(' srcset=', ' data-srcset=', $matches[0]);

      return $matches[0];
    };
    $content = preg_replace_callback('#<img([^>]*)>#i', $callback, $content);

    return $content;
  });
}

/**
 * Build manifest.json data
 * More info at https://developers.google.com/web/fundamentals/web-app-manifest/
 *
 * @return array|false
 */
function tinsta_manifest_json()
{

  $cache = get_transient('tinsta_manifest_json');

  if (!$cache) {

    $icon_id = get_option('site_icon');
    if (!$icon_id) {
      $icon_id = get_theme_mod('custom_logo');
      if (!$icon_id) {
        return false;
      }
    }

    $cache = [
      'name' => get_bloginfo('blogdescription'),
      'short_name' => substr(get_bloginfo('name'), 0, 60),
      'icons' => [],
      'start_url' => get_home_url('/'),
      'background_color' => get_theme_mod('region_root_color_background'),
      'theme_color' => get_theme_mod('region_root_color_primary'),
      'display' => 'standalone',
      'scope' => get_home_url('/'),
    ];

    $icon_sizes = wp_get_attachment_metadata($icon_id, true);
    foreach ($icon_sizes['sizes'] as $size) {
      $src = wp_get_attachment_image_src($icon_id, [$size['width'], $size['height']]);
      if ($src && !empty($src[0])) {
        $cache['icons'][] = [
          'src' => $src[0],
          'type' => $size['mime-type'],
          'sizes' => $size['width'] . 'x' . $size['height'],
        ];
      }
    }

    set_transient('tinsta_manifest_json', $cache, 60 * 60);
  }

  return $cache;
}
