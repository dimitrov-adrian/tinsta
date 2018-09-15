<?php
// @TODO move outside the template.
?>

<div class="wrap">

  <h1>
    <?php _e('Tools', 'tinsta') ?>
  </h1>

  <?php

  if (!empty($_POST['tinsta-import'])) {
    if (!empty($_FILES['file']['tmp_name']) && empty($_FILES['file']['error'])) {
      $imported = tinsta_settings_import($_FILES['file']['tmp_name'], false, !empty($_POST['dry-run']));
      ?>
      <div class="updated notice is-dismissible">
        <p>
          <?php printf(__('%d settings are imported.', 'tinsta'), count($imported)) ?>
        </p>
        <details>
          <table>
            <?php foreach ($imported as $key => $value): ?>
              <tr>
                <th class="textleft">
                  <?php echo esc_html($key) ?>
                </th>
                <td>
                  &mdash;&gt;
                </td>
                <td>
                  <?php echo esc_html($value) ?>
                </td>
              </tr>
            <?php endforeach ?>
          </table>
        </details>
      </div>
    <?php } else { ?>
      <div class="error notice is-dismissible">
        <p> <?php _e('Upload error.', 'tinsta') ?></p>
      </div>
      <?php
    }
  }

  if (!empty($_POST['tinsta-reset'])) {
    $status = [];
    $current_mods = get_theme_mods();
    foreach (tinsta_get_options_defaults() as $key => $val) {
      $old_value = isset($current_mods[$key]) ? $current_mods[$key] : '';
      if ($old_value != $val) {
        $status[$key] = [
          'from' => $old_value,
          'to' => $val,
        ];
      }
      set_theme_mod($key, $val);
    }
    ?>
    <div class="updated notice is-dismissible">
      <p>
        <?php _e('All theme settings are reset.', 'tinsta') ?>
      </p>
      <?php if ($status): ?>
        <details>
          <table>
            <?php foreach ($status as $key => $status_single): ?>
              <tr>
                <th class="textleft">
                  <?php echo esc_html($key) ?>
                </th>
                <td>
                  <?php echo esc_html($status_single['from']) ?>
                </td>
                <td>
                  <?php echo esc_html($status_single['to']) ?>
                </td>
              </tr>
            <?php endforeach ?>
          </table>
        </details>
      <?php endif ?>
    </div>
    <?php
  }

  if (!empty($_POST['tinsta-clean'])) {
    $cleaned_settings = tinsta_settings_clean(!empty($_POST['dry-run']));
    ?>
    <div class="updated notice is-dismissible">
      <p> <?php printf(__('%s settings are removed.', 'tinsta'), count($cleaned_settings)) ?></p>
      <?php if ($cleaned_settings): ?>
        <details>
          <ul>
            <?php foreach ($cleaned_settings as $key): ?>
              <li>
                <var> <?php echo esc_html($key) ?> </var>
              </li>
            <?php endforeach ?>
          </ul>
        </details>
      <?php endif ?>
    </div>
    <?php
  }

  if (!empty($_POST['tinsta-invalidate-stylesheets'])) {
    delete_transient('tinsta_theme');
    tinsta_get_stylesheet('comments');
    tinsta_get_stylesheet('default');
    tinsta_get_stylesheet('error');
    tinsta_get_stylesheet('login');
    ?>
    <div class="updated notice is-dismissible">
      <p> <?php _e('Invalidated', 'tinsta') ?></p>
    </div>
    <?php
  }

  ?>

  <table id="tinsta-export" class="form-table">
    <tbody>
    <tr>
      <th>
        <?php _e('Export', 'tinsta') ?>
      </th>
      <td>
        <form method="get" action="<?php echo admin_url('admin-ajax.php') ?>" target="_blank">
          <p class="description">
            <?php _e('Export .tinsta file with current settings.', 'tinsta') ?>
          </p>
          <p>
            <label>
              <input type="checkbox" name="tinsta_settings_only" value="on" checked="checked" />
              <?php _e('Export Tinsta\'s settings only. <span class="description">All other variables created from custom theme or plugins will be ignored.</span>',
                'tinsta') ?>
            </label>
          </p>
          <p>
            <button type="submit" name="action" value="tinsta-export-settings" class="button">
              <?php _e('Download', 'tinsta') ?>
            </button>
          </p>
        </form>
      </td>
    </tr>
    <tr>
      <th>
        <?php _e('Import', 'tinsta') ?>
      </th>
      <td>
        <form method="post" enctype="multipart/form-data"
              onsubmit="return confirm('<?php _e('This action is going to override settings. Do you want to continue?',
                'tinsta') ?>')">
          <p class="description">
            <?php _e('Import theme settings from .tinsta file. Note that this will override all your current settings about the theme.',
              'tinsta') ?>
          </p>
          <p>
            <label>
              <?php _e('File', 'tinsta') ?>
              <input type="file" name="file" accept=".tinsta" required="required" />
            </label>
          </p>
          <p>
            <label>
              <input type="checkbox" name="dry-run" value="yes" checked="checked" />
              <?php _e('Dry run', 'tinsta') ?>
            </label>
          </p>
          <p>
            <button type="submit" name="tinsta-import" class="button" value="true">
              <?php _e('Import', 'tinsta') ?>
            </button>
          </p>
        </form>
      </td>
    </tr>

    <tr>
      <th>
        <?php _e('Reset', 'tinsta') ?>
      </th>
      <td>
        <form method="post"
              onsubmit="return confirm('<?php _e('This action is going to override settings. Do you want to continue?',
                'tinsta') ?>')">
          <p class="description">
            <?php _e('Reset all theme settings to default values.', 'tinsta') ?>
          </p>
          <p>
            <button type="submit" name="tinsta-reset" value="true" class="button button-link-delete">
              <?php _e('Reset', 'tinsta') ?>
            </button>
          </p>
        </form>
      </td>
    </tr>

    <tr>
      <th>
        <?php _e('Clean', 'tinsta') ?>
      </th>
      <td>
        <form method="post"
              onsubmit="return confirm('<?php _e('This action is going to remove settings. Do you want to continue?',
                'tinsta') ?>')">
          <p class="description">
            <?php _e('Clean unknown settings. Usually, there should not be something to clean, but some of the plugins could store something as theme setting.',
              'tinsta') ?>
          </p>
          <p>
            <input type="checkbox" name="dry-run" value="yes" checked="checked" />
            <?php _e('Dry run', 'tinsta') ?>
          </p>
          <p>
            <button type="submit" name="tinsta-clean" value="true" class="button button-link-delete">
              <?php _e('Clean', 'tinsta') ?>
            </button>
          </p>
        </form>
      </td>
    </tr>

    <tr>
      <th>
        <?php _e('Invalidate CSS Cache', 'tinsta') ?>
      </th>
      <td>
        <form method="post">
          <p class="description">
            <?php _e('Invalidate currently generated stylesheets. Usually this is not required, and after every settings save they are invalidate automatically, but sometimes this button could helps.',
              'tinsta') ?>
          </p>
          <p>
            <button type="submit" name="tinsta-invalidate-stylesheets" value="true" class="button">
              <?php _e('Invalidate', 'tinsta') ?>
            </button>
          </p>
        </form>
      </td>
    </tr>

    </tbody>
  </table>

</div>
