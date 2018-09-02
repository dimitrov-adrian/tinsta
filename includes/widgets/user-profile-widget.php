<?php

/**
 * Deprecated
 */

class Tinsta_UserProfile_Widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('User Profile', 'tinsta')));
  }

  function form($instance)
  {

    $instance = wp_parse_args($instance, [
      'user_style'     => 'avatarname',
    ]);

    ?>
    <p>
      <label for="<?php echo $this->get_field_id('user_style') ?>">
        <?php _e('Style', 'tinsta') ?>
      </label>
      <select id="<?php echo $this->get_field_id('user_style') ?>" name="<?php echo $this->get_field_name('user_style') ?>">
        <option <?php selected('', $instance['user_style']) ?> value="">
          &mdash; <?php _e('Select', 'tinsta') ?> &mdash;
        </option>
        <option <?php selected('icon', $instance['user_style']) ?> value="icon">
          <?php _e('Icon', 'tinsta') ?>
        </option>
        <option <?php selected('iconname', $instance['user_style']) ?> value="iconname">
          <?php _e('Icon & Name', 'tinsta') ?>
        </option>
        <option <?php selected('avatar', $instance['user_style']) ?> value="avatar">
          <?php _e('Avatar', 'tinsta') ?>
        </option>
        <option <?php selected('avatarname', $instance['user_style']) ?> value="avatarname">
          <?php _e('Avatar & Name', 'tinsta') ?>
        </option>
      </select>
    </p>
    <?php
  }

  function widget($args, $instance)
  {

    if (!is_user_logged_in()) {
      return;
    }

    $instance = wp_parse_args($instance, [
      'user_style'     => 'avatarname',
    ]);

    echo $args['before_widget'];


    if ($instance['user_style']) {
      echo '<div class="user-info style-' . $instance['user_style'] . '">';

      if ($instance['user_style'] == 'avatar' || $instance['user_style'] == 'avatarname') {
        echo get_avatar(wp_get_current_user(), get_theme_mod('component_avatar_size'));
      }

      if ($instance['user_style'] == 'avatarname' || $instance['user_style'] == 'iconname') {
        echo wp_get_current_user()->display_name;
      }

      echo '</div>';
    }

    echo $args['after_widget'];
  }
}
