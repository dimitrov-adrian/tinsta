<article <?php post_class() ?>>
  <h2 class="entry-title">
    <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute() ?>">
      <?php if (get_the_title()): ?>
        <?php the_title() ?>
      <?php else: ?>
        <?php the_time(get_option('date_format')) ?>
      <?php endif ?>
    </a>
  </h2>
</article>
