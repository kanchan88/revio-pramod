<?php
/**
 * Theme Customizer — non-developer settings (Appearance → Customize).
 *
 * Adds panels for:
 *   - Brand (logo + colors)
 *   - Header / Promo strip
 *   - Footer
 *   - Vendor stats (numbers shown in the "List your software" panel)
 */

if (!defined('ABSPATH')) { exit; }

add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

    /* ------------------------------------------------------------------
     * BRAND panel
     * ------------------------------------------------------------------ */
    $wp_customize->add_panel('inp_brand', [
        'title'    => __('Infer Nepal — Brand', 'infer-nepal'),
        'priority' => 30,
    ]);

    $wp_customize->add_section('inp_colors', [
        'title' => __('Brand colors', 'infer-nepal'),
        'panel' => 'inp_brand',
    ]);

    $colors = [
        'inp_color_brand'      => ['Primary brand', '#0A7AA9'],
        'inp_color_brand_deep' => ['Brand deep',    '#066189'],
        'inp_color_ink'        => ['Ink (charcoal)','#333F4B'],
    ];
    foreach ($colors as $id => [$label, $default]) {
        $wp_customize->add_setting($id, [
            'default'           => $default,
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'refresh',
        ]);
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $id, [
            'label'   => $label,
            'section' => 'inp_colors',
        ]));
    }

    /* ------------------------------------------------------------------
     * PROMO STRIP
     * ------------------------------------------------------------------ */
    $wp_customize->add_section('inp_promo', [
        'title' => __('Promo strip (top bar)', 'infer-nepal'),
        'panel' => 'inp_brand',
    ]);

    $wp_customize->add_setting('inp_promo_enabled', [
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ]);
    $wp_customize->add_control('inp_promo_enabled', [
        'type'    => 'checkbox',
        'label'   => __('Show promo strip', 'infer-nepal'),
        'section' => 'inp_promo',
    ]);

    $wp_customize->add_setting('inp_promo_tag', ['default' => 'New', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('inp_promo_tag', ['type'=>'text', 'label'=>'Tag', 'section'=>'inp_promo']);

    $wp_customize->add_setting('inp_promo_message', [
        'default' => 'Top 10 ERP for Nepali SMBs — 2026 report. Benchmarked across features, deployment & TCO.',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('inp_promo_message', ['type'=>'textarea', 'label'=>'Message', 'section'=>'inp_promo']);

    $wp_customize->add_setting('inp_promo_cta_text', ['default'=>'Read the report', 'sanitize_callback'=>'sanitize_text_field']);
    $wp_customize->add_control('inp_promo_cta_text', ['type'=>'text', 'label'=>'CTA text', 'section'=>'inp_promo']);

    $wp_customize->add_setting('inp_promo_cta_url', ['default'=>'#', 'sanitize_callback'=>'esc_url_raw']);
    $wp_customize->add_control('inp_promo_cta_url', ['type'=>'url', 'label'=>'CTA URL', 'section'=>'inp_promo']);

    /* ------------------------------------------------------------------
     * FOOTER
     * ------------------------------------------------------------------ */
    $wp_customize->add_section('inp_footer', [
        'title' => __('Footer', 'infer-nepal'),
        'panel' => 'inp_brand',
    ]);

    $wp_customize->add_setting('inp_footer_blurb', [
        'default' => "Nepal's independent B2B software discovery platform. Verified reviews, transparent pricing, unbiased editor scores. No pay-to-play rankings.",
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('inp_footer_blurb', ['type'=>'textarea', 'label'=>'Footer blurb', 'section'=>'inp_footer']);

    $wp_customize->add_setting('inp_footer_copyright', [
        'default' => '© 2026 Infer Nepal · infernepal.com',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('inp_footer_copyright', ['type'=>'text', 'label'=>'Copyright', 'section'=>'inp_footer']);

    $wp_customize->add_setting('inp_footer_tagline', [
        'default' => 'Made in Kathmandu 🇳🇵',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('inp_footer_tagline', ['type'=>'text', 'label'=>'Footer tagline', 'section'=>'inp_footer']);

    /* ------------------------------------------------------------------
     * VENDOR STATS (used in the "List your software" panel)
     * ------------------------------------------------------------------ */
    $wp_customize->add_section('inp_stats', [
        'title' => __('Platform stats', 'infer-nepal'),
        'panel' => 'inp_brand',
        'description' => 'Numbers shown on the home page "vendor" panel.',
    ]);

    foreach ([
        'inp_stat_buyers'   => ['Monthly buyers', '38K+'],
        'inp_stat_demos'    => ['Demo requests / month', '2,800'],
        'inp_stat_reviews'  => ['Verified reviews', '18.4K'],
        'inp_stat_listings' => ['Software listed', '642'],
    ] as $id => [$label, $default]) {
        $wp_customize->add_setting($id, ['default' => $default, 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control($id, ['type' => 'text', 'label' => $label, 'section' => 'inp_stats']);
    }
});

/* ==================================================================
 * SITE COPY — every hard-coded string in the templates exposed here
 * so admins can edit them without touching code.
 *
 * Use inp_t('key', 'fallback default') in templates to read a value.
 * ================================================================== */
add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

    $wp_customize->add_panel('inp_copy', [
        'title'    => __('Site copy', 'infer-nepal'),
        'priority' => 35,
        'description' => 'Edit every section heading, button label, and helper text used across the site.',
    ]);

    // Each entry: section_id => [Section Title, [setting_key => [Label, default]]]
    $copy_sections = [
        'inp_copy_header' => ['Header & search', [
            'header_search_placeholder' => ['Search box placeholder', 'Search ERP, school, hotel, accounting…'],
            'header_signin_label'       => ['Sign-in button label',  'Sign in'],
        ]],
        'inp_copy_footer' => ['Footer', [
            'footer_h_industries'       => ['Column 1 heading', 'Industries'],
            'footer_h_categories'       => ['Column 2 heading', 'Categories'],
            'footer_h_vendors'          => ['Column 3 heading', 'For vendors'],
            'footer_h_company'          => ['Column 4 heading', 'Company'],
            'footer_newsletter_label'   => ['Newsletter button label', 'Subscribe'],
            'footer_newsletter_blurb'   => ['Newsletter hint',         'One short digest every Friday. Nepali SMB software news.'],
            'footer_newsletter_placeholder' => ['Newsletter placeholder', 'you@company.com'],
        ]],
        'inp_copy_product' => ['Software detail page', [
            'p_subnav_overview'      => ['Sticky subnav: Overview',     'Overview'],
            'p_subnav_features'      => ['Sticky subnav: Features',     'Features'],
            'p_subnav_pricing'       => ['Sticky subnav: Pricing',      'Pricing'],
            'p_subnav_reviews'       => ['Sticky subnav: Reviews',      'Reviews'],
            'p_subnav_alternatives'  => ['Sticky subnav: Alternatives', 'Alternatives'],
            'p_cta_demo'             => ['CTA: Get free demo',          'Get free demo'],
            'p_cta_pricing'          => ['CTA: See pricing',            'See pricing'],
            'p_cta_website'          => ['CTA: Visit website',          'Visit official website ↗'],
            'p_meta_line'            => ['Trust line below CTAs',       'No spam · 2,800+ businesses request demos here every month'],

            'p_tab_overview'         => ['Tab: Overview',     'Overview'],
            'p_tab_features'         => ['Tab: Features',     'Features'],
            'p_tab_pricing'          => ['Tab: Pricing',      'Pricing'],
            'p_tab_reviews'          => ['Tab: Reviews',      'Reviews'],
            'p_tab_specifications'   => ['Tab: Specifications','Specifications'],
            'p_tab_alternatives'     => ['Tab: Alternatives', 'Alternatives'],
            'p_tab_faq'              => ['Tab: FAQ',          'FAQ'],

            'p_h_verdict'            => ['Heading: Verdict',           'Why we recommend %s'],
            'p_h_best_fit'           => ['Heading: Best fit for',      'Best fit for'],
            'p_h_look_elsewhere'     => ['Heading: Look elsewhere',    'Look elsewhere if'],
            'p_h_awards'             => ['Heading: Awards',            'Recognised in'],
            'p_h_industries'         => ['Heading: Industries served', 'Industries it serves'],
            'p_h_proscons'           => ['Heading: Pros & cons',       'Pros & cons at a glance'],
            'p_h_pros'               => ['Heading: Pros column',       'What buyers love'],
            'p_h_cons'               => ['Heading: Cons column',       'Common complaints'],
            'p_h_features'           => ['Heading: Features',          'Features included'],
            'p_h_features_sub'       => ['Sub-heading: Features',      'Modules and capabilities that ship out of the box.'],
            'p_h_not_included'       => ['Heading: Not included',      'Not included (you\'ll need add-ons)'],
            'p_h_pricing'            => ['Heading: Pricing',           'Pricing & plans'],
            'p_h_pricing_sub'        => ['Sub-heading: Pricing',       '%s · pricing in Nepali Rupees where applicable.'],
            'p_h_reviews'            => ['Heading: Reviews',           'User reviews'],
            'p_h_specs'              => ['Heading: Specifications',    'Specifications'],
            'p_h_alternatives'       => ['Heading: Alternatives',      'Top alternatives to %s'],
            'p_h_alternatives_sub'   => ['Sub-heading: Alternatives',  'Compared on rating, deployment, country of origin and entry pricing.'],
            'p_h_faq'                => ['Heading: FAQ',               'Frequently asked questions'],

            'p_demo_h'               => ['Demo form: Heading',         'Get a free %s demo'],
            'p_demo_lede'            => ['Demo form: Sub-line',        'A certified partner near you will reach out within 24 hours. No obligation.'],
            'p_demo_lbl_name'        => ['Demo form: Name label',      'Your name'],
            'p_demo_lbl_email'       => ['Demo form: Email label',     'Work email'],
            'p_demo_lbl_phone'       => ['Demo form: Phone label',     'Phone'],
            'p_demo_lbl_size'        => ['Demo form: Size label',      'Company size'],
            'p_demo_lbl_industry'    => ['Demo form: Industry label',  'Industry'],
            'p_demo_btn'             => ['Demo form: Submit button',   'Request demo →'],
            'p_demo_trust'           => ['Demo form: Trust line',      '🔒 Your details are shared only with the certified partner you select.'],

            'p_vendor_h'             => ['Sidebar: Vendor heading',    'Vendor at a glance'],
            'p_toc_h'                => ['Sidebar: TOC heading',       'On this page'],
            'p_score_h'              => ['Sidebar: Methodology heading', 'How we score'],
            'p_score_body'           => ['Sidebar: Methodology body',  'Reviews are verified via work email + invoice. Editor scores combine user reviews with hands-on feature tests.'],
            'p_score_link'           => ['Sidebar: Methodology link',  'Read methodology →'],

            'p_show_all_reviews'     => ['Reviews: "Show all" button', 'Show all %s reviews'],
            'p_write_review'         => ['Reviews: Write button',      'Write a review'],

            'p_score_easy'           => ['Score row: Ease of use',     'Ease of use'],
            'p_score_value'          => ['Score row: Value for money', 'Value for money'],
            'p_score_support'        => ['Score row: Customer support','Customer support'],
            'p_score_features'       => ['Score row: Features',        'Features'],
        ]],
        'inp_copy_archive' => ['Archive / category page', [
            'a_h_industry'           => ['Filter heading: Industry',         'Industry'],
            'a_h_category'           => ['Filter heading: Category',         'Category'],
            'a_h_country'            => ['Filter heading: Country of origin','Country of origin'],
            'a_h_deployment'         => ['Filter heading: Deployment',       'Deployment'],
            'a_h_pricing'            => ['Filter heading: Pricing model',    'Pricing model'],
            'a_h_mobapi'             => ['Filter heading: Mobile & API',     'Mobile & API'],
            'a_h_score'              => ['Filter heading: Review score',     'Review score'],
            'a_apply_btn'            => ['Filter button: Apply',             'Apply filters'],
            'a_reset_btn'            => ['Filter button: Reset',             'Reset'],
            'a_active_label'         => ['Filter chip: Active label',        'Active:'],
            'a_no_filters'           => ['Filter chip: No filters text',     'No filters · showing all'],
            'a_sort_label'           => ['Sort label',                       'Sort:'],
            'a_results_count'        => ['Results count format',             'Showing %1$s of %2$s matching software'],
            'a_no_results_h'         => ['No results heading',               'No software matches these filters.'],
            'a_no_results_b'         => ['No results body',                  'Try removing a filter, or'],
            'a_no_results_btn'       => ['No results link',                  'reset all'],
            'a_buyer_eyebrow'        => ['Buyer guide: Eyebrow',             'Free buyer\'s guide'],
            'a_buyer_h'              => ['Buyer guide: Heading',             '12 questions every Nepali buyer should ask before signing.'],
            'a_buyer_sub'            => ['Buyer guide: Sub-line',            'Used by 1,400+ admins · 4-page PDF · no email required.'],
            'a_buyer_btn'            => ['Buyer guide: Button',              'Download PDF →'],
            'a_hero_btn1'            => ['Hero button 1 (left)',             'Download buyer\'s guide'],
            'a_hero_btn2'            => ['Hero button 2 (right)',            'Get free shortlist'],
        ]],
    ];

    foreach ($copy_sections as $section_id => [$title, $fields]) {
        $wp_customize->add_section($section_id, [
            'title' => $title,
            'panel' => 'inp_copy',
        ]);
        foreach ($fields as $key => [$label, $default]) {
            $wp_customize->add_setting($key, [
                'default'           => $default,
                'sanitize_callback' => 'sanitize_text_field',
                'transport'         => 'refresh',
            ]);
            $wp_customize->add_control($key, [
                'type'    => strlen($default) > 60 ? 'textarea' : 'text',
                'label'   => $label,
                'section' => $section_id,
            ]);
        }
    }
});

/**
 * Helper used by templates to fetch a Site-copy string with fallback.
 * Use sprintf-style %s placeholders for product/category names etc.
 */
function inp_t($key, $default = '') {
    $val = get_theme_mod($key, '');
    if ($val === '' || $val === null) return $default;
    return $val;
}

/* ------------------------------------------------------------------
 * Output customizer brand colors as CSS variables
 * ------------------------------------------------------------------ */
add_action('wp_head', function () {
    $brand     = get_theme_mod('inp_color_brand', '#0A7AA9');
    $brandDeep = get_theme_mod('inp_color_brand_deep', '#066189');
    $ink       = get_theme_mod('inp_color_ink', '#333F4B');
    echo "<style id='inp-customizer-vars'>:root{--accent:{$brand};--accent-deep:{$brandDeep};--accent-ink:{$brand};--ink:{$ink};}</style>\n";
}, 99);
