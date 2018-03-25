<?php

class Tinsta_BreadCrumbs_Widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('Breadcrumbs', 'tinsta')));
  }

  function widget($args, $instance)
  {
    tinsta_the_breadcrumbs();
  }
}
