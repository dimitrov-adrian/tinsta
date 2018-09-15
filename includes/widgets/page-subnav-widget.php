<?php

class Tinsta_Page_Subnav_Widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('Page Navigation', 'tinsta')), [
        'description' => __('Display page\'s siblings and childrens.', 'tinsta'),
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

    $instance = wp_parse_args($instance, ['title' => '']);

    if (!is_page()) {
      return false;
    }

    $post = get_post(get_the_ID());
    $return_state = false;

    ob_start();

    $ancestors = get_post_ancestors($post->ID);
    $root = array_pop($ancestors);
    $current = ($post->post_parent && $post->post_parent !== $root) ? $post->post_parent : $post->ID;

    $query_args = [
      'child_of' => $current,
      'depth' => 1,
      'title_li' => '',
      'echo' => 0,
    ];

    $tmp = wp_list_pages($query_args);

    if ($tmp) {
      $tmp = strtr($tmp, [
        'current_page_item' => 'current_page_item current-menu-item',
      ]);
      echo "<div class=\"base-title\">" . get_the_title($current) . "</div>";
      echo "<ul class=\"menu\">{$tmp}</ul>";
      $return_state = true;
    }

    if ($root && $root !== $current) {
      if (!empty($tmp)) {
        echo "<p>&nbsp;</p>";
      }
      $query_args = [
        'child_of' => $root,
        'depth' => 1,
        'title_li' => '',
        'echo' => 0,
      ];
      $tmp = wp_list_pages($query_args);
      if ($tmp) {
        $tmp = strtr($tmp, [
          'current_page_ancestor' => 'current_page_ancestor current-menu-ancestor',
          'current_page_parent' => 'current_page_parent current-menu-parent',
        ]);
        echo "<div class=\"base-title\">" . get_the_title($root) . "</div>";
        echo "<ul class=\"menu\">{$tmp}</ul>";
        $return_state = true;
      }
    }

    $widget_content = ob_get_clean();

    if ($return_state) {
      echo $args['before_widget'];
      $instance['title'] = apply_filters('widget_title', $instance['title']);
      if ($instance['title']) {
        echo $args['before_title'] . $instance['title'] . $args['after_title'];
      }
      echo $widget_content;
      echo $args['after_widget'];
    }

  }
}
