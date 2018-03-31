<?php

class Tinsta_LoginForm_Widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('Login Form', 'tinsta')));
  }

  function form($instance)
  {
    $instance = wp_parse_args($instance, ['title' => '']);

    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title') ?>">
        <?php _e('Title', 'tinsta') ?>
      </label>
      <input id="<?php echo $this->get_field_id('title') ?>" name="<?php echo $this->get_field_name('title') ?>" type="text"
             value="<?php echo esc_attr($instance['title']) ?>" />
    </p>
    <?php

  }

  function widget($args, $instance)
  {

    if (is_user_logged_in()) {
      return;
    }

    $instance = wp_parse_args($instance, ['title' => '']);

    echo $args['before_widget'];

    $instance['title'] = apply_filters('widget_title', $instance['title']);
    if ($instance['title']) {
      echo $args['before_title'] . $instance['title'] . $args['after_title'];
    }

    $form_args = [
      'echo'           => true,
      'redirect'       => site_url($_SERVER['REQUEST_URI']),
      'form_id'        => 'loginform',
      'id_username'    => 'user_login',
      'id_password'    => 'user_pass',
      'id_remember'    => 'rememberme',
      'id_submit'      => 'wp-submit',
      'remember'       => true,
      'value_username' => null,
      'value_remember' => false,
    ];

    wp_login_form($form_args);

    echo $args['after_widget'];
  }
}
