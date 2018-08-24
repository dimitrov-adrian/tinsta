<article <?php post_class() ?>>

  <?php if (post_type_supports(get_post_type(), 'thumbnail') && has_post_thumbnail()): ?>
    <div class="entry-thumbnail-wrapper">
      <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute() ?>">
        <?php the_post_thumbnail('medium') ?>
      </a>
    </div>
  <?php endif ?>

  <header class="entry-header">

    <h2 class="entry-title">
      <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute() ?>">
        <?php if (get_the_title()): ?>
          <?php the_title() ?>
        <?php else: ?>
          <?php the_time(get_option('date_format')) ?>
        <?php endif ?>
      </a>
    </h2>

    <div class="entry-meta">
      <time class="entry-meta-time" datetime="<?php the_time('c') ?>" pubdate="pubdate">
        <?php the_time(get_option('date_format')) ?>
      </time>
      <?php if (get_comments_number()): ?>
        <span class="entry-meta-comments">
          <span class="screen-reader-text">
            <?php _e('Comments', 'tinsta') ?>
          </span>
          <?php comments_number('0', '1', '%') ?>
        </span>
      <?php endif ?>
      <span class="entry-meta-author">
        <span class="screen-reader-text">
          <?php _e('Authored by', 'tinsta') ?>
        </span>
        <?php the_author() ?>
      </span>

    </div>

  </header>

  <?php if ( get_theme_mod("post_type_" . get_post_type() . "_archive_show") == 'full' ):?>
    
    <div class="entry-summary">
      <?php the_content() ?>
    </div>

  <?php else:?>

    <?php if (get_the_excerpt() && ! post_password_required()): ?>
      <div class="entry-summary">
        <?php the_excerpt() ?>
      </div>
    <?php endif ?>

    <?php if ( ! post_password_required() && ( ! get_the_title() || ! get_the_excerpt())): ?>
      <div class="entry-summary">
        <p>
          <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute() ?>">
            <?php _e('Read more', 'tinsta') ?>
          </a>
        </p>
      </div>
    <?php endif ?>

  <?php endif?>

</article>
