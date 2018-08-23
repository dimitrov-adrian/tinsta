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

    <div class="entry-meta">

      <time class="entry-meta-time" datetime="<?php the_time('c') ?>" pubdate="pubdate">
        <a href="<?php echo get_month_link(get_post_time('Y'), get_post_time('m')) ?>">
          <?php the_time(get_option('date_format')) ?>
        </a>
      </time>

      <?php if (get_comments_number()): ?>
        <span class="entry-meta-comments user-rating">
          <span class="screen-reader-text">
            <?php _e('Comments', 'tinsta') ?>
          </span>
          <a href="#comments">
            <?php comments_number() ?>
          </a>
        </span>
      <?php endif ?>

      <span class="entry-meta-author">
        <span class="screen-reader-text">
          <?php _e('Authored by', 'tinsta') ?>
        </span>
        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))) ?>">
          <?php the_author() ?>
        </a>
      </span>

      <?php tinsta_the_social_code() ?>

    </div>

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

    <?php if ($categories = get_the_terms(get_the_ID(), 'category')): ?>
      <div class="entry-footer-row categories">
        <span class="entry-footer-categories-label">
          <?php _e('Categories: ', 'tinsta') ?>
        </span>
        <?php echo get_the_category_list() ?>
      </div>
    <?php endif ?>

    <?php if (null != ($tags = get_the_tag_list(__('Tags: ', 'tinsta'), ', '))): ?>
      <div class="entry-footer-row tags">
        <?php echo $tags ?>
      </div>
    <?php endif ?>

    <?php if ( ! is_post_type_hierarchical(get_post_type()) && get_theme_mod('post_type_' . get_post_type() . '_append_post_nav') ):?>
      <?php tinsta_pagination('prevnext') ?>
    <?php endif ?>

  </footer>

</article>
