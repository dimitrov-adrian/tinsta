<?php

// Custom widget logic

/**
 * Compute and validate rules (Tinsta condition rule)
 *
 * It could be array for multiples (AND separated) and one (string)
 *
 * Examples computes (work also on singular and nonsingular pages):
 *
 *  post_type=post,page
 *
 *  taxonomy:category
 *  // Check if current post or archive is from taxonomy category
 *  taxonomy:category=cat1,cat2
 *  // Check if current post or archive has taxonomy term cat1 from taxonomy category
 *
 *  get:param1 // Check if current request has $_GET['param1'] set
 *  get:param1=example // Check for $_GET['param1'] = 'example'
 *
 *  post_format=<format1[,...]>
 *
 *  some of the condition tags: 'comments_open', 'pings_open', 'is_home', 'is_front_page',
 * 'is_date', 'is_year', 'is_month', 'is_day', 'is_404', 'is_attachment', 'has_excerpt',
 * 'has_post_thumbnail', 'is_user_logged_in', 'is_rtl', 'in_the_loop', 'is_region_main_query',
 * 'is_singular', 'is_archive', 'is_search', 'is_paged',
 *
 * Example usage:
 *
 * tinsta_compute_rule('post_type=post,news is_singular taxonomy:category=Codes')
 * // Will check if post is singular AND is from type post or news AND is in category Codes
 *
 * tinsta_compute_rule('!is_singular');
 * // Every page that is not singular
 *
 * tinsta_compute_rule([
 *   'is_singular post_type=post taxonomy:category=Codes',
 *   'is_singular post_type=news taxonomy:news_tax=Codes2',
 * ]);
 * // Will check if:
 * //   post is singular AND post_type = post AND has category Codes
 * //   OR
 * //   post is singular AND post_type = news AND has category Codes2
 *
 *
 * @param $rules string|array
 *
 * @return bool
 *  TRUE - pass
 *  FALSE - fail
 */
function tinsta_compute_rule($rules)
{
  global $wp, $wp_query;
  if (is_admin() || ! $rules) {
    return true;
  }
  // Check for array of rules.
  if (is_array($rules)) {
    $rules = array_filter($rules);
    foreach ($rules as $rule) {
      if (tinsta_compute_rule($rule)) {
        return true;
      }
    }

    return false;
  }
  $rules = strtr($rules, ["\n" => ' ', "\r" => ' ']);
  // Handle conditions... OR conditions... OR ... like arrray
  if (strpos($rules, ' OR ')) {
    $multirule = array_filter(explode(' OR ', $rules));
    return tinsta_compute_rule($multirule);
  }

  $cururl = $wp->query_string;
  $rules  = preg_split('#[\s\&]+#', $rules, -1, PREG_SPLIT_NO_EMPTY);
  foreach ($rules as $rule) {
    $rule    = explode('=', $rule, 2);
    $name    = $rule[0];
    $has_arg = isset($rule[1]);
    $args    = $has_arg ? array_map('trim', explode(',', $rule[1])) : [];
    if ($name{0} == '!') {
      $negate = true;
      $name   = substr($name, 1);
    } else {
      $negate = false;
    }
    $state = true;
    // URL checking
    if ($name == 'url') {
      $urlmatch = 0;
      foreach ($args as $arg) {
        if (fnmatch($arg, $cururl)) {
          $urlmatch++;
          break;
        }
      }
      if ( ! $urlmatch) {
        $state = false;
      }
    } // Post type check
    elseif ($name == 'post_type') {
      if (is_singular()) {
        if ( ! is_singular($args)) {
          $state = false;
        }
      } else {
        if ( ! is_post_type_archive($args)) {
          $state = false;
        }
      }
    } // Post format
    elseif ($name == 'post_format') {
      if ( ! in_array(get_post_format(), $args)) {
        $state = false;
      }
    } // Taxonomy.
    elseif (preg_match('#^taxonomy\:([^\s]+)#i', $name, $matches)) {
      if (is_singular()) {
        if ($has_arg) {
          if ( ! has_term($args, $matches[1])) {
            $state = false;
          }
        }
      } else {
        if ($has_arg) {
          if ( ! is_tax($matches[1], $args)) {
            $state = false;
          }
        } else {
          if ( ! is_tax($matches[1])) {
            $state = false;
          }
        }
      }
    } // GET request checks
    elseif (preg_match('#^get\:([^\s]+)#i', $name, $matches)) {
      if ( ! isset($_GET[$matches[1]])) {
        $state = false;
      }
      if ($has_arg) {
        if ( ! in_array($_GET[$matches[1]], $args)) {
          $state = false;
        }
      }
    } // Even/Odd
    elseif ($name == 'is_even') {
      $state = ! ($wp_query->current_post % 2);
    } elseif ($name == 'is_odd') {
      $state = $wp_query->current_post % 2;
    } // Check for conditions tags that shouldn't accept args
    elseif (in_array($name, [
      'comments_open',
      'pings_open',
      'is_home',
      'is_front_page',
      'is_date',
      'is_year',
      'is_month',
      'is_day',
      'is_404',
      'is_attachment',
      'has_excerpt',
      'has_post_thumbnail',
      'is_multi_author',
      'is_user_logged_in',
      'is_rtl',
      'in_the_loop',
      'is_region_main_query',
      'is_paged',
      'is_search',
      'is_archive',
      'wp_attachment_is_image',
    ])) {
      $state = call_user_func($name);
    } // Check for conditions tags that accept args
    elseif (in_array($name, [
      'is_singular',
      'is_post_type_archive',
      'is_author',
    ])) {
      $state = call_user_func($name, $args);
    } // If current page is login page.
    elseif ($name == 'tinsta_is_login_page') {
      $state = tinsta_is_login_page();
    } // Unknown condition.
    else {
      $state = false;
    }
    // Allow others to interact with the compute rule.
    $state = apply_filters('tinsta_compute_rule', $state, $name, $args);
    if ($negate) {
      $state = ! $state;
    }
    if ( ! $state) {
      return false;
    }
  }

  return true;
}

add_filter('sidebars_widgets', function ($sidebars_widgets) {
  return $sidebars_widgets;
});

add_filter('widget_update_callback', function ($instance, $new_instance, $widget, $object) {
  $instance = wp_parse_args((array)$new_instance, [
    'tinsta_widget_logic' => '',
  ]);

  return $instance;
}, 10, 4);

add_action('in_widget_form', function ($object, &$return, $instance) {

  $return = null;

  $instance = wp_parse_args((array)$instance, [
    'tinsta_widget_logic' => '',
  ]);
  ?>

  <p>
    <legend>
      <?php _e('Logic', 'tinsta') ?>
    </legend>
    <textarea name="<?php echo $object->get_field_name('tinsta_widget_logic') ?>"
              id="<?php echo $object->get_field_id('tinsta_widget_logic') ?>"
              class="widefat"><?php echo esc_attr($instance['tinsta_widget_logic']) ?></textarea>
  </p>

  <?php
  return [$object, $return, $instance];

}, 10, 3);
