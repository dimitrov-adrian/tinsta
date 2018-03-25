<?php


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


add_filter('embed_oembed_html', function ($html, $url, $attr) {

  // Wrap embed videos within a media-container div element.
  if ( preg_match('#(\<video)#i', $html) ) {
    $html = '<div class="media-container">' . $html . '</div>';
  }

  return $html;

}, 10, 3);


add_filter('the_content', function ($content) {

  global $post;

  $outdated_post_time = get_theme_mod('outdated_post_time', 0);
  if ($outdated_post_time && (int)get_the_time('U') + ((int)$outdated_post_time * 60 * 60 * 24) < time()) {
    $outdated_post_message = get_theme_mod('outdated_post_message');
    $outdated_post_message = substr_replace('%time%', human_time_diff(get_the_time('U')), $outdated_post_message);
    $content               .= '
      <div class="message warning">
        ' . $outdated_post_message . '
      </div>';
  }

  if (get_theme_mod("post_type_{$post->name}_append_authors")) {
    ob_start();
    locate_template('template-parts/misc/post-authors.php', true, false);
    $content .= ob_get_clean();
  }

  if (!is_admin_bar_showing()) {
    // When use edit_post_link() no need to add one more translation.
    ob_start();
    edit_post_link(null, '<p>', '</p>' );
    $content = ob_get_clean() . $content;
  }

  return $content;

}, 10, 2);

// Allow widgets to be included
if (!shortcode_exists('widget')) {
  add_shortcode('widget', function ($atts) {
    if (class_exists($atts['type'], false) && is_subclass_of($atts['type'], 'WP_Widget')) {
      ob_start();
      the_widget($atts['type'], $atts);

      return ob_get_clean();
    }
    return ' <!-- Inactive Widget --> ';
  });
}

add_action('init', function () {

  $sidebar_variants = [];

  foreach (get_post_types(['public' => true], 'objects') as $post_type) {

    $sidebar_variants[$post_type->name . '-single'] = $post_type->label;

    if ( ! ($post_type->has_archive === 0 || $post_type->has_archive === false) || $post_type->name == 'post') {
      $sidebar_variants[$post_type->name . '-archive'] = sprintf(__('%s Archive', 'tinsta'), $post_type->label);
    }

  }

  foreach ($sidebar_variants as $variant_slug => $variant_label) {

    register_sidebar([
      'name'          => $variant_label . ' (' . __('Before Entries', 'tinsta') . ')',
      'id'            => 'before-entries-' . $variant_slug,
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<div class="widgettitle">',
      'after_title'   => '</div>',
    ]);

    register_sidebar([
      'name'          => $variant_label . ' (' . __('After Entries', 'tinsta') . ')',
      'id'            => 'after-entries-' . $variant_slug,
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<div class="widgettitle">',
      'after_title'   => '</div>',
    ]);

    register_sidebar([
      'name'          => $variant_label . ' (' . __('Primary Sidebar', 'tinsta') . ')',
      'id'            => 'sidebar-' . $variant_slug,
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<div class="widgettitle">',
      'after_title'   => '</div>',
    ]);

    register_sidebar([
      'name'          => $variant_label . ' (' . __('Secondary Sidebar', 'tinsta') . ')',
      'id'            => 'sidebar-' . $variant_slug . '-secondary',
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<div class="widgettitle">',
      'after_title'   => '</div>',
    ]);
  }

  register_sidebar([
    'name'          => __('Footer', 'tinsta'),
    'id'            => 'footer',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<div class="widgettitle">',
    'after_title'   => '</div>',
  ]);

}, 20);


add_action('customize_register', function ($wp_customize) {
  /*** @var $wp_customize WP_Customize_Manager */

  if (!tinsta_is_customizer_enabled()) {
    return;
  }

  foreach (get_post_types(['public' => true], 'objects') as $post_type) {

    if (!$wp_customize->get_setting("post_type_{$post_type->name}")) {
      $wp_customize->add_section("post_type_{$post_type->name}", [
        'title' => sprintf(__('Type: %s', 'tinsta'), $post_type->label),
        'panel' => 'tinsta_page_types',
      ]);
    }

    $wp_customize->add_setting("post_type_{$post_type->name}_use_defaults");
    $wp_customize->add_control("post_type_{$post_type->name}_use_defaults", [
      'label'   => __('Use default views', 'tinsta'),
      'section' => "post_type_{$post_type->name}",
      'type'    => 'checkbox',
    ]);

    if ( ! ($post_type->has_archive === 0 || $post_type->has_archive === false) || $post_type->name == 'post') {
      $wp_customize->add_setting("post_type_{$post_type->name}_layout_archive");
      $wp_customize->add_control("post_type_{$post_type->name}_layout_archive", [
        'label'   => __('Archive Layout', 'tinsta'),
        'section' => "post_type_{$post_type->name}",
        'type'    => 'select',
        // Mode @see &.site-entries-archive { ... } from _entries.scss
        // left-thumbnail
        // right-thumbnail
        // boxes
        // timeline
        // poetry
        'choices' => [
          ''                => __('Default', 'tinsta'),
          'boxes'           => __('Boxes', 'tinsta'),
          'cover-boxes'     => __('Cover Boxes', 'tinsta'),
          'timeline'        => __('Timeline', 'tinsta'),
          'poetry'          => __('Poetry', 'tinsta'),
          'left-thumbnail'  => __('Left Thumbnail', 'tinsta'),
          'right-thumbnail' => __('Right Thumbnail', 'tinsta'),
        ],
      ]);
    }

    $wp_customize->add_setting("post_type_{$post_type->name}_layout");
    $wp_customize->add_control("post_type_{$post_type->name}_layout", [
      'label'   => __('Singular Layout', 'tinsta'),
      'section' => "post_type_{$post_type->name}",
      'type'    => 'select',
      // Mode @see &.site-entries-singular { ... } from _entries.scss
      // left-thumbnail
      'choices' => [
        ''                  => __('Default', 'tinsta'),
        'left-thumbnail'    => __('Left Thumbnail', 'tinsta'),
        'right-thumbnail'   => __('Right Thumbnail', 'tinsta'),
        'contextual-header' => __('Contextual Header', 'tinsta'),
      ],
    ]);

    $wp_customize->add_setting("post_type_{$post_type->name}_append_authors");
    $wp_customize->add_control("post_type_{$post_type->name}_append_authors", [
      'label'   => __('Append Authors Bio at end of content', 'tinsta'),
      'section' => "post_type_{$post_type->name}",
      'type'    => 'checkbox',
    ]);

    if (is_post_type_hierarchical($post_type->name)) {
      $wp_customize->add_setting("post_type_{$post_type->name}_append_post_nav");
      $wp_customize->add_control("post_type_{$post_type->name}_append_post_nav", [
        'label'   => __('Append post navigation', 'tinsta'),
        'section' => "post_type_{$post_type->name}",
        'type'    => 'checkbox',
      ]);
    }

  }

});
