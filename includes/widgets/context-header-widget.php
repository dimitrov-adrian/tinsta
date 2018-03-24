<?php

/**
 * This widget is in still in prototype and will be considered in future.
 */
class Tinsta_ContextHeader_Widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('Context Header', 'tinsta')));
  }

  function form($instance)
  {
    $instance = wp_parse_args($instance,  [ 'title' => '', 'image' => '', 'secondary' => '' ]);

    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title') ?>">
        <?php _e('Title:', 'tinsta') ?>
      </label>
      <input id="<?php echo $this->get_field_id('title') ?>" name="<?php echo $this->get_field_name('title') ?>" type="text"
             value="<?php echo esc_attr($instance['title']) ?>"/>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('image') ?>">
        <?php _e('Image:', 'tinsta') ?> URL
      </label>
      <input id="<?php echo $this->get_field_id('image') ?>" name="<?php echo $this->get_field_name('image') ?>" type="url"
             value="<?php echo esc_attr($instance['image']) ?>"/>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('secondary') ?>">
        <?php _e('Secondary Content (HTML)', 'tinsta') ?>
      </label>
      <br />
      <textarea id="<?php echo $this->get_field_id('title') ?>" name="<?php echo $this->get_field_name('title') ?>"><?php
          echo empty($instance['secondary']) ? '' : esc_html($instance['secondary']);
        ?></textarea>
    </p>
    <?php

  }

  function widget($args, $instance)
  {
    ?>
    <div class="context-header">
      <?php if (empty($instance['image'])):?>
        <?php the_post_thumbnail('tinsta_cover')?>
      <?php else:?>
        <?php echo tinsta_get_category_cover_image(get_queried_object_id()) ?>
      <?php endif?>
      <?php tinsta_the_breadcrumbs() ?>
      <h1 class="title">
        <?php echo empty($instance['title']) ? wp_title('', false) : $instance['title'] ?>
      </h1>
      <?php if (!empty($instance['secondary'])):?>
      <div class="secondary">
        <?php $instance['secondary']?>
      </div>
      <?php endif?>
    </div>
    <?php
  }
}
