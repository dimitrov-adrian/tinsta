<?php

class Tinsta_BreadCrumbs_Widget extends WP_Widget
{

  function __construct()
  {
    parent::__construct(false, sprintf('(Tinsta) %s', __('Breadcrumbs', 'tinsta')), [
      'description' => __('Breadcrumbs', 'tinsta'),
    ]);

  }

  function form($instance)
  {
    echo '<p>' . __('Breadcrumbs widget does not provide per widget configuration but global such. Edit from Appearance -&gt; Customizer -&gt; Options -&gt; Breadcrumbs.',
        'tinsta') . '</p>';
  }

  function widget($args, $instance)
  {

    global $wp;

    // Do not show breadcrumbs on homepage.
    if (is_front_page() || empty($wp->query_vars)) {
      return;
    }

    $trail = [];
    $min_trails_to_show = 1;

    if (get_theme_mod('options_breadcrumbs_include_home')) {
      $trail[get_home_url()] = get_bloginfo('name');
      $min_trails_to_show++;
    }

    if (is_search()) {
      $trail[get_search_link(get_search_query(false))] = sprintf(__('Search: %s', 'tinsta'), get_search_query());
    } elseif (is_tag()) {
      $trail[] = single_term_title('', true);
    } elseif (is_month()) {
      $trail[] = sprintf(__('Archives: %s', 'tinsta'), get_the_time('F, Y'));
    } elseif (is_year()) {
      $trail[] = sprintf(__('Archives: %s', 'tinsta'), get_the_time('Y'));
    } elseif (is_author()) {
      $trail[] = __('Posts by %s', 'tinsta');
    } elseif (is_day()) {
      $trail[] = sprintf(__('Archives: %s', 'tinsta'), get_the_time('F jS, Y'));
    } elseif (is_home()) {
      $trail[] = single_post_title('', false);
    } else {

      // Post type archive link.
      //if (get_post_type_archive_link(get_post_type())) {
      //  $post_type_object = get_post_type_object(get_post_type());
      //  $trail[get_post_type_archive_link(get_post_type())] = $post_type_object->label;
      //}

      if (is_post_type_archive() || is_singular()) {
        $post_type_object = get_post_type_object(get_post_type());
        if ($post_type_object) {
          $post_type_archive_link = get_post_type_archive_link($post_type_object->name);
          if ($post_type_archive_link) {
            $trail[$post_type_archive_link] = $post_type_object->label;
          }
        }
      }

      // Check if taxonomy is public.
      $queried_object = get_queried_object();
      if (is_a($queried_object, 'WP_Taxonomy')) {
        if (1 || strpos(strtolower($queried_object->taxonomy), 'cat')) {
          foreach (get_ancestors($queried_object->term_id, $queried_object->taxonomy, 'taxonomy') as $ancestor) {
            $category = get_category($ancestor);
            $trail[get_category_link($ancestor)] = $category->name;
          }
          $trail[get_term_link($queried_object)] = $queried_object->name;
        }
      }

      if (is_singular() && is_post_type_hierarchical(get_post_type())) {
        foreach (array_reverse(get_ancestors(get_the_ID(), get_post_type(), 'post_type')) as $ancestor) {
          $post = get_post($ancestor);
          $trail[get_permalink($post)] = $post->post_title;
        }
      } elseif (is_category() || has_category()) {

        //      $the_category = get_the_category();
        //      $the_category = array_shift($the_category);
        //      if ($the_category) {
        //        foreach (get_ancestors($the_category->term_id, $the_category->taxonomy, 'taxonomy') as $ancestor) {
        //          $category = get_category($ancestor);
        //          $trail[get_category_link($ancestor)] = $category->name;
        //        }
        //        $trail[get_category_link($the_category)] = $the_category->name;
        //      }
      }

      if (is_singular()) {
        $trail[get_permalink()] = get_the_title();
      }
    }

    $trail = (array)apply_filters('tinsta_breadcrumb_trail', $trail);

    if ($min_trails_to_show < 2) {
      $min_trails_to_show = 2;
    }

    if (count($trail) < $min_trails_to_show) {
      return;
    }

    $last = array_pop($trail);

    echo $args['before_widget'];
    ?>
    <div class="breadcrumbs">
      <?php if (get_theme_mod('options_breadcrumbs_title')): ?>
        <span class="label">
          <?php echo get_theme_mod('options_breadcrumbs_title') ?>
        </span>
      <?php endif ?>
      <?php foreach ($trail as $trail_link => $trail_label): ?>
        <a href="<?php echo $trail_link ?>" class="breadcrumbs-trail">
          <?php echo $trail_label ?>
        </a>
      <?php endforeach ?>
      <span class="breadcrumbs-trail active">
        <?php echo $last ?>
      </span>
    </div>
    <?php
    echo $args['after_widget'];
  }

}
