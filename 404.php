<?php

$theme_404_page = get_theme_mod('theme_404_page') ? '' : 'embed';

get_header($theme_404_page) ?>

<div class="site-main" role="main" id="main">

  <h1 class="title">
    <?php _e('Oops! That page can&rsquo;t be found.', 'tinsta') ?>
  </h1>

  <div class="content">
    <?php if (is_active_sidebar('error-404')): ?>
      <?php dynamic_sidebar('error-404') ?>
    <?php else: ?>
      <p>
        <?php _e('It looks like nothing was found at this location. Maybe try a search?', 'tinsta') ?>
      </p>
    <?php endif ?>
  </div>

  <?php if ($theme_404_page == 'embed'):?>
    <div class="site-info">
      <?php bloginfo('name') ?>
    </div>
  <?php endif?>

</div>


<?php get_footer($theme_404_page) ?>
