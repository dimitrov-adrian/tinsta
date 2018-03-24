<?php

/**
 * Class Tinsta_Logo_Widget
 */
class Tinsta_Logo_Widget extends WP_Widget
{

  public function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('Logo', 'tinsta')));
  }

  public function widget($args, $instance)
  {
    echo str_replace('class=', 'role="logo" class=', $args['before_widget']);

    if (get_theme_mod('custom_logo') && get_custom_logo()) {
      the_custom_logo();
    }
    else {
      ?>
        <a class="custom-logo-link noimage" href="<?php echo esc_url(home_url()) ?>"
           title="<?php _e('Front Page', 'tinsta') ?>" rel="home">
          <?php bloginfo('blogname') ?>
        </a>
      <?php
    }

    if (get_theme_mod('header_textcolor') !== 'blank') {
      ?>
      <div class="logo-site-description">
        <?php bloginfo('description') ?>
      </div>
      <?php
    }

    echo $args['after_widget'];
  }

}
