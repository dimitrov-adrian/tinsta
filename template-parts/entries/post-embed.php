<article <?php post_class() ?>>

  <div class="entry-content">
    <?php
      the_content(sprintf(__('Continue reading %s', 'tinsta'), '<span class="screen-reader-text">' . get_the_title() . '</span>'));
      tinsta_pagination('singular');
    ?>
  </div>

</article>
