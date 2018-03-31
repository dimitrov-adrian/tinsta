<?php

$layout_page_id = get_theme_mod('post_type_' . get_post_type() . '_layout_page_id');
if ($layout_page_id) {
  echo get_post_field('post_content', $layout_page_id, 'display');
}