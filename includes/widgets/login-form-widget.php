<?php

class Tinsta_Login_Form_Widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('Login Form', 'tinsta')), [
        'description' => __('Display login form.', 'tinsta'),
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

    // This widget must not be displayed on login page or on logged in users.
    if (is_user_logged_in() || tinsta_is_login_page()) {
      return;
    }

    $instance = wp_parse_args($instance, ['title' => '']);

    echo $args['before_widget'];

    $instance['title'] = apply_filters('widget_title', $instance['title']);
    if ($instance['title']) {
      echo $args['before_title'] . $instance['title'] . $args['after_title'];
    }

    $form_args = [
      'echo' => true,
      'form_id' => $this->get_field_id('loginform'),
      'id_username' => $this->get_field_id('login'),
      'id_password' => $this->get_field_id('password'),
      'id_remember' => $this->get_field_id('remember'),
      'id_submit' => $this->get_field_id('submit'),
    ];

    wp_login_form($form_args);

    echo $args['after_widget'];
  }
}
