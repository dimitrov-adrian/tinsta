<?php if (tinsta_get_sidebar_variant() && is_active_sidebar('sidebar-' . tinsta_get_sidebar_variant() . '-secondary')): ?>
  <aside class="sidebar sidebar-secondary" role="complementary">
    <?php dynamic_sidebar('sidebar-' . tinsta_get_sidebar_variant() . '-secondary') ?>
  </aside>
<?php endif ?>
