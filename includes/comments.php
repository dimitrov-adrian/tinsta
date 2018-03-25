<?php


/**
 * Comment renderer callback
 *
 * @param $comment
 * @param $args
 * @param $depth
 */
function tinsta_comment_callback($comment, $args, $depth)
{
  if ($comment->comment_approved || current_user_can('moderate_comments')) {
    if ($comment->comment_type == 'pingback' || $comment->comment_type == 'trackback') {
      get_template_part('template-parts/comments/pingback');
    }
    else {
      $args['depth'] = $depth > 1 ? $depth - 1 : $depth;
      get_template_part('template-parts/comments/comment');
    }
  }
}

add_filter('comment_form_defaults', function ($defaults) {

  // Enable auto-completion for fields

  if (!empty($defaults['fields']['author'])) {
    $defaults['fields']['author'] = str_replace('<input ', '<input autocomplete="name" ', $defaults['fields']['author']);
  }

  if (!empty($defaults['fields']['url'])) {
    $defaults['fields']['url'] = str_replace('<input ', '<input autocomplete="on" ', $defaults['fields']['url']);
  }

  if (!empty($defaults['fields']['email'])) {
    $defaults['fields']['email'] = str_replace('<input ', '<input autocomplete="email" ', $defaults['fields']['email']);
  }

  // Append avatar to comment post form.
  $defaults['title_reply_after'] .= '<div class="comment-form-avatar">' . get_avatar(get_current_user_id(), get_theme_mod('avatar_size')) . '</div>';

  return $defaults;

}, 1000);


// Security reasons, Format comment text.
add_filter('comment_text', function ($comment_text, $comment, $args) {

  if ($comment->user_id && user_can($comment->user_id, 'unfiltered_html')) {
    return $comment_text;
  }

  global $allowedtags;

  $allowed_html_tags = '';
  foreach (array_keys($allowedtags) as $tag) {
    $allowed_html_tags .= "<$tag>";
  }

  return strip_tags($comment_text, $allowed_html_tags);

}, 10, 3);
