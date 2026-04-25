<?php
/**
 * Custom Gutenberg blocks for Infer Nepal.
 *
 * All blocks are server-side rendered (call our existing render helpers),
 * and have an editor-side script that:
 *   - registers the block in the inserter
 *   - shows InspectorControls (industry/category/count/sort dropdowns)
 *   - uses ServerSideRender to live-preview the block in the editor
 *
 * No JS build step required — uses inline createElement (no JSX).
 */

if (!defined('ABSPATH')) { exit; }

/* ------------------------------------------------------------------
 * Custom block category so our blocks group together in the inserter
 * ------------------------------------------------------------------ */
add_filter('block_categories_all', function ($categories) {
    array_unshift($categories, [
        'slug'  => 'infer-nepal',
        'title' => 'Infer Nepal',
        'icon'  => 'screenoptions',
    ]);
    return $categories;
});

/* ------------------------------------------------------------------
 * Editor JS — registers the blocks in the inserter and gives them
 * sidebar controls + live ServerSideRender preview.
 * ------------------------------------------------------------------ */
add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_script(
        'inp-blocks',
        get_theme_file_uri('assets/blocks.js'),
        ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n'],
        INP_THEME_VER,
        true
    );

    // Provide industry & category lists to the editor so the dropdowns are live
    $industries = array_map(fn($t) => ['label' => $t->name, 'value' => $t->slug],
        get_terms(['taxonomy' => 'industry', 'hide_empty' => false]));
    $categories = array_map(fn($t) => ['label' => $t->name, 'value' => $t->slug],
        get_terms(['taxonomy' => 'sw_category', 'hide_empty' => false]));

    array_unshift($industries, ['label' => '— Any industry —', 'value' => '']);
    array_unshift($categories, ['label' => '— Any category —', 'value' => '']);

    wp_localize_script('inp-blocks', 'inpBlockData', [
        'industries' => array_values($industries),
        'categories' => array_values($categories),
    ]);
});

/* ------------------------------------------------------------------
 * Block: Software List (cards or rows, filtered by industry/category)
 * ------------------------------------------------------------------ */
add_action('init', function () {
    register_block_type('infer-nepal/software-list', [
        'api_version'     => 2,
        'title'           => 'Software list (live)',
        'category'        => 'infer-nepal',
        'icon'            => 'list-view',
        'description'     => 'Auto-populated list of software, optionally filtered by industry / category.',
        'attributes'      => [
            'industry'       => ['type' => 'string', 'default' => ''],
            'category'       => ['type' => 'string', 'default' => ''],
            'count'          => ['type' => 'number', 'default' => 6],
            'orderby'        => ['type' => 'string', 'default' => 'rating'],
            'display'        => ['type' => 'string', 'default' => 'cards'],   // cards | rows
            'featured_first' => ['type' => 'boolean', 'default' => true],
        ],
        'render_callback' => function ($attrs) {
            $atts = wp_parse_args($attrs, [
                'industry' => '', 'category' => '', 'count' => 6,
                'orderby' => 'rating', 'display' => 'cards', 'featured_first' => true,
            ]);
            $args = [
                'post_type'      => 'software',
                'posts_per_page' => max(1, (int) $atts['count']),
                'no_found_rows'  => true,
            ];
            $tax = [];
            if ($atts['industry']) $tax[] = ['taxonomy' => 'industry',    'field' => 'slug', 'terms' => $atts['industry']];
            if ($atts['category']) $tax[] = ['taxonomy' => 'sw_category', 'field' => 'slug', 'terms' => $atts['category']];
            if ($tax) $args['tax_query'] = $tax;
            if ($atts['orderby'] === 'rating') {
                $args['meta_key'] = 'rating'; $args['orderby'] = 'meta_value_num'; $args['order'] = 'DESC';
            } elseif ($atts['orderby'] === 'reviews') {
                $args['meta_key'] = 'review_count'; $args['orderby'] = 'meta_value_num'; $args['order'] = 'DESC';
            } else {
                $args['orderby'] = 'date'; $args['order'] = 'DESC';
            }
            $q = new WP_Query($args);
            if (!$q->have_posts()) {
                return '<p style="color:var(--text-muted); text-align:center; padding: 24px;">No software found for these filters.</p>';
            }
            ob_start();
            if ($atts['display'] === 'cards') {
                echo '<div class="card-grid" style="grid-template-columns: repeat(3, 1fr);">';
                while ($q->have_posts()) { $q->the_post(); inp_render_software_card(get_the_ID()); }
                echo '</div>';
            } else {
                echo '<div style="display:grid; gap:14px;">';
                $i = 0;
                while ($q->have_posts()) { $q->the_post();
                    inp_render_software_row(get_the_ID(), ($i === 0 && $atts['featured_first']));
                    $i++;
                }
                echo '</div>';
            }
            wp_reset_postdata();
            return ob_get_clean();
        },
    ]);

    /* ------------------------------------------------------------------
     * Block: Industry Grid (auto-pulls all industry taxonomy terms)
     * ------------------------------------------------------------------ */
    register_block_type('infer-nepal/industry-grid', [
        'api_version'     => 2,
        'title'           => 'Industry tile grid (live)',
        'category'        => 'infer-nepal',
        'icon'            => 'grid-view',
        'description'     => 'Auto-populated grid of industry tiles.',
        'attributes'      => [
            'count'   => ['type' => 'number', 'default' => 10],
            'columns' => ['type' => 'number', 'default' => 5],
        ],
        'render_callback' => function ($attrs) {
            $atts = wp_parse_args($attrs, ['count' => 10, 'columns' => 5]);
            $terms = get_terms(['taxonomy' => 'industry', 'hide_empty' => false, 'number' => (int) $atts['count']]);
            if (is_wp_error($terms) || empty($terms)) return '';
            ob_start();
            printf('<div class="cat-grid" style="grid-template-columns: repeat(%d, 1fr);">', max(1, (int) $atts['columns']));
            foreach ($terms as $term) inp_render_industry_card($term);
            echo '</div>';
            return ob_get_clean();
        },
    ]);

    /* ------------------------------------------------------------------
     * Block: Vendor Stats (4 stat cards from Customizer)
     * ------------------------------------------------------------------ */
    register_block_type('infer-nepal/vendor-stats', [
        'api_version'     => 2,
        'title'           => 'Vendor stats (4 cards)',
        'category'        => 'infer-nepal',
        'icon'            => 'chart-bar',
        'render_callback' => function () { return do_shortcode('[inp_vendor_stats]'); },
    ]);

    /* ------------------------------------------------------------------
     * Block: Section heading (with sec-head structure)
     * ------------------------------------------------------------------ */
    register_block_type('infer-nepal/section-heading', [
        'api_version'     => 2,
        'title'           => 'Section heading',
        'category'        => 'infer-nepal',
        'icon'            => 'heading',
        'attributes'      => [
            'title'    => ['type' => 'string', 'default' => 'Top-rated software'],
            'subtitle' => ['type' => 'string', 'default' => 'Most reviewed by Nepali buyers this quarter.'],
            'linkText' => ['type' => 'string', 'default' => 'View all →'],
            'linkUrl'  => ['type' => 'string', 'default' => '/software/'],
        ],
        'render_callback' => function ($attrs) {
            $atts = wp_parse_args($attrs, ['title' => '', 'subtitle' => '', 'linkText' => '', 'linkUrl' => '']);
            ob_start(); ?>
            <div class="container"><div class="sec-head">
              <div>
                <h2><?= esc_html($atts['title']) ?></h2>
                <?php if ($atts['subtitle']): ?><p><?= esc_html($atts['subtitle']) ?></p><?php endif; ?>
              </div>
              <?php if ($atts['linkText']): ?>
                <a href="<?= esc_url($atts['linkUrl']) ?>" class="more"><?= esc_html($atts['linkText']) ?></a>
              <?php endif; ?>
            </div></div>
            <?php
            return ob_get_clean();
        },
    ]);
});
