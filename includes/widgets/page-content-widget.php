<?php

/**
 * Class Tinsta_PageContent_Widget
 */
class Tinsta_PageContent_Widget extends WP_Widget
{

  public function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('Page Content', 'tinsta')));
  }

  function form($instance)
  {
    $instance = wp_parse_args($instance, [ 'page_id' => '' ]);

    ?>
    <p>
      <label for="<?php echo $this->get_field_id('page_id') ?>">
        <?php _e('Page', 'tinsta') ?>
      </label>
      <?php
        wp_dropdown_pages([
          'post_type'        => 'page',
          'exclude_tree'     => NULL,
          'selected'         => $instance['page_id'],
          'name'             => $this->get_field_name('page_id'),
          'id'               => $this->get_field_id('page_id'),
          'echo'             => true,
        ]);
      ?>
    </p>
    <?php

  }

  function widget($args, $instance)
  {
    // @TODO add recursion fail check.

    $instance = wp_parse_args($instance, [ 'page_id' => '' ]);

    if ( !empty($instance['page_id'])) {
      $post = get_post($instance['page_id'], OBJECT, 'display');
      if ($post) {
        echo $args['before_widget'];
        echo do_shortcode($post->post_content);
        echo $args['after_widget'];
      }
    }

  }

}