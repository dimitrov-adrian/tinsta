<div <?php comment_class('comment', $comment) ?> >

  <time>
    <?php printf(__('%s ago', 'tinsta'), human_time_diff(strtotime($comment->comment_date), time())) ?>
  </time>

  <div class="comment-pingback-content">
    <?php comment_author_link($comment) ?>
  </div>

</div>
