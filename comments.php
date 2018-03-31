<?php

// Hide comments when:
if (

  // Not Single page
  ! is_singular()

  // Password is required
  || post_password_required()

  // Have no comments and comments are closed
  || ( ! have_comments() && ! comments_open())

  // Post type does not supports comments
  || ! post_type_supports(get_post_type(), 'comments')

  // Post type is attachment
  || get_post_type() == 'attachment'

) {
  return;
}

$comments_order = strtoupper(empty($_GET['comments-order']) || ! is_scalar($_GET['comments-order']) ? get_option('comment_order') : $_GET['comments-order']);

$classes = ['comments-wrapper'];

if ( ! get_option('show_avatars')) {
  $classes[] = 'hide-avatars';
}

?>

<div class="<?php echo implode(' ', $classes) ?>" id="comments">

  <div class="comments-heading">
    <?php echo get_comments_number_text() ?>
  </div>

  <?php if (have_comments() && get_comments_number() > 1): ?>
    <div class="comments-toolbar">
      <label>
        <?php _e('Order: ', 'tinsta') ?>
        <select onchange="window.location.href=this.value+'#comments'">
          <option value="<?php echo add_query_arg('comments-order', 'ASC') ?>" <?php selected($comments_order, 'ASC') ?>>
            <?php _e('Oldest first', 'tinsta') ?>
          </option>
          <option value="<?php echo add_query_arg('comments-order', 'DESC') ?>" <?php selected($comments_order, 'DESC') ?>>
            <?php _e('Newest first', 'tinsta') ?>
          </option>
        </select>
      </label>
    </div>
  <?php endif ?>

  <div class="comments-list" itemprop="UserComments">
    <?php if (have_comments()): ?>
      <?php wp_list_comments([
        'reverse_top_level' => $comments_order == 'DESC',
        'reverse_children'  => $comments_order == 'DESC',
        'component_avatar_size'       => 100,
        'short_ping'        => true,
        'callback'          => 'tinsta_comment_callback',
      ]) ?>
    <?php endif ?>

    <?php tinsta_pagination('comments') ?>

  </div>

  <?php if (comments_open() && post_type_supports(get_post_type(), 'comments')): ?>
    <?php comment_form() ?>
  <?php else: ?>
    <p class="no-comments">
      <?php _e('Comments are closed.', 'tinsta') ?>
    </p>
  <?php endif ?>

</div>
