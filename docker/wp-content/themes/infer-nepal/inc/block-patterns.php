<?php
/**
 * Block patterns — the "lightweight builder" surface.
 *
 * Site editors can insert these via the "Patterns" tab in the block inserter
 * (in any page or post) and edit text inline. The patterns use ONLY core
 * Gutenberg blocks (heading, paragraph, group, columns, button, html) styled
 * by the theme's CSS classes — no third-party page-builder plugin needed.
 */

if (!defined('ABSPATH')) { exit; }

// Custom category so patterns appear under "Infer Nepal" in the inserter.
// WordPress auto-discovers pattern files inside the theme's /patterns/
// directory (each file just needs a header comment with Title + Slug),
// so we don't manually register them here.
add_action('init', function () {
    if (function_exists('register_block_pattern_category')) {
        register_block_pattern_category('infer-nepal', [
            'label' => __('Infer Nepal', 'infer-nepal'),
        ]);
    }
});

/* =================================================================
 * Lightweight server-rendered "blocks" via shortcodes — useful inside
 * patterns when admins want LIVE data (top-rated software, industry
 * tiles, etc.) without needing to maintain it manually.
 * ================================================================= */

add_action('init', function () {

    /* [inp_top_software count="6" industry="" category=""] */
    add_shortcode('inp_top_software', function ($atts) {
        $atts = shortcode_atts([
            'count'    => 6,
            'industry' => '',
            'category' => '',
            'orderby'  => 'rating', // rating | reviews | recent
        ], $atts, 'inp_top_software');

        $args = [
            'post_type'      => 'software',
            'posts_per_page' => (int) $atts['count'],
            'meta_query'     => [],
        ];

        if ($atts['industry']) {
            $args['tax_query'][] = ['taxonomy' => 'industry', 'field' => 'slug', 'terms' => explode(',', $atts['industry'])];
        }
        if ($atts['category']) {
            $args['tax_query'][] = ['taxonomy' => 'sw_category', 'field' => 'slug', 'terms' => explode(',', $atts['category'])];
        }
        if ($atts['orderby'] === 'rating') {
            $args['meta_key'] = 'rating';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
        } elseif ($atts['orderby'] === 'reviews') {
            $args['meta_key'] = 'review_count';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
        } else {
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
        }

        $q = new WP_Query($args);
        if (!$q->have_posts()) return '<p>No software found.</p>';

        ob_start();
        echo '<div class="card-grid" style="grid-template-columns: repeat(3, 1fr);">';
        while ($q->have_posts()) { $q->the_post(); inp_render_software_card(get_the_ID()); }
        echo '</div>';
        wp_reset_postdata();
        return ob_get_clean();
    });

    /* [inp_industry_grid count="10"] */
    add_shortcode('inp_industry_grid', function ($atts) {
        $atts = shortcode_atts(['count' => 10], $atts, 'inp_industry_grid');
        $terms = get_terms(['taxonomy' => 'industry', 'hide_empty' => false, 'number' => (int) $atts['count']]);
        if (is_wp_error($terms) || empty($terms)) return '';
        ob_start();
        echo '<div class="cat-grid" style="grid-template-columns: repeat(5, 1fr);">';
        foreach ($terms as $term) inp_render_industry_card($term);
        echo '</div>';
        return ob_get_clean();
    });

    /* [inp_software_list count="8" industry="school-college" featured_first="1"] */
    add_shortcode('inp_software_list', function ($atts) {
        $atts = shortcode_atts([
            'count'          => 8,
            'industry'       => '',
            'category'       => '',
            'featured_first' => 1,
        ], $atts, 'inp_software_list');

        $args = [
            'post_type'      => 'software',
            'posts_per_page' => (int) $atts['count'],
            'meta_key'       => 'rating',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
        ];
        if ($atts['industry']) {
            $args['tax_query'][] = ['taxonomy' => 'industry', 'field' => 'slug', 'terms' => explode(',', $atts['industry'])];
        }
        if ($atts['category']) {
            $args['tax_query'][] = ['taxonomy' => 'sw_category', 'field' => 'slug', 'terms' => explode(',', $atts['category'])];
        }
        $q = new WP_Query($args);
        if (!$q->have_posts()) return '<p>No software found.</p>';
        ob_start();
        echo '<div style="display:grid; gap:14px;">';
        $i = 0;
        while ($q->have_posts()) { $q->the_post();
            inp_render_software_row(get_the_ID(), ($i === 0 && (int) $atts['featured_first'] === 1));
            $i++;
        }
        echo '</div>';
        wp_reset_postdata();
        return ob_get_clean();
    });

    /* [inp_vendor_stats] — pulls numbers from Customizer */
    add_shortcode('inp_vendor_stats', function () {
        $stats = [
            ['inp_stat_buyers',   'Monthly buyers'],
            ['inp_stat_demos',    'Demo requests / mo'],
            ['inp_stat_reviews',  'Verified reviews'],
            ['inp_stat_listings', 'Software listed'],
        ];
        ob_start(); ?>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <?php foreach ($stats as [$key, $label]): ?>
          <div style="background: rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius: 12px; padding: 16px;">
            <div style="font-size: 28px; font-weight: 800; color:#fff;"><?= esc_html(get_theme_mod($key, '—')) ?></div>
            <div style="font-size: 12px; color: rgba(255,255,255,.7);"><?= esc_html($label) ?></div>
          </div>
        <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    });
});
