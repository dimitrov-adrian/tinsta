<article <?php post_class() ?>>

  <header class="entry-header">
    <h1 class="entry-title">
      <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute() ?>">
        <?php the_title() ?>
      </a>
    </h1>
    <?php if (get_post_meta(get_the_ID(), '_wp_attachment_image_alt', true)): ?>
      <h5>
        <?php echo get_post_meta(get_the_ID(), '_wp_attachment_image_alt', true) ?>
      </h5>
    <?php endif ?>

    <div class="entry-meta">

      <time class="entry-meta-time" datetime="<?php the_time('c') ?>" pubdate="pubdate">
        <?php the_time(get_option('date_format')) ?>
      </time>

      <span class="entry-meta-item">
        <span class="screen-reader-text">
          <?php _e('Type', 'tinsta') ?>
        </span>
        <?php echo get_post_mime_type() ?>
      </span>

    </div>

  </header>

  <?php if (get_the_excerpt() && !post_password_required()): ?>
    <div class="entry-summary">
      <?php the_excerpt() ?>
    </div>
  <?php endif ?>

  <div class="entry-content">
    <?php the_content() ?>
  </div>

</article>
