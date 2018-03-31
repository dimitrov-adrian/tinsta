<?php

// Check for minimal supported PHP and WordPress versions.
if (version_compare(phpversion(), '5.4.0', '<') || version_compare($wp_version, '4.4.0', '<')) {
  function _tinsta_unsupported_message()
  {
    echo '
      <div class="error">
        <p> ' . __('Tinsta theme requires PHP >= 5.4.0 and WordPress >= 4.4.', 'tinsta') . ' </p>
      </div>';
  }

  add_action('admin_notices', '_tinsta_unsupported_message');

  return;
}

// TINSTA_STYLESHEET_CACHE_DIR should be relative to WP_CONTENT_DIR and must starts with slash
if ( ! defined('TINSTA_STYLESHEET_CACHE_DIR')) {
  define('TINSTA_STYLESHEET_CACHE_DIR', '/static');
}

// Ensure TINSTA_DISABLE_INTEGRATIONS is set.
if ( ! defined('TINSTA_ENABLE_INTEGRATIONS')) {
  define('TINSTA_ENABLE_INTEGRATIONS', false);
}

// Ensure TINSTA_DEBUG is set.
if ( ! defined('TINSTA_DEBUG')) {
  define('TINSTA_DEBUG', defined('WP_DEBUG') && WP_DEBUG);
}

// Vendors.
require_once __DIR__ . '/includes/vendor/autoload.php';

// Define and hook only required.

// Base theme.
require_once __DIR__ . '/includes/theme.php';


/**
 * Build pagination links.
 *
 * @param string $type
 */
function tinsta_pagination($type = '')
{

  $open            = '
    <nav class="pagination pagination-' . $type . ' navigation" role="navigation">
      <span class="screen-reader-text">' . __('Post navigation', 'tinsta') . '</span>';
  $close           = '
    </nav>';
  $nav_links_open  = '
      <div class="nav-links">';
  $nav_links_close = '
      </div>';
  $mid_size        = 2;
  $prev_text       = __('Prev', 'tinsta');
  $next_text       = __('Next', 'tinsta');

  if (function_exists('is_bbpress') && is_bbpress()) {
    // TODO integrate bbpress paginator here.
    return;
  } // Comments pagination.
  elseif ($type == 'comments') {
    if (get_comment_pages_count() > 1 && get_option('page_comments')) {
      $args = [
        'type'      => 'plain',
        'mid_size'  => $mid_size,
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
      'before'           => $open . $nav_links_open,
      'after'            => $nav_links_close . $close,
      'next_or_number'   => 'next',
      'separator'        => ' ',
      'nextpagelink'     => $next_text,
      'previouspagelink' => $prev_text,
      'pagelink'         => '<span>%</span>',
      'echo'             => 0,
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
        'type'      => 'plain',
        'mid_size'  => $mid_size,
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

  // Before post entries sidebar area.
  $before_entries_sidebar = tinsta_get_post_type_sidebar('before-entries');
  if ($before_entries_sidebar) {
    dynamic_sidebar($before_entries_sidebar);
  }

  // Actual post rendering
  if ( ! $post_type) {
    $post_type = get_post_type();
  }
  if ( ! $display_mode) {
    $display_mode = is_singular() ? 'single' : 'archive';
  }

  echo "<div class=\"site-entries site-entries-type-{$post_type} site-entries-{$display_mode}\">";
  if (have_posts()) {
    tinsta_render_posts_loop($display_mode, $post_type);
  } else {
    get_template_part('template-parts/misc/no-entries', $post_type);
  }
  echo '</div>';

  // Pagination.
  if ($display_mode !== 'single') {
    tinsta_pagination('archive');
  }

  // After post entries sidebar area.
  $after_entries_sidebar = tinsta_get_post_type_sidebar('after-entries');
  if ($after_entries_sidebar) {
    dynamic_sidebar($after_entries_sidebar);
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

  while (have_posts()) {

    the_post();
    $page_template  = get_page_template_slug(get_the_ID());
    $post_post_type = $post_type ? $post_type : get_post_type();

    if ( ! isset($post_type_layouts[$post_type])) {
      $post_type_layouts[$post_type] = get_theme_mod("post_type_{$post_type}_layout");
    }

    if (in_array($page_template, ['template-content-only.php', 'template-fullscreen.php'])) {
      $templates = [
        "template-parts/entries/{$post_post_type}-embed.php",
        "template-parts/entries/post-embed.php",
      ];
    } else {
      $templates = [
        "template-parts/entries/{$post_post_type}-{$display_mode}-{$post_type_layouts[$post_type]}.php",
        "template-parts/entries/post-{$display_mode}-{$post_type_layouts[$post_type]}.php",
        "template-parts/entries/{$post_post_type}-{$display_mode}.php",
        "template-parts/entries/{$post_post_type}.php",
        "template-parts/entries/post-{$display_mode}.php",
        "template-parts/entries/post.php",
      ];
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

  $social_code = apply_filters('tinsta_the_social_code', (string)get_theme_mod('component_social_networks_code'));

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

  if ( ! $object) {
    $object = get_queried_object();
  }

  $cover_id = 0;

  // Check if it is post.
  if (is_a($object, 'WP_Post')) {
    $cover_id = get_post_meta($object->ID, '_cover_id', true);
    $terms    = wp_get_post_terms($object->ID);
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
 * Get all post type based sidebar names with labels
 *
 * @return array
 */
function tinsta_get_post_type_sidebar_names()
{
  return [
    'primary'        => __('Primary Sidebar', 'tinsta'),
    'secondary'      => __('Secondary Sidebar', 'tinsta'),
    'before-entries' => __('Before Entries', 'tinsta'),
    'after-entries'  => __('After Entries', 'tinsta'),
  ];
}

/**
 * Check and post type based sidebars
 *
 * Pattern: <sidebar_name>-[post_type]-[single|archive]
 *
 * @param $sidebar_name
 *
 * @return string
 */
function tinsta_get_post_type_sidebar($sidebar_name = '')
{
  $post_type = get_post_type();
  $supports  = get_theme_mod("post_type_{$post_type}_sidebars_{$sidebar_name}");

  $sidebar_id = $sidebar_name;
  if ($supports && $supports != 'none') {
    $sidebar_id .= "-{$post_type}";
    if ($supports == 'separated') {
      if (is_singular()) {
        $sidebar_id .= '-single';
      } else {
        $sidebar_id .= '-archive';
      }
    }
  }

  $sidebar_id = apply_filters('tinsta_get_post_type_sidebar', $sidebar_id);

  return is_active_sidebar($sidebar_id) ? $sidebar_id : false;
}


/**
 * Default theme mod array
 *
 * @return array
 */
function tinsta_get_options_defaults()
{

  static $settings = NULL;

  if ($settings === NULL) {

    // Add all theme mods.
    $settings = [

      // Misc
      // @TODO re-arrange to other sections.
      'misc_legacy_support'                           => true,
      'misc_excerpt_more'                             => '',
      'misc_nice_scroll'                              => false,
      'effects'                                       => false,
      'effects_roundness'                             => 0,

      /**
       * Typography
       */
      'typography_font_size'                          => 14,
      'typography_font_family'                        => 'medium-content-sans-serif-font, -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif',
      'typography_font_family_headings'               => '',
      'typography_font_google'                        => '',
      'typography_font_headings_google'               => '',
      'typography_font_headings_style'                => '',
      'typography_text_justify'                       => false,
      'typography_text_wordbreak'                     => false,
      'typography_form_spacing'                       => 6,
      'typography_form_borders'                       => 1,
      'typography_form_button_style'                  => 'fill',
      'typography_font_button_text_style'             => 'small-caps',

      // Sections
      'section_root_breakpoint_desktop'               => '1400px',
      'section_root_breakpoint_tablet'                => '920px',
      'section_root_breakpoint_mobile'                => '568px',
      'section_root_height_full'                      => true,
      'section_root_layout'                           => '',
      'section_root_width'                            => 980,
      'section_root_color_background'                 => '#ffffff',
      'section_root_color_primary'                    => '#039be5',
      'section_root_color_primary_inverted'           => '#ffffff',
      'section_root_color_secondary'                  => '#e55a19',
      'section_root_color_secondary_inverted'         => '#ffffff',
      'section_root_image'                            => '',
      'section_root_image_size'                       => 'auto',
      'section_root_image_repeat'                     => true,
      'section_root_image_attachment_scroll'          => true,
      'section_root_image_position_x'                 => 'center',
      'section_root_image_position_y'                 => 'center',
      'section_topline_sticky'                        => false,
      'section_topline_color_background'              => '#eeeeee',
      'section_topline_color_background_opacity'      => 100,
      'section_topline_color_foreground'              => '#555555',
      'section_header_padding'                        => 20,
      'section_header_sticky'                         => false,
      'section_header_background_wrapper'             => true,
      'section_header_image'                          => '',
      'section_header_image_size'                     => 'auto',
      'section_header_image_repeat'                   => true,
      'section_header_image_attachment_scroll'        => true,
      'section_header_image_position_x'               => 'center',
      'section_header_image_position_y'               => 'center',
      'section_header_color_background'               => '#4285f4',
      'section_header_color_background_opacity'       => 100,
      'section_header_color_foreground'               => '#eeeeee',
      'section_primary_menu_movetop'                  => false,
      'section_primary_menu_aligncenter'              => false,
      'section_primary_menu_color_background'         => '#4285f4',
      'section_primary_menu_color_background_opacity' => 100,
      'section_primary_menu_color_foreground'         => '#ffffff',
      'section_main_color_background'                 => '#ffffff',
      'section_main_color_background_opacity'         => 100,
      'section_main_color_foreground'                 => '#222222',
      'section_main_image'                            => '',
      'section_main_image_size'                       => 'auto',
      'section_main_image_repeat'                     => true,
      'section_main_image_attachment_scroll'          => true,
      'section_main_image_position_x'                 => 'center',
      'section_main_image_position_y'                 => 'center',
      'section_sidebar_primary_width'                 => 220,
      'section_sidebar_primary_color_background'      => '#ffffff',
      'section_sidebar_primary_color_foreground'      => '#222222',
      'section_sidebar_secondary_width'               => 160,
      'section_sidebar_secondary_color_background'    => '#ffffff',
      'section_sidebar_secondary_color_foreground'    => '#222222',
      'section_footer_background_highlight_inner'     => false,
      'section_footer_image'                          => '',
      'section_footer_image_size'                     => 'auto',
      'section_footer_image_repeat'                   => true,
      'section_footer_image_attachment_scroll'        => true,
      'section_footer_image_position_x'               => 'center',
      'section_footer_image_position_y'               => 'center',
      'section_footer_color_background'               => '#263238',
      'section_footer_color_background_opacity'       => 100,
      'section_footer_color_foreground'               => '#cfd8dc',
      'section_bottomline_sticky'                     => false,
      'section_bottomline_color_background'           => '#455a64',
      'section_bottomline_color_background_opacity'   => 100,
      'section_bottomline_color_foreground'           => '#eeeeee',

      // Components
      'component_site_topline'                        => '',
      'component_site_bottomline'                     => get_bloginfo('name') . ' - ' . date('Y'),
      'component_header_markup'                       => '',
      'component_footer_markup'                       => '',
      'component_social_networks_code'                => '',
      'component_breadcrumbs_include_home'            => true,
      'component_outdated_post_time'                  => 0,
      'component_outdated_post_message'               => __('This article is older than %time%, the content might not be relevant anymore.', 'tinsta'),
      'component_pagination_style'                    => 'borders',
      'component_scrolltop'                           => '',
      'component_site_agreement_enable'               => true,
      'component_site_agreement_text'                 => __('I agree with site terms.', 'tinsta'),
      'component_site_agreement_agree_button'         => __('Agree', 'tinsta'),
      'component_site_agreement_cancel_url'           => 'http://google.com/',
      'component_site_agreement_cancel_title'         => __('Cancel', 'tinsta'),
      'component_context_header_date_format'          => translate_with_gettext_context('F Y', 'textual', 'tinsta'),
      'component_avatar_size'                         => 72,
      'component_avatar_size_small'                   => 42,

      // Page Types
      'system_page_login_theming'                     => 'brand',
      'system_page_404_theming'                       => '',
      'system_page_search_hide_widgets'               => false,
      'system_page_search_search_field'               => '',

    ];

    foreach (get_post_types(['public' => true], 'objects') as $post_type) {
      $settings["post_type_{$post_type->name}_use_defaults"]    = (bool)$post_type->_builtin;
      $settings["post_type_{$post_type->name}_layout"]          = '';
      $settings["post_type_{$post_type->name}_layout_page_id"]  = null;
      $settings["post_type_{$post_type->name}_append_authors"]  = false;
      $settings["post_type_{$post_type->name}_append_post_nav"] = true;

      if ( ! ($post_type->has_archive === 0 || $post_type->has_archive === false) || $post_type->name == 'post') {
        $settings["post_type_{$post_type->name}_layout_archive"] = '';
      }
    }

    $settings = apply_filters('tinsta_default_options', $settings);
  }

  return $settings;
}

/**
 * Check if stylesheet cache directory exists AND is writable OR can create such.
 *
 * @return bool
 */
function tinsta_check_stylesheet_cache_directory()
{
  if ( ! is_dir(WP_CONTENT_DIR . TINSTA_STYLESHEET_CACHE_DIR)) {
    if ( ! wp_mkdir_p(WP_CONTENT_DIR . TINSTA_STYLESHEET_CACHE_DIR)) {
      return false;
    }
  }

  return is_writeable(WP_CONTENT_DIR . TINSTA_STYLESHEET_CACHE_DIR);
}

/**
 * Build|Rebuild stylesheet file from SCSS source and return as URI,
 * $scss_file must be prefixfree and provided as absolute path like get_template_directory() . '/assets/scss/somefile
 *
 * @param $scss_file
 *
 * @return string
 */
function tinsta_get_stylesheet($scss_file)
{

  $source_file = $scss_file . '.scss';
  if (file_exists(get_template_directory() . '/assets/scss/' . $source_file)) {
    $source_file = get_template_directory() . '/assets/scss/' . $source_file;
  }
  $source_file_hash = md5($source_file);

  $args = apply_filters('tinsta_get_stylesheet_args', [
    'preview'            => false,
    'preview_is_updated' => false,
    'variables'          => [
      'tinsta_version'           => wp_get_theme()->Version,
      'tinsta_theme_dir_url'     => '"' . get_template_directory_uri() . '"',
      'stylesheet_directory_uri' => '"' . get_stylesheet_directory_uri() . '"',
    ],
  ], $scss_file);

  $suffix = empty($args['preview']) ? '' : '.preview';

  // WP_CONTENT_DIR relative
  $compiled_css_file     = sprintf(TINSTA_STYLESHEET_CACHE_DIR . '/%s-%s-%s%s.css', basename($scss_file), substr($source_file_hash, 0, 8), get_stylesheet(),
    $suffix);
  $compiled_css_uri      = content_url($compiled_css_file);
  $compiled_css_filepath = WP_CONTENT_DIR . $compiled_css_file;

  // Stylesheet hashes.
  $stylesheet_hashes       = get_transient('tinsta_theme');
  $stylesheet_hash_stored  = empty($stylesheet_hashes[$source_file_hash]) ? null : $stylesheet_hashes[$source_file_hash];
  $stylesheet_hash_current = md5(serialize($args['variables']));

  // Rebuild only when:
  if ( // File not exists.
    ! is_readable($compiled_css_filepath)

    // Force rebuild when debugging and no-cache is sent,
    // but only when not in preview mode because it's automatically built.
    || ((TINSTA_DEBUG && ! empty($_SERVER['HTTP_CACHE_CONTROL']) && stripos($_SERVER['HTTP_CACHE_CONTROL'],
          'no-cache') !== false) && (empty($args['preview']) && empty($args['preview_is_updated'])))

    // Preview mode.
    || ( ! empty($args['preview']) && ! empty($args['preview_is_updated']))

    // Hash is changed.
    || $stylesheet_hash_current != $stylesheet_hash_stored

  ) {

    // Init SCSS compiler.
    $compiler = new \Leafo\ScssPhp\Compiler();

    if ( ! TINSTA_DEBUG) {
      $compiler->setFormatter('Leafo\ScssPhp\Formatter\Compressed');
      $compiler->setIgnoreErrors(true);
    }

    // Setup import paths.
    $import_paths = array_unique([

      // Tinsta's SCSS directory
      get_template_directory() . '/assets/scss',

      // SCSS directory from current file.
      dirname($source_file),
    ]);
    $compiler->setImportPaths($import_paths);

    // Set variables.
    $compiler->setVariables($args['variables']);

    try {

      if ( ! tinsta_check_stylesheet_cache_directory()) {
        throw new \Exception('Cannot prepare storage for stylesheets (' . dirname($compiled_css_filepath) . ')');
      }

      if ( ! file_exists($source_file)) {
        throw new \Exception("Cannot locate stylesheet source ({$source_file})");
      }

      $output = $compiler->compile(file_get_contents($source_file));

      if (file_put_contents($compiled_css_filepath, $output) === false) {
        throw new \Exception('Cannot build stylesheets');
      }

      // Saving hash value only when NOT in preview mode.
      if (empty($args['preview'])) {
        $stylesheet_hashes[$source_file_hash] = $stylesheet_hash_current;
        set_transient('tinsta_theme', $stylesheet_hashes);
      }

    } catch (\Exception $e) {
      syslog(LOG_ERR, __FUNCTION__ . '(): ' . $e->getMessage());
      if (TINSTA_DEBUG) {
        wp_die(__FUNCTION__ . '(): ' . $e->getMessage());
      }
    }
  }

  return $compiled_css_uri;
}
