<?php


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
 * Render posts with wrapper from current request
 *
 * @param $post_type
 * @param $display_mode
 */
function tinsta_render_posts($post_type = '', $display_mode = '')
{

  rewind_posts();

  // Before post entries sidebar area.
  if (is_active_sidebar('before-entries')) {
    echo '<div class="sidebar-before-entries">';
    dynamic_sidebar('before-entries');
    echo '</div>';
  }

  // Actual post rendering
  if (!$post_type) {
    $post_type = get_post_type();
  }
  if (!$display_mode) {
    $display_mode = is_singular() ? 'single' : 'archive';
  }

  echo "<div class=\"site-entries site-entries-type-{$post_type} site-entries-{$display_mode}\">";
  if (have_posts()) {
    tinsta_render_posts_loop($display_mode, $post_type);
  } else {
    get_template_part('template-parts/theme/no-entries', $post_type);
  }
  echo '</div>';

  // Pagination.
  if ($display_mode !== 'single') {
    tinsta_pagination('archive');
  }

  // After post entries sidebar area.
  if (is_active_sidebar('after-entries')) {
    echo '<div class="sidebar-after-entries">';
    dynamic_sidebar('after-entries');
    echo '</div>';
  }

  // Append comments for singular modes.
  if ($display_mode == 'single') {
    comments_template('/comments.php', true);
  }

}


/**
 * Render posts from current request (useful when serving posts with AJAX)
 *
 * @param $display_mode
 * @param $post_type
 */
function tinsta_render_posts_loop($display_mode, $post_type = '')
{

  static $post_type_layouts = [];

  static $has_template_filter = NULL;
  if ($has_template_filter === NULL) {
    $has_template_filter = has_filter('tinsta_render_posts_loop_template');
  }

  while (have_posts()) {

    the_post();
    $page_template = get_page_template_slug(get_the_ID());
    $post_post_type = $post_type ? $post_type : get_post_type();

    if (!isset($post_type_layouts[$post_type])) {
      if ($display_mode == 'single') {
        $post_type_layouts[$post_type] = get_theme_mod("post_type_{$post_type}_layout");
      } else {
        $post_type_layouts[$post_type] = get_theme_mod("post_type_{$post_type}_layout_archive");
      }
    }

    if (in_array($page_template, ['template-content-only.php', 'template-fullscreen.php'])) {
      $templates = [
        "template-parts/entries/{$post_post_type}-embed.php",
        "template-parts/entries/post-embed.php",
      ];
    } else {
      $templates = [
        "template-parts/entries/post-{$display_mode}-{$post_type_layouts[$post_type]}.php",
        "template-parts/entries/{$post_post_type}-{$display_mode}.php",
        "template-parts/entries/{$post_post_type}.php",
        "template-parts/entries/post-{$display_mode}.php",
        "template-parts/entries/post.php",
      ];
      if (!empty($post_type_layouts[$post_type])) {
        array_unshift($templates, "template-parts/entries/{$post_post_type}-{$display_mode}-{$post_type_layouts[$post_type]}.php");
      }
    }

    if ($has_template_filter) {
      $templates = apply_filters('tinsta_render_posts_loop_template', $templates, get_post(), $display_mode, $post_post_type, $post_type_layouts[$post_type]);
    }

    locate_template($templates, true, false);

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
  if ($comment->comment_approved || current_user_can('moderate_comments')) {
    if ($comment->comment_type == 'pingback' || $comment->comment_type == 'trackback') {
      get_template_part('template-parts/comments/pingback');
    } else {
      $args['depth'] = $depth > 1 ? $depth - 1 : $depth;
      get_template_part('template-parts/comments/comment');
    }
  }
}


/**
 * Render social network's code
 *
 * @TODO integrate with most of the plugins
 */
function tinsta_the_social_code()
{
  $social_code = get_theme_mod('component_social_networks_code');
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
 * Default theme mod array
 *
 * @return array
 */
function tinsta_get_options_defaults()
{

  $privacy_policy_link = home_url('?p=' . get_option('wp_page_for_privacy_policy'));

  // Add all theme mods.
  $settings = [

    // Misc
    // @TODO re-arrange to other regions.
    'component_effects_animations' => 0,
    'component_effects_shadows' => 0,
    'component_effects_smooth_scroll' => false,
    'component_effects_lazyload' => false,
    'component_seo_enable' => true,

    /**
     * Typography
     */
    'typography_font_size' => 13,
    'typography_font_line_height' => 140,
    //'typography_font_family' => 'medium-content-sans-serif-font, -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif',
    'typography_font_family' => 'medium-content-sans-serif-font, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
    'typography_font_family_headings' => '',
    'typography_font_google' => '',
    'typography_font_headings_google' => '',
    'typography_font_headings_style' => '',
    'typography_text_justify' => false,
    'typography_text_wordbreak' => false,
    'typography_form_spacing' => 30,
// @TODO
//    'typography_text_font_weight' => 'normal',
//    'typography_text_font_weight' => 'normal',
//    'typography_font_letterspacing' => 'normal',
//    'typography_font_wordspacing' => 'normal',
//    'typography_link_style' => 'underline', // @TODO
    'typography_roundness' => 0,
    'typography_brightness' => 50,
    'typography_bordering' => 1,
    'typography_form_button_style' => 'fill',
    'typography_font_button_text_style' => 'small-caps',
    'typography_text_enhancements' => false,

    // Regions
    'region_root_breakpoint_tablet' => '920px',
    'region_root_breakpoint_mobile' => '568px',
    'region_root_height_full' => true,
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
    'region_topline_color_background' => '#eeeeee',
    'region_topline_color_background_opacity' => 100,
    'region_topline_color_foreground' => '#555555',
    'region_header_layout' => '',
    'region_header_padding' => 20,
    'region_header_sticky' => false,
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
    'region_primary_menu_layout' => '',
    'region_primary_menu_position' => '',
    'region_primary_menu_aligncenter' => false,
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
    'region_sidebar_primary_width' => 220,
    'region_sidebar_primary_color_background' => '#ffffff',
    'region_sidebar_primary_color_background_opacity' => 100,
    'region_sidebar_primary_color_foreground' => '#222222',
    'region_sidebar_primary_color_primary' => '#4285f4',
    'region_sidebar_primary_color_secondary' => '#e55a19',
    'region_sidebar_secondary_width' => 160,
    'region_sidebar_secondary_color_background' => '#ffffff',
    'region_sidebar_secondary_color_background_opacity' => 100,
    'region_sidebar_secondary_color_foreground' => '#222222',
    'region_sidebar_secondary_color_primary' => '#4285f4',
    'region_sidebar_secondary_color_secondary' => '#e55a19',
    'region_footer_layout' => '',
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
    'region_bottomline_color_background' => '#455a64',
    'region_bottomline_color_background_opacity' => 100,
    'region_bottomline_color_foreground' => '#eeeeee',

    // Components
    'component_site_topline' => '',
    'component_site_bottomline' => get_bloginfo('name') . ' - ' . date('Y'),
    'component_header_markup' => '',
    'component_footer_markup' => '',
    'component_social_networks_code' => '',
    'component_breadcrumbs_include_home' => true,
    'component_outdated_post_time' => 0,
    'component_outdated_post_message' => __('This article is older than %time%, the content might not be relevant anymore.', 'tinsta'),
    'component_pagination_style' => 'bordered',
    'component_scrolltop' => '',
    'component_site_agreement_enable' => true,
    'component_site_agreement_style' => 'center',
    'component_site_agreement_text' => sprintf(__('I agree with site <a href="%s">terms</a>.', 'tinsta'), $privacy_policy_link),
    'component_site_agreement_agree_button' => __('Agree', 'tinsta'),
    'component_site_agreement_cancel_url' => 'http://google.com/',
    'component_site_agreement_cancel_title' => __('Cancel', 'tinsta'),
    'component_context_header_date_format' => translate_with_gettext_context('F Y', 'textual', 'tinsta'),
    'component_avatar_size' => 72,
    'component_avatar_size_small' => 42,
    'component_comments_style' => '',
    'component_comments_order_selector' => true,

    // Page Types
    'system_page_login_theming' => 'brand',
    'system_page_404_theming' => '',
    'system_page_404_display' => '',
    'system_page_search_search_force_post_type' => '',

  ];

  foreach (get_post_types(['public' => true], 'objects') as $post_type) {
    $settings["post_type_{$post_type->name}_use_defaults"] = (bool) $post_type->_builtin;
    $settings["post_type_{$post_type->name}_layout"] = '';
    $settings["post_type_{$post_type->name}_append_authors"] = false;
    $settings["post_type_{$post_type->name}_append_post_nav"] = true;
    $settings["post_type_{$post_type->name}_outdated_notification"] = true;
    if ( ! ($post_type->has_archive === 0 || $post_type->has_archive === false ) || $post_type->name == 'post' ) {
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
  require_once( ABSPATH . '/wp-admin/includes/file.php' );
  WP_Filesystem();
  global $wp_filesystem;

  $source_file = $scss_file . '.scss';
  if ($wp_filesystem->exists(get_template_directory() . '/assets/scss/' . $source_file)) {
    $source_file = get_template_directory() . '/assets/scss/' . $source_file;
  }
  $source_file_hash = md5($source_file);

  $args = apply_filters('tinsta_get_stylesheet_args', [
    'preview' => false,
    'preview_is_updated' => false,
    'variables' => [
      'tinsta_version' => wp_get_theme()->Version,
      'tinsta_theme_dir_url' => '"' . get_template_directory_uri() . '"',
      'stylesheet_directory_uri' => '"' . get_stylesheet_directory_uri() . '"',
    ],
  ], $scss_file);
  // @TODO add scss variables sanitization.

  $suffix = empty($args['preview']) ? '' : '.preview';

  // WP_CONTENT_DIR relative
  $compiled_css_file = sprintf(TINSTA_STYLESHEET_CACHE_DIR . '/%d/%s-%s-%s%s.css', get_current_blog_id(), basename($scss_file), substr($source_file_hash, 0, 8), get_stylesheet(), $suffix);
  $compiled_css_uri = content_url($compiled_css_file);
  $compiled_css_filepath = WP_CONTENT_DIR . $compiled_css_file;

  // Stylesheet hashes.
  $stylesheet_hashes = get_transient('tinsta_theme');
  $stylesheet_hash_stored = empty($stylesheet_hashes[$source_file_hash]) ? null : $stylesheet_hashes[$source_file_hash];
  $stylesheet_hash_current = md5(serialize($args['variables']));

  // Rebuild only when:
  if (
    // File not exists.
    !file_exists($compiled_css_filepath)

    // Force rebuild when debugging and no-cache is sent,
    // but only when not in preview mode because it's automatically built.
    || (
      ( SCRIPT_DEBUG && !empty($_SERVER['HTTP_CACHE_CONTROL'])
        && stripos($_SERVER['HTTP_CACHE_CONTROL'], 'no-cache') !== false
      )
      && ( empty($args['preview']) && empty($args['preview_is_updated']))
    )

    // Preview mode.
    || ( !empty($args['preview']) && !empty($args['preview_is_updated']) )

    // Hash is changed.
    || $stylesheet_hash_current != $stylesheet_hash_stored

  ) {

    // Init SCSS compiler.
    require_once 'phar://' . __DIR__ . '/vendor/scssphp.phar/scss.inc.php';
    $compiler = new \Leafo\ScssPhp\Compiler();

    if ( SCRIPT_DEBUG ) {
      $compiler->setFormatter('Leafo\ScssPhp\Formatter\Expanded');
    } else {
      $compiler->setFormatter('Leafo\ScssPhp\Formatter\Compressed');
      $compiler->setIgnoreErrors(true);
    }

    // Setup import paths.
    $import_paths = [ dirname($source_file) ];
    if ($include_tinsta_includes) {
      $import_paths[] = get_template_directory() . '/assets/scss';
    }
    $compiler->setImportPaths($import_paths);

    // Set variables.
    $compiler->setVariables($args['variables']);

    try {

      $dir = dirname($compiled_css_filepath);

      if (!$wp_filesystem->is_dir($dir) && !wp_mkdir_p($dir)) {
        throw new \Exception("Cannot prepare storage for stylesheets \"{$dir}\"");
      }

      if (!file_exists($source_file)) {
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

    } catch (\Exception $e) {
      error_log(__FUNCTION__ . '(): ' . $e->getMessage());
      if ( SCRIPT_DEBUG ) {
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
    foreach (tinsta_get_options_defaults() as $k => $val) {
      if ( $val && $val{0} === '#' && ( strlen($val) === 7 || strlen($val) === 4 ) ) {
        $palette[] = $val;
      }
    }
    $palette = array_unique($palette);
  }
  return $palette;
}

/**
 * Starts the lazyload buffer.
 */
function tinsta_lazyload_start_buffer()
{

  static $started = false;
  if ($started) {
    return false;
  }
  $started = true;

  ob_start(function ($content) {
    $skip_first_count = 2;
    $callback = function ($matches) use (&$skip_first_count) {

      if ($skip_first_count > 0) {
        $skip_first_count--;
        return $matches[0];
      }

      $empty_image_attr = 'src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACAA=="';
      //$empty_image_attr = '';
      $matches[0] = str_ireplace(' src=', ' ' . $empty_image_attr . ' data-src=', $matches[0]);
      $matches[0] = str_ireplace(' srcset=', ' data-srcset=', $matches[0]);

      return $matches[0];
    };
    $content = preg_replace_callback('#<img([^>]*)>#i', $callback, $content);

    return $content;
  });
}