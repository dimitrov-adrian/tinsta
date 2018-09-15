<?php

class Tinsta_Related_Posts_Widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('Related Posts', 'tinsta')), [
        'description' => __('Simple similar posts widget.', 'tinsta'),
      ]);
  }

  function form($instance)
  {
    $instance = wp_parse_args($instance, ['title' => '']);
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title') ?>">
        <?php _e('Title', 'tinsta') ?>
      </label>
      <input id="<?php echo $this->get_field_id('title') ?>" name="<?php echo $this->get_field_name('title') ?>"
             type="text"
             value="<?php echo esc_attr($instance['title']) ?>" />
    </p>
    <?php
  }

  function widget($args, $instance)
  {

    if (!is_single()) {
      return;
    }

    $related_posts = $this->getRelatedPosts(get_the_ID());

    if ($related_posts) {
      echo $args['before_widget'];
      $instance['title'] = apply_filters('widget_title', (!empty($instance['title']) ? $instance['title'] : ''));
      if ($instance['title']) {
        echo $args['before_title'] . $instance['title'] . $args['after_title'];
      }
      echo '<ul>';
      foreach ($related_posts as $post):?>
        <li>
          <a href="<?php echo get_permalink($post->ID) ?>">
            <?php echo $post->post_title ?>
          </a>
        </li>
      <?php endforeach;
      echo '</ul>';
      echo $args['after_widget'];
    }
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
  function getRelatedPosts($related_to_post, $limit = 5, $related_post_types = [])
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
}
