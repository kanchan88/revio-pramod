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
require_once get_theme_file_path('inc/blocks.php');

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
   FILTERING & SEARCH
   - Archive / taxonomy / search pages all read GET params and filter
     the main query: ?country=nepal&deployment=cloud&min_rating=4&mobile=1
   - Search uses post_type=software by default and matches title,
     content, excerpt, vendor and included_features (postmeta).
   ================================================================= */
add_action('pre_get_posts', function (WP_Query $q) {
    if (is_admin() || !$q->is_main_query()) return;

    $is_software = $q->is_post_type_archive('software')
        || $q->is_tax(['industry', 'sw_category', 'country'])
        || ($q->is_search() && (($_GET['post_type'] ?? '') === 'software' || (string) $q->get('post_type') === 'software'));

    // For ANY search request, default to the software CPT (we don't have a blog).
    if (!is_admin() && $q->is_search() && !$q->get('post_type')) {
        $q->set('post_type', 'software');
        $is_software = true;
    }

    if (!$is_software) return;
    $q->set('posts_per_page', 12);

    /* ---- Build meta_query from GET parameters ---- */
    $mq = ['relation' => 'AND'];

    if (!empty($_GET['mobile'])) {
        $mq[] = ['key' => 'has_mobile_app', 'value' => '1', 'compare' => '='];
    }
    if (!empty($_GET['api'])) {
        $mq[] = ['key' => 'has_api', 'value' => '1', 'compare' => '='];
    }
    if (!empty($_GET['trial'])) {
        $mq[] = ['key' => 'free_trial', 'value' => '1', 'compare' => '='];
    }
    if (!empty($_GET['min_rating'])) {
        $mq[] = ['key' => 'rating', 'value' => floatval($_GET['min_rating']), 'type' => 'NUMERIC', 'compare' => '>='];
    }
    if (!empty($_GET['deployment'])) {
        $mq[] = ['key' => 'deployment', 'value' => sanitize_text_field($_GET['deployment']), 'compare' => 'LIKE'];
    }
    if (!empty($_GET['pricing_model'])) {
        $mq[] = ['key' => 'pricing_model', 'value' => sanitize_text_field($_GET['pricing_model']), 'compare' => 'LIKE'];
    }
    if (count($mq) > 1) $q->set('meta_query', $mq);

    /* ---- Build tax_query from GET parameters ---- */
    $tq = ['relation' => 'AND'];
    foreach (['industry', 'sw_category', 'country'] as $taxonomy) {
        if (!empty($_GET[$taxonomy])) {
            $vals = array_filter(array_map('sanitize_title', (array) $_GET[$taxonomy]));
            if ($vals) {
                $tq[] = ['taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $vals];
            }
        }
    }
    if (count($tq) > 1) {
        // Merge with any existing tax_query (e.g. on a taxonomy page)
        $existing = $q->get('tax_query');
        if ($existing) {
            $tq = array_merge($tq, is_array($existing) ? $existing : [$existing]);
        }
        $q->set('tax_query', $tq);
    }

    /* ---- Sort ---- */
    $sort = $_GET['orderby'] ?? '';
    if ($sort === 'rating') {
        $q->set('meta_key', 'rating');
        $q->set('orderby', 'meta_value_num');
        $q->set('order', 'DESC');
    } elseif ($sort === 'reviews') {
        $q->set('meta_key', 'review_count');
        $q->set('orderby', 'meta_value_num');
        $q->set('order', 'DESC');
    } elseif ($sort === 'price_low') {
        $q->set('meta_key', 'price');
        $q->set('orderby', 'meta_value');
        $q->set('order', 'ASC');
    } elseif ($sort === 'date') {
        $q->set('orderby', 'date');
        $q->set('order', 'DESC');
    }
});

/* Extend WP search to also match selected meta fields (vendor, features).
 * Approach: pre-fetch IDs whose postmeta matches the term, then OR them
 * into WP's default search clause cleanly via 'posts_search'. */
add_filter('posts_search', function ($search, WP_Query $q) {
    if (is_admin() || !$q->is_main_query() || !$q->is_search()) return $search;
    if ((string) $q->get('post_type') !== 'software') return $search;
    $term = (string) $q->get('s');
    if ($term === '') return $search;

    global $wpdb;
    $like = '%' . $wpdb->esc_like($term) . '%';

    $ids = $wpdb->get_col($wpdb->prepare(
        "SELECT DISTINCT post_id FROM {$wpdb->postmeta}
          WHERE meta_key IN ('vendor','included_features','tagline','verdict','best_fit_for','headquarters','deployment','price','pricing_model','company_size','languages')
            AND meta_value LIKE %s",
        $like
    ));
    if (empty($ids)) return $search;

    $ids_in = implode(',', array_map('intval', $ids));
    // WP's search starts with " AND (" — we wrap that whole clause in another
    // OR with our IN(...) list. Simpler than regex surgery.
    if (strpos($search, ' AND (') === 0) {
        $original = substr($search, strlen(' AND ('));            // strip leading " AND ("
        // The original ends with ")"; reuse it as the right side of an OR
        $search = " AND ( {$wpdb->posts}.ID IN ($ids_in) OR ($original";
    }
    return $search;
}, 10, 2);

/* WP also adds an "ORDER BY title-LIKE" clause in search; if our IN()
 * push above produces a malformed ORDER BY in some configurations, this
 * filter strips that ordering and uses the WP_Query orderby instead. */
add_filter('posts_search_orderby', function ($orderby, WP_Query $q) {
    if (is_admin() || !$q->is_main_query() || !$q->is_search()) return $orderby;
    if ((string) $q->get('post_type') !== 'software') return $orderby;
    return ''; // let the main query's orderby apply
}, 10, 2);
