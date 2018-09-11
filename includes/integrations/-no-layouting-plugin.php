<?php

/**
 * @file
 * Simple post "aranging" mechanism, allowing to override the post content with sidebar
 */


/**
 * Register the sidebars.
 */
add_action('widgets_init', function () {

  $post_widgets = get_posts([
    'post_type' => 'any',
    'meta_query' => [
      [
        'key' => '_tinsta_post_append_widgets',
      ],
    ],
    'orderby' => 'title',
    'posts_per_page' => -1,
  ]);

  foreach ($post_widgets as $post_widget) {
    register_sidebar([
      'name' => sprintf(__('Post "%s"', 'tinsta'), $post_widget->post_title),
      'description' => sprintf(__('Replaces the "%s" content.', 'tinsta'), $post_widget->post_title),
      'id' => 'tinsta-post-' . $post_widget->ID,
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widgettitle">',
      'after_title' => '</div>',
    ]);
  }

});

if (is_admin()) {

  /**
   * Add metabox for posts
   */
  add_action('add_meta_boxes', function () {
    add_meta_box('tinsta-page-attributes', __('Theme Features', 'tinsta'), 'tinsta_page_attributes_render', 'page',
      'side', 'default');
  });

  /**
   * The metabox renderer
   *
   * @param $post
   */
  function tinsta_page_attributes_render($post)
  {

    $has_tinsta_post_append_widgets = (bool)get_post_meta($post->ID, '_tinsta_post_append_widgets', true);

    ?>
    <p class="help tinsta-notice">
      <?php _e('Next options come from Tinsta theme.', 'tinsta') ?>
    </p>
    <p>
      <label>
        <input type="checkbox" name="_tinsta_post_append_widgets" value="on"
          <?php checked(true, $has_tinsta_post_append_widgets) ?>
        />
        <?php _e('Create custom widgets region', 'tinsta') ?>

        <?php
        if (!is_customize_preview() && $has_tinsta_post_append_widgets && current_user_can('customize')) {
          $url = add_query_arg([
            'autofocus' => [
              'panel' => 'widgets',
              'section' => urlencode('sidebar-widgets-tinsta-post-' . $post->ID),
            ],
            'url' => urlencode(get_permalink($post->ID)),
            'return' => urlencode(remove_query_arg(wp_removable_query_args(), wp_unslash($_SERVER['REQUEST_URI']))),
          ], admin_url('customize.php'));
          printf('(<a class="hide-if-no-customize" href="%1$s">%2$s</a>)', esc_url($url), __('Edit Widgets', 'tinsta'));
        }
        ?>

      </label>
    </p>
    <?php
  }

  /**
   * Save tinsta's post related to post.
   */
  add_action('save_post', function ($post_id) {

    // Skip if:
    if (wp_is_post_revision($post_id) || defined('DOING_AJAX')) {
      return;
    }

    if (!empty($_POST['_tinsta_post_append_widgets'])) {
      update_post_meta($post_id, '_tinsta_post_append_widgets', 'on');
    } elseif (get_post_meta($post_id, '_tinsta_post_append_widgets',
        true) && empty($_POST['_tinsta_post_append_widgets'])) {
      delete_post_meta($post_id, '_tinsta_post_append_widgets');
    }

  });

} else {

  /**
   * Alter post contents
   */
  add_action('wp', function () {
    if (is_singular()) {

      add_action('the_post', function ($post) {
        global $__tinsta_nolayouting_setup_postdata_is_fresh;
        $__tinsta_nolayouting_setup_postdata_is_fresh = true;
      });

      // Replace the post content with widget area.
      add_filter('the_content', function ($content) {
        global $__tinsta_nolayouting_setup_postdata_is_fresh;
        if ($__tinsta_nolayouting_setup_postdata_is_fresh === true) {
          $__tinsta_nolayouting_setup_postdata_is_fresh = false;
          $post_id = get_the_ID();
          if (get_post_meta($post_id, '_tinsta_post_append_widgets', true)) {
            ob_start();
            dynamic_sidebar('tinsta-post-' . $post_id);
            $content = ob_get_clean();
          }
        }

        return $content;
      }, 5);

    }
  });

}