<?php

class Tinsta_RelatedPosts_Widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('Related posts', 'tinsta')));
  }

  function form($instance)
  {
    $instance = wp_parse_args($instance, array('title' => ''));
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title')?>">
        <?php _e('Title:', 'tinsta')?>
      </label>
      <input id="<?php echo $this->get_field_id('title')?>" name="<?php echo $this->get_field_name('title')?>" type="text" value="<?php echo esc_attr($instance['title'])?>" />
    </p>
    <?php
  }

  function widget($args, $instance)
  {

    if (!is_single()) {
      return;
    }

    $related_posts = tinsta_get_related_posts(get_the_ID());

    if ($related_posts) {
      echo $args['before_widget'];
      $instance['title'] = apply_filters('widget_title', (empty($instance['title']) ? '' : $instance['title']));
      if ($instance['title']) {
        echo $args['before_title'] . $instance['title'] . $args['after_title'];
      }
      echo '<ul>';
      foreach ($related_posts as $post):?>
        <li>
          <a href="<?php echo get_permalink($post->ID)?>">
            <?php echo $post->post_title?>
          </a>
        </li>
      <?php endforeach;
      echo '</ul>';
      echo $args['after_widget'];
    }
  }
}
