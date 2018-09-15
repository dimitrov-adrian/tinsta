<?php

class Tinsta_Page_Content_Widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('Page Content', 'tinsta')), [
      'description' => __('Display custom page\'s content as widget.', 'tinsta'),
    ]);
  }

  function form($instance)
  {
    $instance = wp_parse_args($instance, ['show_title' => '', 'page_id' => 0]);
    ?>
    <p>
      <input type="checkbox"
             id="<?php echo $this->get_field_id('show_title') ?>"
             name="<?php echo $this->get_field_name('show_title') ?>"
             value="on"
        <?php checked('on', $instance['show_title']) ?>
      />
      <label for="<?php echo $this->get_field_id('show_title') ?>">
        <?php _e('Title', 'tinsta') ?>
      </label>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('page_id') ?>">
        <?php _e('Page', 'tinsta') ?>
      </label>
      <?php
      wp_dropdown_pages([
        'selected' => $instance['page_id'],
        'name' => $this->get_field_name('page_id'),
        'id' => $this->get_field_id('page_id'),
        'show_option_none' => false,
        'value_field' => 'ID',
      ]);
      ?>
    </p>
    <?php
  }

  function widget($args, $instance)
  {

    $instance = wp_parse_args($instance, ['show_title' => '', 'page_id' => 0]);

    if ($instance['page_id']) {
      $page = get_post($instance['page_id']);
      if (!$page) {
        return;
      }
    }

    setup_postdata($page);
    echo $args['before_widget'];

    if ($instance['show_title']) {
      $title = apply_filters('widget_title', get_the_title());
      if ($title) {
        echo $args['before_title'] . $title . $args['after_title'];
      }
    }

    the_content();

    echo $args['after_widget'];

    wp_reset_postdata();
  }
}
