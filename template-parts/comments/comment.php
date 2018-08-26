<div id="comment-<?php comment_ID() ?>" <?php comment_class('comment') ?>>

  <div class="comment-meta">

    <?php if (!empty($show_avatars)):?>
    <div class="comment-author-avatar">
      <?php echo get_avatar(get_comment_author_email(), get_theme_mod('component_avatar_size')) ?>
    </div>
    <?php endif?>

    <div class="comment-author">
      <?php comment_author() ?>
    </div>

    <a href="#comment-<?php comment_ID() ?>">
      <div class="comment-time">
        <?php printf(__('%s ago', 'tinsta'), human_time_diff(strtotime($comment->comment_date), time())) ?>
      </div>
    </a>

    <?php
      // Always show comment reply link, regardless of the depth.
      comment_reply_link(['max_depth' => get_option('thread_comments_depth'), 'depth' => 1]);
    ?>

    <?php edit_comment_link() ?>

  </div>

  <div class="comment-content">
    <?php comment_text() ?>
  </div>

</div>
