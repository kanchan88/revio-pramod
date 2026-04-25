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

/* ------------------------------------------------------------------
 * Output customizer brand colors as CSS variables
 * ------------------------------------------------------------------ */
add_action('wp_head', function () {
    $brand     = get_theme_mod('inp_color_brand', '#0A7AA9');
    $brandDeep = get_theme_mod('inp_color_brand_deep', '#066189');
    $ink       = get_theme_mod('inp_color_ink', '#333F4B');
    echo "<style id='inp-customizer-vars'>:root{--accent:{$brand};--accent-deep:{$brandDeep};--accent-ink:{$brand};--ink:{$ink};}</style>\n";
}, 99);
