<?php

/**
 * Template Name: Content Only, No Sidebars
 *
 * We use template file, just to show the template selector for posts.
 */

get_header();

the_post();
the_content();

get_footer();
