<?php

/**
 * This widget is in still in prototype and will be considered in future.
 */
class Tinsta_Context_Header_Widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('Context Header', 'tinsta')), [
      'description' => __('Context Header Widget is related to object of current page (post, category, tag, ...). Displays big image and some short information.',
        'tinsta'),
    ]);
  }

  function form($instance)
  {
    $instance = wp_parse_args($instance, ['title' => '', 'image' => '', 'secondary' => '']);

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
      <label for="<?php echo $this->get_field_id('image') ?>">
        <?php _e('Image', 'tinsta') ?> URL
      </label>
      <input id="<?php echo $this->get_field_id('image') ?>"
             name="<?php echo $this->get_field_name('image') ?>"
             type="url"
             class="tinsta-media-picker"
             value="<?php echo esc_attr($instance['image']) ?>" />
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

    <script>

    </script>

    <?php

  }

  function widget($args, $instance)
  {
    $title = trim(!empty($instance['title']) ? $instance['title'] : wp_title('', false));

    if (!$title) {
      return;
    }

    if (is_singular() && get_theme_mod('post_type_' . get_post_type() . '_layout') == 'contextual-header') {
      return;
    }

    echo $args['before_widget'];
    ?>
    <div class="context-header">
      <?php if (empty($instance['image'])): ?>
        <?php the_post_thumbnail('tinsta_cover') ?>
      <?php else: ?>
        <?php echo tinsta_get_category_cover_image() ?>
      <?php endif ?>
      <?php
        // tinsta_the_breadcrumbs()
      ?>
      <h1 class="title">
        <?php echo $title ?>
      </h1>
      <?php if (!empty($instance['secondary'])): ?>
        <div class="secondary">
          <?php $instance['secondary'] ?>
        </div>
      <?php endif ?>
    </div>
    <?php
    echo $args['after_widget'];
  }
}
