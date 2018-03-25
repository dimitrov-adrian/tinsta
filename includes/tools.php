<div class="wrap">

  <h1>
    <?php _e('Tools', 'tinsta')?>
  </h1>

  <table class="widefat importers striped">
    <tbody>

      <tr>
        <td>
          <?php _e('Export Tinsta theme settings into .tinsta file.', 'tinsta')?>
        </td>
        <td>
          <form method="get" action="<?php echo admin_url('admin-ajax.php')?>">
            <button name="action" value="tinsta-export-settings" class="button">
              <?php _e('Export', 'tinsta')?>
            </button>
          </form>
        </td>
      </tr>

      <tr>
        <td>
          <p>
            <?php _e('Import theme settings from .tinsta file. Note that this will override all your current settings about the theme.', 'tinsta')?>
          </p>
        </td>
        <td>
          <?php
          if (!empty($_POST['tinsta-import'])) {
            if (!empty($_FILES['file']['tmp_name']) && empty($_FILES['file']['error'])) {
              if (tinsta_settings_import($_FILES['file']['tmp_name'])) {
                echo '<p>' . __('Settings file is imported', 'tinsta') . '</p>';
              }
            }
            else {
              echo '<p>' . __('Upload error.', 'tinsta') . '</p>';
            }
          }
          ?>
          <form method="post" enctype="multipart/form-data">
            <p>
              <input type="file" name="file" />
            </p>
            <p>
              <button name="tinsta-import" value="1" class="button" onclick="return confirm('<?php _e('Do you want to continue?', 'tinsta')?>')">
                <?php _e('Import', 'tinsta')?>
              </button>
            </p>
          </form>
        </td>
      </tr>

      <tr>
        <td>
          <p>
            <?php _e('Reset all theme settings to default values.', 'tinsta')?>
          </p>
        </td>
        <td>
          <?php
            if (!empty($_POST['tinsta-reset'])) {
              foreach (tinsta_get_options_defaults() as $key => $val) {
                set_theme_mod($key, $val);
              }
              echo '<p>' . __('All theme settings are reset.', 'tinsta') . '</p>';
            }
          ?>
          <form method="post">
            <p>
              <button name="tinsta-reset" value="1" class="button button-link-delete" onclick="return confirm('<?php _e('Do you want to continue?', 'tinsta')?>')">
                <?php _e('Reset', 'tinsta')?>
              </button>
            </p>
          </form>
        </td>
      </tr>

      <tr>
        <td>
          <p>
            <?php _e('Invalidate currently generated stylesheets.', 'tinsta')?>
          </p>
        </td>
        <td>
          <?php
            if (!empty($_POST['tinsta-invalidate-stylesheets'])) {
              delete_transient('tinsta_theme');
              echo '<p>' . __('Invalidated', 'tinsta') . '</p>';
            }
          ?>
          <form method="post">
            <p>
              <button name="tinsta-invalidate-stylesheets" value="1" class="button">
                <?php _e('Invalidate', 'tinsta')?>
              </button>
            </p>
          </form>
        </td>
      </tr>

    </tbody>
  </table>

</div>
