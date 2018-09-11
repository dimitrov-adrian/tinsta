<div id="site-enter-agreement" class="dialog-overlay">
  <div class="dialog-inner">
    <div class="content">
      <?php echo get_theme_mod('options_site_agreement_text') ?>
    </div>
    <div class="buttons">
      <button id="site-enter-agreement-button" class="primary">
        <?php echo get_theme_mod('options_site_agreement_agree_button') ?>
      </button>
      <?php if (get_theme_mod('options_site_agreement_cancel_url')): ?>
        <a href="<?php echo esc_attr(get_theme_mod('options_site_agreement_cancel_url')) ?>"
           rel="nofollow noindex noopener">
          <?php echo get_theme_mod('options_site_agreement_cancel_title') ?>
        </a>
      <?php endif ?>
    </div>
  </div>
</div>
