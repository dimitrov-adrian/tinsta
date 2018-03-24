<?php if (tinsta_get_sidebar_variant() && is_active_sidebar('sidebar-' . tinsta_get_sidebar_variant())): ?>
  <aside class="sidebar" role="complementary">
    <?php dynamic_sidebar('sidebar-' . tinsta_get_sidebar_variant()) ?>
  </aside>
<?php endif ?>
