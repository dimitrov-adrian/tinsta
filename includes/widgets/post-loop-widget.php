<?php

class Tinsta_Post_Loop_Widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('Post List', 'tinsta')), [
      'description' => __('Simple post list loop.', 'tinsta'),
    ]);
  }

  function form($instance)
  {
    $instance = wp_parse_args($instance, ['title' => '', 'post_type' => 'post', 'layout' => '', 'limit' => 5 ]);

    $layouts = apply_filters('tinsta_post_type_layouts_single', [
      '' => __('Default', 'tinsta'),
      'left-thumbnail' => __('Left Thumbnail', 'tinsta'),
      'right-thumbnail' => __('Right Thumbnail', 'tinsta'),
      'contextual-header' => __('Contextual Header', 'tinsta'),
      'catalog-item' => __('Catalog Item', 'tinsta'),
      'widgets-area' => __('Widgets Area', 'tinsta'),
    ], '');

    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title') ?>">
        <?php _e('Title', 'tinsta') ?>
      </label>
      <input id="<?php echo $this->get_field_id('title') ?>" name="<?php echo $this->get_field_name('title') ?>"
             type="text"
             value="<?php echo esc_attr($instance['title']) ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('post_type') ?>">
        <?php _e('Type', 'tinsta') ?>
      </label>
      <select id="<?php echo $this->get_field_id('post_type') ?>" name="<?php echo $this->get_field_name('post_type') ?>">
        <?php foreach (get_post_types(['public' => true ], 'objects') as $post_type => $post_type_info):?>
          <option value="<?php echo esc_attr($post_type)?>">
          <?php echo esc_html($post_type_info->label)?>
          </option>
        <?php endforeach?>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('layout') ?>">
        <?php _e('Layout', 'tinsta') ?>
      </label>
      <select id="<?php echo $this->get_field_id('layout') ?>" name="<?php echo $this->get_field_name('layout') ?>">
        <?php foreach ($layouts as $layout => $layout_title):?>
        <option value="<?php echo esc_attr($layout)?>">
          <?php echo esc_html($layout_title)?>
        </option>
        <?php endforeach?>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('limit') ?>">
        <?php _e('Limit', 'tinsta') ?>
      </label>
      <input id="<?php echo $this->get_field_id('limit') ?>" name="<?php echo $this->get_field_name('limit') ?>"
             type="number"
             min="1"
             max="100"
             step="1"
             value="<?php echo esc_attr($instance['limit']) ?>" />
    </p>

    <?php
  }

  function widget($args, $instance)
  {

    $instance = wp_parse_args($instance, ['title' => '', 'post_type' => 'post', 'layout' => '', 'limit' => 5 ]);

    if (!$instance['post_type']) {
      return;
    }

    $query = new WP_Query([
      'post_type' => $instance['post_type'],
      'orderby' => 'title',
      'posts_per_page' => $instance['limit'],
    ]);

    if ($query->have_posts()) {

      echo $args['before_widget'];
      $title = apply_filters('widget_title', $instance['title']);
      if ($title) {
        echo $args['before_title'] . $title . $args['after_title'];
      }

      global $post;
      $old_post = $post;
      while ($query->have_posts()) {
        $query->the_post();
        tinsta_render_posts_loop_post('archive', $instance['post_type']);
        wp_reset_postdata();
      }
      $post = $old_post;
      echo $args['after_widget'];
    }

  }
}
