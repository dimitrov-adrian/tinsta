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

  function form($instance)
  {

    $instance = wp_parse_args($instance, [
      'style' => '',
      'tagline' => '',
      'width' => '',
      'height' => '',
    ]);

    ?>
    <p>
      <?php _e('The logo can be changed from Apperance -&gt; Customizer', 'tinsta')?>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('style') ?>">
        <?php _e('Style', 'tinsta') ?>
      </label>
      <select id="<?php echo $this->get_field_id('style') ?>" name="<?php echo $this->get_field_name('style') ?>">
        <option value=""><?php _e('None', 'tinsta')?></option>
        <option value="colored" <?php selected('colored', $instance['style'])?>><?php _e('Colored', 'tinsta')?></option>
        <option value="colored-inverted" <?php selected('colored-inverted', $instance['style'])?>><?php _e('Inverted Color', 'tinsta')?></option>
      </select>
    </p>
    <p>
      <input type="checkbox"
             id="<?php echo $this->get_field_id('tagline') ?>"
             name="<?php echo $this->get_field_name('tagline') ?>"
             value="on"
             <?php checked('on', $instance['tagline'])?>
            />
      <label for="<?php echo $this->get_field_id('tagline') ?>">
        <?php _e('Show Tagline', 'tinsta') ?>
      </label>
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('width') ?>">
        <?php _e('Width', 'tinsta') ?>
      </label>
      <input type="number"
             id="<?php echo $this->get_field_id('width') ?>"
             name="<?php echo $this->get_field_name('width') ?>"
             value="<?php esc_html($instance['width'])?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('height') ?>">
        <?php _e('Width', 'tinsta') ?>
      </label>
      <input type="number"
             id="<?php echo $this->get_field_id('height') ?>"
             name="<?php echo $this->get_field_name('height') ?>"
             value="<?php esc_html($instance['height'])?>" />
    </p>
    <?php

  }

  public function widget($args, $instance)
  {
    $instance = wp_parse_args($instance, [
      'style' => '',
      'tagline' => '',
      'width' => '',
      'height' => '',
    ]);
    $class = $instance['style'];
    $style = '';
    if ($instance['width'] || $instance['height']) {
      $style = ' style="';
      if ($instance['width']) {
        $style .= 'width:' . esc_attr($instance['width']) . 'px';
      }
      if ($instance['height']) {
        $style .= 'height:' . esc_attr($instance['height']) . 'px';
      }
      $style .= '" ';
    }
    echo str_replace('class="', 'aria-roledescription="logo" ' . $style . ' class="' . $class . ' ', $args['before_widget']);

    if (get_theme_mod('custom_logo') && get_custom_logo()) {
      the_custom_logo();
    } else {
      ?>
      <a class="custom-logo-link noimage" href="<?php echo esc_url(home_url()) ?>"
         title="<?php _e('Front Page', 'tinsta') ?>" rel="home">
        <?php bloginfo('blogname') ?>
      </a>
      <?php
    }

    if ( $instance['tagline'] == 'on' && get_theme_mod('header_textcolor') !== 'blank') {
      ?>
      <div class="logo-site-description">
        <?php bloginfo('description') ?>
      </div>
      <?php
    }

    echo $args['after_widget'];
  }

}
