<article <?php post_class() ?>>

  <header class="entry-header">

    <?php if (post_type_supports(get_post_type(), 'thumbnail') && has_post_thumbnail()): ?>
      <div class="entry-thumbnail-wrapper">
        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute() ?>">
          <?php the_post_thumbnail('large') ?>
        </a>
      </div>
    <?php endif ?>

    <?php if (get_the_title()): ?>
      <h1 class="entry-title">
        <?php the_title() ?>
      </h1>
    <?php endif ?>

    <?php if (has_excerpt() && ! post_password_required()): ?>
      <div class="entry-summary">
        <?php the_excerpt_embed() ?>
      </div>
    <?php endif ?>

  </header>

  <div class="entry-content">
    <?php
    the_content(sprintf(__('Continue reading %s', 'tinsta'), '<span class="screen-reader-text">' . get_the_title() . '</span>'));
    tinsta_pagination('singular');
    ?>
  </div>

  <footer class="entry-footer">

    <?php if ( ! is_post_type_hierarchical(get_post_type()) && get_theme_mod('post_type_' . get_post_type() . '_append_post_nav') ):?>
      <?php tinsta_pagination('prevnext') ?>
    <?php endif ?>

  </footer>

</article>
