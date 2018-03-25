<?php

$authors = [];

if (function_exists('get_coauthors')) {
  foreach (get_coauthors() as $author) {
    $authors[] = $author->ID;
  }
}

else {
  $authors[] = get_the_author_meta('ID');
}

if (!$authors) {
  return;
}

?>

<div class="author-list">
  <?php foreach ($authors as $author_id):?>
  <div class="author">
    <?php echo get_avatar($author_id, 96); ?>
    <div class="author-title">
      <?php
        echo '
          <a class="url fn n" href="' . esc_url( get_author_posts_url( $author_id ) ) . '" 
            title="' . esc_attr( get_the_author_meta( 'display_name', $author_id ) ) . '">
            ' . get_the_author_meta( 'display_name', $author_id) . '
            </a>'?>
    </div>
  <?php endforeach?>
</div>
