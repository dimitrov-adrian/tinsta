<?php


$system_page_404_theming = get_theme_mod('system_page_404_theming') ? '' : 'embed';

if ($system_page_404_theming == 'embed') {
  wp_enqueue_style('tinsta-error', get_template_directory_uri() . '/assets/css/error.css');
}

get_header($system_page_404_theming) ?>

<div class="error-content-wrapper">

  <div class="content">
    <?php if (is_active_sidebar('error-404')): ?>
      <?php dynamic_sidebar('error-404') ?>
    <?php else: ?>
      <h1 class="title">
        <?php _e('Oops! That page can&rsquo;t be found.', 'tinsta') ?>
      </h1>
      <p>
        <?php _e('It looks like nothing was found at this location. Maybe try a search?', 'tinsta') ?>
      </p>
    <?php endif ?>
  </div>

  <?php if ($system_page_404_theming == 'embed'): ?>
    <div class="site-info">
      <a href="javascript:window.history.back();" style="float:left;">
        <?php _e('Back', 'tinsta')?>
      </a>
      <a href="<?php home_url()?>">
        <?php bloginfo('name') ?>
      </a>
    </div>
  <?php endif ?>

</div>

<?php get_footer($system_page_404_theming) ?>
