<?php
/**
 * Beaver Builder integration — registers our custom modules so admins can
 * drag-and-drop the same software-directory components inside any BB layout.
 *
 * Modules registered (under "Infer Nepal" category in the BB content panel):
 *   - Software List   : industry/category-filtered list (cards or rows)
 *   - Industry Grid   : auto-populated taxonomy tile grid
 *   - Vendor Stats    : 4 stat cards from Customizer
 *   - Section Heading : big title + subtitle + "see all" link
 *
 * If Beaver Builder isn't installed, this file is a no-op except for the
 * admin-notice prompting installation.
 */

if (!defined('ABSPATH')) { exit; }

/* --- Admin notice when BB Lite isn't active --- */
add_action('admin_notices', function () {
    if (class_exists('FLBuilder')) return;
    if (!current_user_can('install_plugins')) return;
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if ($screen && in_array($screen->base, ['plugins', 'plugin-install'], true)) return; // don't double-noise
    $install_url = wp_nonce_url(
        self_admin_url('update.php?action=install-plugin&plugin=beaver-builder-lite-version'),
        'install-plugin_beaver-builder-lite-version'
    );
    echo '<div class="notice notice-info"><p>';
    echo '<strong>Infer Nepal:</strong> Beaver Builder isn\'t installed yet. ';
    echo '<a href="' . esc_url($install_url) . '" class="button button-primary" style="margin-left:8px;">Install Beaver Builder Lite</a> ';
    echo '<span style="margin-left:6px; color:#666;">— or skip and use the built-in Gutenberg editor with our patterns/blocks.</span>';
    echo '</p></div>';
});

/* --- Define paths so module classes can reference assets --- */
if (!defined('INP_BB_DIR'))     define('INP_BB_DIR',  get_theme_file_path('bb-modules/'));
if (!defined('INP_BB_URL'))     define('INP_BB_URL',  get_theme_file_uri('bb-modules/'));

/* --- Helper: build BB select-options array from a taxonomy --- */
function inp_bb_term_options($taxonomy, $any_label) {
    $opts  = ['' => $any_label];
    $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
    if (is_wp_error($terms) || !is_array($terms)) return $opts;
    foreach ($terms as $t) {
        // BB sometimes filters terms into arrays — handle both shapes safely.
        $slug = is_object($t) ? ($t->slug ?? '') : (is_array($t) ? ($t['slug'] ?? '') : '');
        $name = is_object($t) ? ($t->name ?? '') : (is_array($t) ? ($t['name'] ?? '') : '');
        if ($slug !== '') {
            $opts[$slug] = $name !== '' ? $name : $slug;
        }
    }
    return $opts;
}

/* --- Register our modules once BB has loaded --- */
add_action('init', function () {
    if (!class_exists('FLBuilder')) return;
    require_once INP_BB_DIR . 'inp-software-list/inp-software-list.php';
    require_once INP_BB_DIR . 'inp-industry-grid/inp-industry-grid.php';
    require_once INP_BB_DIR . 'inp-vendor-stats/inp-vendor-stats.php';
    require_once INP_BB_DIR . 'inp-section-heading/inp-section-heading.php';
}, 5);

/* --- Add an "Infer Nepal" category to the BB content panel sidebar --- */
add_filter('fl_builder_module_categories', function ($categories) {
    if (!is_array($categories)) $categories = [];
    return array_merge(['Infer Nepal' => 'Infer Nepal'], $categories);
});

/* --- Hide BB's "Try Premium" upsells inside our admin (optional) --- */
add_filter('fl_builder_admin_settings_nav_items', '__return_empty_array');

/* --- Enable BB on every editable post type (page, post, software) ---
 * Admins can launch BB on any single Software product to design its
 * overview / intro / outro prose. The structured meta (price, rating,
 * deployment, alternatives, FAQ) still renders from CPT meta. */
add_filter('fl_builder_post_types', function ($types) {
    $types = is_array($types) ? $types : [];
    foreach (['page', 'post', 'software'] as $pt) {
        if (!in_array($pt, $types, true)) $types[] = $pt;
    }
    return $types;
});

/* --- Per-taxonomy "landing page" override.
 *     If an admin creates a Page with the EXACT title "Industry: School & College"
 *     (matching the term name), the taxonomy archive renders that page's BB
 *     content above the auto-listing. Same for sw_category / country. */
function inp_get_term_landing_page($term) {
    if (!is_object($term)) return null;
    $title  = $term->taxonomy === 'industry'    ? 'Industry: '   . $term->name
            : ($term->taxonomy === 'sw_category' ? 'Category: '   . $term->name
            : ($term->taxonomy === 'country'     ? 'Country: '    . $term->name : ''));
    if (!$title) return null;
    $page = get_page_by_title($title, OBJECT, 'page');
    return $page ? $page : null;
}

/* --- When the Home page is opened in BB for the first time, give it the
 *     standard composition of our modules. We do this by storing a flag
 *     and seeding the layout on first access. (Optional convenience.) */
add_action('wp_loaded', function () {
    if (!class_exists('FLBuilderModel')) return;
    $home_id = (int) get_option('page_on_front');
    if (!$home_id) return;
    if (get_post_meta($home_id, '_inp_bb_seeded', true) === '1') return;
    // Don't auto-seed BB layout — let the admin opt-in via "Launch Beaver Builder".
    // (Programmatic layout seeding is brittle across BB versions.)
});