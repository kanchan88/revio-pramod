<?php
/**
 * Infer Nepal — Theme bootstrap.
 *
 * - Sets up theme supports
 * - Enqueues the bundled stylesheet + Montserrat
 * - Registers nav menus
 * - Loads helpers, customizer, and block patterns
 */

if (!defined('ABSPATH')) { exit; }

define('INP_THEME_VER', '1.0.0');

/* =================================================================
   Theme supports + image sizes
   ================================================================= */
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height'      => 64,
        'width'       => 240,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('automatic-feed-links');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('block-template-parts');
    add_theme_support('editor-styles');

    // Bring our front-end CSS into the block editor too
    add_editor_style('assets/styles.css');

    register_nav_menus([
        'primary' => __('Primary navigation', 'infer-nepal'),
        'footer-industries' => __('Footer — Industries', 'infer-nepal'),
        'footer-categories' => __('Footer — Categories', 'infer-nepal'),
        'footer-vendors'    => __('Footer — For vendors', 'infer-nepal'),
        'footer-company'    => __('Footer — Company', 'infer-nepal'),
    ]);

    add_image_size('inp-card', 600, 400, true);
    add_image_size('inp-hero', 1200, 700, true);
});

/* =================================================================
   Enqueue assets
   ================================================================= */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'inp-google-fonts',
        'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap',
        [],
        null
    );
    wp_enqueue_style(
        'inp-main',
        get_theme_file_uri('assets/styles.css'),
        [],
        INP_THEME_VER
    );
    wp_enqueue_script(
        'inp-main',
        get_theme_file_uri('assets/app.js'),
        [],
        INP_THEME_VER,
        true
    );
});

/* =================================================================
   Includes
   ================================================================= */
require_once get_theme_file_path('inc/customizer.php');
require_once get_theme_file_path('inc/block-patterns.php');
require_once get_theme_file_path('inc/helpers.php');
require_once get_theme_file_path('inc/nav-walker.php');

/* =================================================================
   Body class — adds CPT/taxonomy info so styles can target pages
   ================================================================= */
add_filter('body_class', function ($c) {
    if (is_singular('software')) $c[] = 'is-software-detail';
    if (is_post_type_archive('software')) $c[] = 'is-software-archive';
    if (is_tax(['industry', 'sw_category', 'country'])) $c[] = 'is-software-tax';
    return $c;
});

/* =================================================================
   Excerpt length
   ================================================================= */
add_filter('excerpt_length', function () { return 22; });
add_filter('excerpt_more',   function () { return '…'; });

/* =================================================================
   Make industries / categories available in front-page query loops
   ================================================================= */
add_action('pre_get_posts', function (WP_Query $q) {
    if (!is_admin() && $q->is_main_query() && $q->is_post_type_archive('software')) {
        $q->set('posts_per_page', 12);
    }
});
