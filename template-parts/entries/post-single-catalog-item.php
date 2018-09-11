<article <?php post_class() ?>>

  <header class="entry-header">

    <div class="entry-thumbnail-wrapper">
      <?php if (post_type_supports(get_post_type(), 'thumbnail') && has_post_thumbnail()): ?>
        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute() ?>">
          <?php the_post_thumbnail('large') ?>
        </a>
      <?php endif ?>
    </div>

    <div class="entry-details">

      <?php if (get_the_title()): ?>
        <h1 class="entry-title">
          <?php the_title() ?>
        </h1>
      <?php endif ?>

      <?php if (get_comments_number()): ?>
        <div class="entry-meta-comments user-rating">
          <span class="screen-reader-text">
            <?php _e('Comments', 'tinsta') ?>
          </span>
          <a href="#comments">
            <?php comments_number() ?>
          </a>
        </div>
      <?php endif ?>

      <ul class="entry-details-meta">
        <?php foreach (get_object_taxonomies(get_post(), 'objects') as $tax_object): ?>
          <?php if ($tax_object->public && !in_array($tax_object->name, ['post_format'])): ?>
            <?php if ($terms = get_the_terms(get_the_ID(), $tax_object->name)): ?>
              <li class="tax-<?php echo $tax_object->name ?>">
                <span class="label"><?php echo $tax_object->label ?></span>
                <span class="term-wrapper">
                  <?php foreach ($terms as $term): ?>
                    <a href="<?php echo get_term_link($term, $tax_object->name) ?>" rel="nofollow" class="term">
                      <?php echo $term->name ?>
                    </a>
                  <?php endforeach ?>
                </span>
              </li>
            <?php endif ?>
          <?php endif ?>
        <?php endforeach ?>
        <li>
          <span class="label"><?php _e('Published', 'tinsta') ?></span>
          <span class="term-wrapper">
            <time class="term " datetime="<?php the_time('c') ?>" pubdate="pubdate">
              <?php the_time(get_option('date_format')) ?>
            </time>
          </span>
      </ul>

      <?php tinsta_the_social_code() ?>

      <?php if (has_excerpt() && !post_password_required()): ?>
        <div class="entry-summary">
          <?php the_excerpt_embed() ?>
        </div>
      <?php endif ?>

    </div>

  </header>

  <div class="entry-content">
    <?php
    the_content(sprintf(__('Continue reading %s', 'tinsta'),
      '<span class="screen-reader-text">' . get_the_title() . '</span>'));
    tinsta_pagination('singular');
    ?>
  </div>

  <?php

  $footer = '';

  if (!is_post_type_hierarchical(get_post_type()) && get_theme_mod('post_type_' . get_post_type() . '_append_post_nav')) {
    ob_start();
    tinsta_pagination('prevnext');
    $footer = ob_get_clean();
  }

  if ($footer) {
    echo '<footer class="entry-footer">' . $footer . '</footer>';
  }
  ?>

</article>



