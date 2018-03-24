<?php

// @TODO Check for PHP 5.6, and return if not met.

// Vendors.
require_once __DIR__ . '/includes/vendor/autoload.php';

// Theme.
require_once __DIR__ . '/includes/theme.php';
require_once __DIR__ . '/includes/posts.php';
require_once __DIR__ . '/includes/comments.php';


/**
 * Build pagination links.
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

  // BBPress has its own breadcrumb presentation before the forum content.
  if (function_exists('is_bbpress') && is_bbpress()) {
    // TODO integrate bbpress paginator here.
    return;
  } // Comments pagination.
  elseif ($type == 'comments') {
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
 * Breadcrumbs
 */
function tinsta_the_breadcrumbs()
{

  // Do not show breadcrumbs on homepage.
  if (is_front_page()) {
    return;
  }

  $trail = [];
  $min_trails_to_show = 1;

  if (get_theme_mod('include_home_in_breadcrumbs', true)) {
    $trail[get_home_url()] = get_bloginfo('name');
    $min_trails_to_show++;
  }

  if (is_search()) {
    $trail[get_search_link(get_search_query(false))] = sprintf(__('Search: %s', 'tinsta'), get_search_query());
  }
  elseif (is_tag()) {
    $breadcrumbs_trail[] = single_term_title('', TRUE);
  }
  elseif (is_month()) {
    $breadcrumbs_trail[] = sprintf(__('Archives: %s', 'insta'), get_the_time('F, Y'));
  }
  elseif (is_year()) {
    $breadcrumbs_trail[] = sprintf(__('Archives: %s', 'insta'), get_the_time('Y'));
  }
  elseif (is_author()) {
    $breadcrumbs_trail[] = __('Posts by %s', 'insta');
  }
  elseif (is_day()) {
    $breadcrumbs_trail[] = sprintf(__('Archives: %s', 'insta'), get_the_time('F jS, Y'));
  }
  elseif (is_home()) {
    $breadcrumbs_trail[] = single_post_title('', FALSE);
  }
  else {

    // Post type archive link.
    //if (get_post_type_archive_link(get_post_type())) {
    //  $post_type_object = get_post_type_object(get_post_type());
    //  $trail[get_post_type_archive_link(get_post_type())] = $post_type_object->label;
    //}

    if (is_post_type_archive() || is_singular()) {
      $post_type_object = get_post_type_object(get_post_type());
      if ($post_type_object) {
        $post_type_archive_link = get_post_type_archive_link($post_type_object->name);
        if ($post_type_archive_link) {
          $trail[$post_type_archive_link] = $post_type_object->label;
        }
      }
    }

    // Check if taxonomy is public.
    $queried_object = get_queried_object();
    if (is_a($queried_object, 'WP_Taxonomy')) {
      if (1 || strpos(strtolower($queried_object->taxonomy), 'cat')) {
        foreach (get_ancestors($queried_object->term_id, $queried_object->taxonomy, 'taxonomy') as $ancestor) {
          $category = get_category($ancestor);
          $trail[get_category_link($ancestor)] = $category->name;
        }
        $trail[get_term_link($queried_object)] = $queried_object->name;
      }
    }

    if (is_singular() && is_post_type_hierarchical(get_post_type())) {
      foreach (array_reverse(get_ancestors(get_the_ID(), get_post_type(), 'post_type')) as $ancestor) {
        $post = get_post($ancestor);
        $trail[get_permalink($post)] = $post->post_title;
      }
    }
    elseif (is_category() || has_category()) {

      //      $the_category = get_the_category();
      //      $the_category = array_shift($the_category);
      //      if ($the_category) {
      //        foreach (get_ancestors($the_category->term_id, $the_category->taxonomy, 'taxonomy') as $ancestor) {
      //          $category = get_category($ancestor);
      //          $trail[get_category_link($ancestor)] = $category->name;
      //        }
      //        $trail[get_category_link($the_category)] = $the_category->name;
      //      }
    }

    if (is_singular()) {
      $trail[get_permalink()] = get_the_title();
    }
  }

  $trail = apply_filters('tinsta_breadcrumb_trail', $trail);

  if (is_singular() && count($trail) < $min_trails_to_show) {
    return;
  }

  ?>
  <div class="breadcrumbs">
    <?php foreach ($trail as $trail_link => $trail_label): ?>
      <a href="<?php echo $trail_link ?>"
         class="trail <?php echo end($trail) == $trail_label ? 'active' : '' ?>"><?php echo $trail_label ?></a>
    <?php endforeach ?>
  </div>
  <?php
}

/**
 * Render posts with wrapper from current request
 *
 * @param $post_type
 * @param $display_mode
 */
function tinsta_render_posts($post_type = '', $display_mode = '')
{

  if (!$post_type) {
    $post_type = get_post_type();
  }

  if (!$display_mode) {
    $display_mode = is_singular() ? 'single' : 'archive';
  }

  if (is_active_sidebar("before-entries-{$post_type}-{$display_mode}")) {
    dynamic_sidebar("before-entries-{$post_type}-{$display_mode}");
  }

  echo "<div class=\"site-entries site-entries-type-{$post_type} site-entries-{$display_mode}\">";

  if (have_posts()) {
    tinsta_render_posts_loop($post_type, $display_mode);
  } else {
    get_template_part('template-parts/misc/no-entries', $post_type);
  }

  echo '</div>';

  if ($display_mode !== 'single') {
    tinsta_pagination('archive');
  }

  if (is_active_sidebar("after-entries-{$post_type}-{$display_mode}")) {
    dynamic_sidebar("after-entries-{$post_type}-{$display_mode}");
  }

  // Append comments for singular modes.
  if ($display_mode == 'single') {
    comments_template('/comments.php', true);
  }

}

/**
 * Render posts from current request (useful when serving posts with AJAX)
 *
 * @param $post_type
 * @param $display_mode
 */
function tinsta_render_posts_loop($post_type, $display_mode)
{

  while (have_posts()) {

    the_post();
    $page_template = get_page_template_slug(get_the_ID());
    $post_post_type = get_post_type();

    if (in_array($page_template, [ 'template-content-only.php', 'template-fullpage.php' ])) {
      $templates = [
        "template-parts/entries/{$post_post_type}-embed.php",
        "template-parts/entries/post-embed.php",
      ];
    } else {
      $templates = [
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
 * Render social network's code
 */
function tinsta_the_social_code()
{
  static $social_code = null;
  if ($social_code === null) {
    $social_code = (string)get_theme_mod('social_networks_code', '');
  }
  if ($social_code) {
    ?>
    <div class="social-networks-code">
      <?php echo $social_code ?>
    </div>
    <?php
  }
}

/**
 * Get review category cover
 *
 * @param int $category_term_id
 * @param string $size
 * @param array $attr
 *
 * @return string
 */
function tinsta_get_category_cover_image($category_term_id = 0, $size = '', $attr = [])
{
  $cover_id = get_term_meta($category_term_id, '_cover_id', true);

  return wp_get_attachment_image($cover_id, $size, false, $attr);
}

/**
 * Get review cover image, if not set, then inherit from category
 *
 * @param NULL $post_id
 * @param string $size
 * @param array $attr
 *
 * @return string
 */
function tinsta_get_cover_image($post_id = null, $size = '', $attr = [])
{

  if ( ! $post_id) {
    $post_id = get_the_ID();
  }

  $cover_id = get_post_meta($post_id, '_cover_id', true);
  if ( ! $cover_id) {
    $terms = wp_get_post_terms($post_id);
    if ($terms) {
      foreach ($terms as $term) {
        $term_cover_id = get_term_meta($term->term_id, '_cover_id', true);
        if ($term_cover_id) {
          break;
        }
      }
    }
  }

  if ($cover_id) {
    return wp_get_attachment_image($cover_id, $size, false, $attr);
  }

  return '';
}

/**
 * Get sidebar variant
 *
 * @return string
 */
function tinsta_get_sidebar_variant()
{

  $variant = '';

  if (is_singular()) {
    $page_template = get_page_template_slug();
    if (in_array($page_template, [ 'template-nosidebars.php', 'template-content-only.php', 'template-thin.php' ])) {
      return $variant;
    }
  }

  if (is_front_page() || is_search() || is_404()) {
    return $variant;
  }

  if (is_search()) {
    return $variant;
  }

  $variant = get_post_type() . (is_singular() ? '' : '-archive');

  return apply_filters('tinsta_get_sidebar_variant', $variant);
}

/**
 * Get related posts
 *
 * @param $related_to_post
 * @param $limit
 * @param array $related_post_types
 *
 * @return \WP_Post[]
 */
function tinsta_get_related_posts($related_to_post, $limit = 5, $related_post_types = [])
{

  $related_to_post = get_post($related_to_post);

  if (!$related_post_types) {
    $related_post_types = [$related_to_post->post_type];
  }

  $tags = wp_get_object_terms($related_to_post->ID, get_object_taxonomies($related_to_post->post_type, 'names'));
  $post__not_in = [$related_to_post->ID];
  $posts = [];

  $tag_ids = [];
  foreach ($tags as $individual_tag) {
    $tag_ids[] = $individual_tag->term_id;
  }

  $args = [
    'tag__in' => $tag_ids, // @TODO make next to work
    'post__not_in' => $post__not_in,
    'post_type' => $related_post_types,
    'posts_per_page' => $limit,
    'numberposts' => $limit,
    'ignore_sticky_posts' => true,
    'orderby' => 'comment_count',
    'order' => 'DESC',
  ];

  foreach (get_posts($args) as $post) {
    $posts[$post->ID] = $post;
    $post__not_in[] = $post->ID;
  }

  if (count($posts) < $limit) {
    $title_tags = explode(' ', get_the_title());
    $title_tags = array_merge($title_tags, explode(' ', get_the_excerpt()));
    $title_tags = array_map('trim', array_filter(array_unique($title_tags)));
    rsort($title_tags, SORT_STRING);
    foreach ($title_tags as $word) {
      if (count($posts) >= $limit) {
        break;
      }
      $args = [
        'post__not_in' => $post__not_in,
        's' => $word,
        'post_type' => $related_post_types,
        'posts_per_page' => $limit,
        'numberposts' => $limit,
        'ignore_sticky_posts' => true,
        'orderby' => 'comment_count',
        'order' => 'DESC',
      ];
      $ss_posts = get_posts($args);
      foreach ($ss_posts as $post) {
        $post__not_in[] = $post->ID;
        $posts[$post->ID] = $post;
        if (count($posts) >= $limit) {
          break;
        }
      }
    }
  }

  return $posts;
}

/**
 * Five-star renderer
 *
 * @param int $rating
 * @param int $max
 *
 * @return bool
 */
function tinsta_the_fivestar($rating = 0, $max = 5)
{
  if (!$rating) {
    return false;
  }
  $rate = round((float)$rating / 0.5) * 0.5; // Because Amazon always round to nearest 0.5
  ?>
  <span class="fivestar" title="<?php echo esc_attr(sprintf(__('Rate: %s/%s', 'tinsta'), $rating, $max)) ?>">
    <span class="fivestar-progress" style="width:<?php echo round($rate / $max * 100) ?>%">
      <?php echo esc_html($rating) ?>
    </span>
  </span>
  <?php
  return true;
}

/**
 * Default theme mod array
 *
 * @return array
 */
function tinsta_options_defaults()
{

  // Add all theme mods.
  return apply_filters('tinsta_options_defaults', [

    /**
     * Misc
     */
    'legacy_support' => true,
    'effects' => false,

    /**
     * Typography
     */
    'font_size' => 14,
    'font_headings_petite' => true,
    'font_family' => 'medium-content-sans-serif-font, -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif',
    'font_family_headings' => '',
    'font_google' => '',
    'font_headings_google' => '',

    /**
     * Post Types defaults
     */
    'post_link_date' => false,
    'post_type_post_use_defaults' => true,
    'post_type_attachment_use_defaults' => true,
    'post_type_page_use_defaults' => true,

    /**
     * Sections
     */

    // Breakpoints
    'breakpoint_desktop' => 1800,
    'breakpoint_tablet' => 1024,
    'breakpoint_mobile' => 640,

    // Section: Globals
    'site_height_full' => true,
    'site_layout_boxed' => false,
    'site_wrapper_width' => 980,
    'site_color_background' => '#ffffff',
    'color_primary' => '#3c7094',
    'color_primary_inverted' => '#ffffff',
    'color_secondary' => '#e55a19',
    'color_secondary_inverted' => '#ffffff',
    'site_body_image' => '',
    'site_body_image_size' => 'auto',
    'site_body_image_repeat' => true,
    'site_body_image_attachment_scroll' => true,
    'site_body_image_position_x' => 'center',
    'site_body_image_position_y' => 'center',

    // Section: Topline
    'topline_color_background' => '#ffffff',
    'topline_color_background_opacity' => 100,
    'topline_color_foreground' => '#222222',

    // Section: Header
    'header_padding' => 20,
    'header_sticky' => false,
    'header_background_wrapper' => true,
    'header_image' => '',
    'header_image_size' => 'auto',
    'header_image_repeat' => true,
    'header_image_attachment_scroll' => true,
    'header_image_position_x' => 'center',
    'header_image_position_y' => 'center',
    'header_color_background' => '#415261',
    'header_color_background_opacity' => 100,
    'header_color_foreground' => '#cdd9e4',

    // Section: Primary Menu
    'primary_menu_color_background' => '#415261',
    'primary_menu_color_background_opacity' => 100,
    'primary_menu_color_foreground' => '#cdd9e4',

    // Section: Main
    'main_color_background' => '#ffffff',
    'main_color_background_opacity' => 100,
    'main_color_foreground' => '#222222',
    'main_image' => '',
    'main_image_size' => 'auto',
    'main_image_repeat' => true,
    'main_image_attachment_scroll' => true,
    'main_image_position_x' => 'center',
    'main_image_position_y' => 'center',

    // Section: Primary Sidebar
    'sidebar_primary_width' => 220,
    'sidebar_primary_color_background' => '',
    'sidebar_primary_color_foreground' => '',

    // Section: Secondary Sidebar
    'sidebar_secondary_width' => 160,
    'sidebar_secondary_color_background' => '',
    'sidebar_secondary_color_foreground' => '',

    // Section: Footer
    'footer_background_highlight_inner' => true,
    'footer_image' => '',
    'footer_image_size' => 'auto',
    'footer_image_repeat' => true,
    'footer_image_attachment_scroll' => true,
    'footer_image_position_x' => 'center',
    'footer_image_position_y' => 'center',
    'footer_color_background' => '#666666',
    'footer_color_background_opacity' => 100,
    'footer_color_foreground' => '#eeeeee',

    // Section: Bottomline
    'bottomline_sticky' => false,
    'bottomline_color_background' => '#535353',
    'bottomline_color_background_opacity' => 100,
    'bottomline_color_foreground' => '#aaaaaa',


    /*
     * Components
     */

    // Component: Topline
    'site_topline' => '',

    // Component: Bottomline
    'site_bottomline' => get_bloginfo('name') . ' - ' . date('Y'),

    // Component: HTML header markup
    'header_markup' => '',

    // Component: HTML footer markup
    'footer_markup' => '',

    // Component: Social Networks Code
    'social_networks_code' => '',

    // Component: Breadcrumbs
    'include_home_in_breadcrumbs' => true,

    // Component: Outdated Posts
    'outdated_post_time' => 0,
    'outdated_post_message' => __('This article is older than %time%, the content might not be relevant anymore.', 'tinsta'),

    // Component: Fivestar
    'fivestar_symbol_empty' => '\f318',
    'fivestar_symbol_full' => '\f318',
    'fivestar_max_value' => 5,

    // Component: site agreement
    'site_agreement_enable' => true,
    'site_agreement_text' => __('I agree with site terms.', 'tinsta'),
    'site_agreement_agree_button' => __('Agree', 'tinsta'),
    'site_agreement_cancel_url' => 'http://google.com/',
    'site_agreement_cancel_title' => __('Cancel', 'tinsta'),

    // Component: Context Header
    'context_header_date_format' => translate_with_gettext_context('F Y', 'textual', 'tinsta'),

    // Component: Login and Register page integrations.
    'login_integration_mode' => 'full',

    // Component: 404
    'theme_404_page' => false,

    // Component: Avatars.
    'avatar_size' => 72,
    'avatar_size_small' => 42,

  ]);
}

/**
 * Generate CSS style for background image
 *
 * @param $args
 *
 * @return string
 */
function tinsta_background_image_styles($args)
{

  $styles = [];

  if (!empty($args['image'])) {

    $styles[] = "background-image:url({$args['image']})";

    if (!empty($args['fixed'])) {
      $styles[] = "background-attachment:fixed";
    }

    if (!empty($args['size'])) {
      $styles[] = "background-size:{$args['size']}";
    }

    if (empty($args['repeat'])) {
      $styles[] = "background-repeat:no-repeat";
    }

    if (!empty($args['x'])) {
      $styles[] = "background-position-x:{$args['x']}";
    }

    if (!empty($args['y'])) {
      $styles[] = "background-position-y:{$args['y']}";
    }

  }

  $styles = implode(';', $styles);

  if (!empty($args['nostyle'])) {
    return $styles;
  }

  return "style=\"{$styles}\"";

}

/**
 * Check if Tinsta theme should add customizer
 *
 * @return bool
 */
function tinsta_is_customizer_enabled()
{
  return apply_filters('tinsta_is_customizer_enabled', true);
}

/**
 * Setup theme's css files and register "tinsta" stylesheet.
 *
 * @param $scss_file
 * @param $args
 *
 * @return string
 */
function tinsta_setup_stylesheets($scss_file, $args = [])
{

  $args = wp_parse_args($args, [
    'force_rebuild' => false,
    'clear' => false,
  ]);

  $is_customize_change = false;

  if (is_customize_preview()) {
    global $wp_customize;
    // @TODO optimize.
    $customizer_patched_data_values = $wp_customize->unsanitized_post_values();
    if ($wp_customize->validate_setting_values($customizer_patched_data_values)) {
      $customizer_patched_data_values = array_intersect_key($customizer_patched_data_values, tinsta_options_defaults());
      $is_customize_change = count($customizer_patched_data_values) > 0;
    }
  }

  $compiled_css_file = '/cache/' . get_stylesheet() . '-' . $scss_file . ( empty($args['clear']) && $is_customize_change ? '.customized' : '') . '.css';
  $compiled_css_path = WP_CONTENT_DIR . $compiled_css_file;
  $compiled_css_dirname = dirname($compiled_css_path);
  $compiled_css_uri = content_url($compiled_css_file);

  // Check source SCSS file.
  $source_file = get_template_directory() . '/assets/scss/' . $scss_file . '.scss';

  // Just clearing old cached file.
  if (!empty($args['clear'])) {
    if (file_exists($compiled_css_path)) {
      call_user_func('unlink', $compiled_css_path);
    }
    return $compiled_css_uri;
  }

  // print_r(apply_filters('tinsta_scss_variables', []));exit;

  // Rebuild the cache if:
  if (

    // Force rebuild
    !empty($args['force_rebuild'])

    // When in customizer.
    || $is_customize_change

    // Or WordPress has WP_DEBUG set to TRUE and the user's browser send no-cache header.
    || (defined('WP_DEBUG') && WP_DEBUG && (!empty($_SERVER['HTTP_CACHE_CONTROL']) && stripos($_SERVER['HTTP_CACHE_CONTROL'], 'no-cache') !== false))

    // If cache file is does not exists.
    || !file_exists($compiled_css_path)

    // @TODO remove this after public release.
    || ( !empty($_GET['tinsta-rebuild-styles']) && current_user_can('edit_theme_options') )

    // Or the theme mod is set.
    // || get_theme_mod('_require_stylesheet_rebuild')

  ) {
    try {

      if (!is_readable($source_file)) {
        throw new Exception("Source file for {$scss_file} is missing.");
      }

      $import_paths = [
        get_template_directory() . '/assets/scss',
      ];

      if (is_child_theme()) {
        $import_paths[] = get_stylesheet_directory() . '/assets/scss';
      }

      // Prepare variables.
      $variables = apply_filters('tinsta_scss_variables', []);

      if ($is_customize_change && !empty($customizer_patched_data_values)) {
        $variables = array_replace_recursive($variables, $customizer_patched_data_values);
      }

      $variables['enable_fontawesome'] = apply_filters('tinsta_enable_fontawesome', true);
      $variables['tinsta_theme_dir_url'] = '"' . get_template_directory_uri() . '"';
      $variables['stylesheet_directory_uri'] = '"' . get_stylesheet_directory_uri() . '"';

      // wp_die(print_r($variables, true));

      $compiler = new \Leafo\ScssPhp\Compiler();

      if (!defined('WP_DEBUG') || !WP_DEBUG) {
        $compiler->setFormatter('Leafo\ScssPhp\Formatter\Compressed');
        $compiler->setIgnoreErrors(true);
      }

      $compiler->setImportPaths($import_paths);
      $compiler->setVariables($variables);

      // Using filter here is not the most elegant solution but is useful from times to times.
      $output = $compiler->compile(file_get_contents($source_file) . apply_filters('tinsta_custom_scss', ''));

      if (!is_dir($compiled_css_dirname)) {
        if (!wp_mkdir_p($compiled_css_dirname)) {
          throw new \Exception("Cannot prepare path \"{$compiled_css_dirname}\" to store stylesheets.");
        }
      }

      // Length of the hash + comment = 2 + 32 + 2 = 36
      $build_hash = md5(serialize(get_theme_mods()));
      $output = $output . "\n/*{$build_hash}*/";

      if (file_put_contents($compiled_css_path, $output) === false) {
        throw new \Exception('Cannot prepare theme\'s stylesheet.');
      }

    } catch (\Exception $e) {
      if (defined('WP_DEBUG') && WP_DEBUG) {
        wp_die('scssphp: ' . $e->getMessage());
      }
      else {
        syslog(LOG_ERR, 'scssphp: ' . $e->getMessage());
      }
    }

    // Unset the _require_stylesheet_rebuild to avoid the loop.
    // if (get_theme_mod('_require_stylesheet_rebuild')) {
    //   remove_theme_mod('_require_stylesheet_rebuild');
    // }

  }

  if (!file_exists($compiled_css_path)) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
      wp_die(__('Unexpected error. Missing stylesheet.', 'tinsta'));
    }
  }

  return $compiled_css_uri;
}
