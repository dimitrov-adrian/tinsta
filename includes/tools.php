<div class="wrap">

  <h1>
    <?php _e('Tools', 'tinsta') ?>
  </h1>

  <?php
    
    // @TODO move outside the template.
    if ( ! empty($_POST['tinsta-import'])) {
      if ( ! empty($_FILES['file']['tmp_name']) && empty($_FILES['file']['error'])) {
        if ( tinsta_settings_import( $_FILES['file']['tmp_name'], !empty($_POST['tinsta_settings_only']) ) ) {
          ?>
          <div class="updated notice is-dismissible">
            <p> <?php _e('Settings file is imported', 'tinsta')?></p>
          </div>
          <?php
        }
      } else {
        ?>
        <div class="error notice is-dismissible">
          <p> <?php _e('Upload error.', 'tinsta')?></p>
        </div>
        <?php
      }
    }

    if ( ! empty($_POST['tinsta-reset'])) {
      foreach (tinsta_get_options_defaults() as $key => $val) {
        set_theme_mod($key, $val);
      }
      ?>
        <div class="updated notice is-dismissible">
            <p> <?php _e('All theme settings are reset.', 'tinsta')?></p>
        </div>
      <?php
    }

    if ( ! empty($_POST['tinsta-invalidate-stylesheets'])) {
      delete_transient('tinsta_theme');
      ?>
      <div class="updated notice is-dismissible">
        <p> <?php _e('Invalidated', 'tinsta')?></p>
      </div>
      <?php
    }

  ?>

  <table id="tinsta-export" class="form-table">
    <tbody>
      <tr>
        <th>
          <?php _e('Export', 'tinsta')?>
        </th>
        <td>
          <form method="get" action="<?php echo admin_url('admin-ajax.php') ?>">
            <p class="description">
              <?php _e('Export .tinsta file with current settings.', 'tinsta') ?>
            </p>
            <button type="submit" name="action" value="tinsta-export-settings" class="button">
              <?php _e('Download', 'tinsta') ?>
            </button>
          </form>
        </td>
      </tr>
      <tr>
        <th>
          <?php _e('Import', 'tinsta')?>
        </th>
        <td>
          <form method="post" enctype="multipart/form-data" onsubmit="return confirm('<?php _e('This action is going to override settings. Do you want to continue?', 'tinsta') ?>')">
            <p class="description">
              <?php _e('Import theme settings from .tinsta file. Note that this will override all your current settings about the theme.', 'tinsta') ?>
            </p>
            <p>
              <label>
                <?php _e('File', 'tinsta')?>
                <input type="file" name="file" accept=".tinsta" required="required" />
              </label>
            </p>
            <p>
              <label>
                <input type="checkbox" name="tinsta_settings_only" value="on" checked="checked" />
                <?php _e('Import Tinsta\'s settings only. <span class="description">All other variables created from custom theme or plugins will be ignored (usually, there should be none).</span>', 'tinsta')?>
              </label>
            </p>
            <p>
              <button type="submit" name="tinsta-import" class="button">
                <?php _e('Import', 'tinsta') ?>
              </button>
            </p>
          </form>
        </td>
      </tr>

      <tr>
        <th>
          <?php _e('Reset', 'tinsta')?>
        </th>
        <td>
          <form method="post" onsubmit="return confirm('<?php _e('This action is going to override settings. Do you want to continue?', 'tinsta') ?>')">
            <p class="description">
              <?php _e('Reset all theme settings to default values.', 'tinsta') ?>
            </p>
            <p>
              <button type="submit" name="tinsta-reset" value="1" class="button button-link-delete">
                <?php _e('Reset', 'tinsta') ?>
              </button>
            </p>
          </form>
        </td>
      </tr>

      <tr>
        <th>
          <?php _e('Invalidate CSS Cache', 'tinsta')?>
        </th>
        <td>
          <form method="post">
            <p class="description">
              <?php _e('Invalidate currently generated stylesheets. Usually this is not required, and after every settings save they are invalidate automatically, but sometimes this button could helps.', 'tinsta') ?>
            </p>
            <p>
              <button type="submit" name="tinsta-invalidate-stylesheets" value="1" class="button">
                <?php _e('Invalidate', 'tinsta') ?>
              </button>
            </p>
          </form>
        </td>
      </tr>

    </tbody>
  </table>

</div>
