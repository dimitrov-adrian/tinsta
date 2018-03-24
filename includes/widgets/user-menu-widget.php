<?php

class Tinsta_UserMenu_Widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('User Profile', 'tinsta')));
  }

  function form($instance)
  {

    $instance = wp_parse_args($instance, [
      'title' => '',
      'user_style' => 'avatarname',
      'loggedin_menu' => '',
      'loggedout_menu' => '',
    ]);

    $menus = wp_get_nav_menus();

    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title')?>">
        <?php _e('Title:', 'tinsta')?>
      </label>
      <input id="<?php echo $this->get_field_id('title')?>" name="<?php echo $this->get_field_name('title')?>" type="text" value="<?php echo esc_attr($instance['title'])?>" />
    </p>
    
    <p>
      <label for="<?php echo $this->get_field_id('user_style')?>"><?php _e('Style', 'tinsta')?></label>
      <select id="<?php echo $this->get_field_id('user_style')?>" name="<?php echo $this->get_field_name('user_style')?>">
        <option <?php selected('', $instance['user_style'])?> value=""> <?php _e('&mdash; Select &mdash;', 'tinsta')?> </option>
        <option <?php selected('icon', $instance['user_style'])?> value="icon"> <?php _e('Icon', 'tinsta')?> </option>
        <option <?php selected('iconname', $instance['user_style'])?> value="iconname"> <?php _e('Icon and Name', 'tinsta')?> </option>
        <option <?php selected('avatar', $instance['user_style'])?> value="avatar"> <?php _e('Avatar', 'tinsta')?> </option>
        <option <?php selected('avatarname', $instance['user_style'])?> value="avatarname"> <?php _e('Avatar and Name', 'tinsta')?> </option>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('loggedin_menu')?>"><?php _e('Menu when Logged', 'tinsta')?></label>
      <select id="<?php echo $this->get_field_id('loggedin_menu')?>" name="<?php echo $this->get_field_name('loggedin_menu')?>">
        <option value="0"><?php _e('&mdash; Select &mdash;', 'tinsta')?></option>
        <?php foreach ( $menus as $menu ):?>
          <option value="<?php echo esc_attr($menu->term_id)?>" <?php selected( $instance['loggedin_menu'], $menu->term_id )?>>
            <?php echo esc_html($menu->name)?>
          </option>
        <?php endforeach?>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('loggedout_menu')?>"><?php _e('Menu when Logged-Out', 'tinsta')?></label>
      <select id="<?php echo $this->get_field_id('loggedout_menu')?>" name="<?php echo $this->get_field_name('loggedout_menu')?>">
        <option value="0"><?php _e('&mdash; Select &mdash;', 'tinsta')?></option>
        <?php foreach ( $menus as $menu ):?>
          <option value="<?php echo esc_attr($menu->term_id)?>" <?php selected( $instance['loggedout_menu'], $menu->term_id )?>>
            <?php echo esc_html($menu->name)?>
          </option>
        <?php endforeach?>
      </select>
    </p>

    <?php
  }

  function widget($args, $instance)
  {

    $instance = wp_parse_args($instance, [
      'title' => '',
      'user_style' => 'avatarname',
      'loggedin_menu' => '',
      'loggedout_menu' => '',
    ]);

    echo $args['before_widget'];

    $instance['title'] = apply_filters('widget_title', $instance['title']);
    if ($instance['title']) {
      echo $args['before_title'] . $instance['title'] . $args['after_title'];
    }

    if ($instance['user_style']) {
      echo '<div class="user-info style-' . $instance['user_style'] . '">';

      if ($instance['user_style'] == 'avatar' || $instance['user_style'] == 'avatarname') {
        echo get_avatar(wp_get_current_user(), get_theme_mod('avatar_size'));
      }

      if ($instance['user_style'] == 'avatarname' || $instance['user_style'] == 'iconname') {
        if (is_user_logged_in()) {
          echo wp_get_current_user()->display_name;
        }
      }
      echo '</div>';
    }


    if (is_user_logged_in()) {
      if (!empty($instance['loggedout_menu'])) {
        wp_nav_menu([
          'fallback_cb' => '',
          'menu' => $instance['loggedin_menu'],
          'container' => null,
          //'menu_class' => 'sub-menu single-sub-menu',
          //'return' => TRUE,
        ]);
      }

    }
    else {
      if (!empty($instance['loggedout_menu'])) {
        wp_nav_menu([
          'fallback_cb' => '',
          'menu' => $instance['loggedout_menu'],
          'container' => NULL,
          //'menu_class' => 'sub-menu single-sub-menu',
          //'return' => TRUE,
        ]);
      }
    }

    echo $args['after_widget'];
  }
}
