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

    /* [inp_nav] — renders the primary nav (brand + mega menu + actions).
     * Used inside parts/header.html so block-template pages get the same
     * mega-menu nav as PHP templates that call get_header(). */
    add_shortcode('inp_nav', function () {
        ob_start(); ?>
        <div class="nav-wrap"><div class="container">
          <nav class="primary">
            <a href="<?= esc_url(home_url('/')) ?>" class="brand">
              <?php if (has_custom_logo()) : the_custom_logo(); else : ?>
                <img src="<?= esc_url(get_theme_file_uri('assets/logo.png')) ?>" alt="<?php bloginfo('name'); ?>" class="brand-logo" />
              <?php endif; ?>
            </a>
            <button class="menu-toggle" aria-label="Open menu">
              <svg class="icon-bars" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
              <svg class="icon-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
            <div class="nav-links" id="primary-nav-links">
              <?php
              if (has_nav_menu('primary')) {
                  wp_nav_menu([
                      'theme_location' => 'primary',
                      'container'      => false,
                      'items_wrap'     => '%3$s',
                      'walker'         => new INP_Mega_Walker(),
                      'depth'          => 2,
                      'fallback_cb'    => false,
                  ]);
              } else {
                  inp_render_default_nav();
              }
              ?>
            </div>
            <button class="search-trigger" aria-label="Open search">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
              <span class="label">Search ERP, school, hotel, accounting…</span>
              <span class="kbd">⌘K</span>
            </button>
            <div class="nav-actions">
              <button class="icon-btn" id="themeBtn" aria-label="Toggle theme">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
              </button>
              <a href="<?= esc_url(wp_login_url()) ?>" class="btn ghost">Sign in</a>
            </div>
          </nav>
        </div></div>
        <?php
        return ob_get_clean();
    });

    /* [inp_footer] — full editable footer (uses Customizer + nav menus) */
    add_shortcode('inp_footer', function () {
        ob_start(); ?>
        <footer class="site"><div class="container">
          <div class="f-grid">
            <div>
              <a href="<?= esc_url(home_url('/')) ?>" class="brand">
                <?php if (has_custom_logo()) : the_custom_logo(); else : ?>
                  <img src="<?= esc_url(get_theme_file_uri('assets/logo.png')) ?>" alt="<?php bloginfo('name'); ?>" class="brand-logo" />
                <?php endif; ?>
              </a>
              <p style="color:var(--text-muted); font-size:13px; margin-top:12px; max-width:320px;">
                <?= esc_html(get_theme_mod('inp_footer_blurb', "Nepal's independent B2B software discovery platform.")) ?>
              </p>
              <form class="newsletter" action="#" method="post">
                <input name="email" placeholder="you@company.com" />
                <button type="submit">Subscribe</button>
              </form>
            </div>
            <?php
            $cols = [
                'footer-industries' => 'Industries',
                'footer-categories' => 'Categories',
                'footer-vendors'    => 'For vendors',
                'footer-company'    => 'Company',
            ];
            foreach ($cols as $loc => $heading) {
                echo '<div><h5>' . esc_html($heading) . '</h5>';
                if (has_nav_menu($loc)) {
                    wp_nav_menu(['theme_location'=>$loc,'container'=>false,'depth'=>1,'items_wrap'=>'<ul>%3$s</ul>']);
                } else {
                    echo '<ul>';
                    if ($loc === 'footer-industries') {
                        foreach (get_terms(['taxonomy'=>'industry','hide_empty'=>false,'number'=>8]) as $t)
                            echo '<li><a href="'.esc_url(get_term_link($t)).'">'.esc_html($t->name).'</a></li>';
                    } elseif ($loc === 'footer-categories') {
                        foreach (get_terms(['taxonomy'=>'sw_category','hide_empty'=>false,'number'=>8]) as $t)
                            echo '<li><a href="'.esc_url(get_term_link($t)).'">'.esc_html($t->name).'</a></li>';
                    } elseif ($loc === 'footer-vendors') {
                        echo '<li><a href="#">List your software</a></li><li><a href="#">Premium listing</a></li><li><a href="#">Lead generation</a></li>';
                    } else {
                        echo '<li><a href="'.esc_url(home_url('/about-infer-nepal/')).'">About</a></li><li><a href="'.esc_url(home_url('/contact/')).'">Contact</a></li><li><a href="#">How we score</a></li>';
                    }
                    echo '</ul>';
                }
                echo '</div>';
            }
            ?>
          </div>
          <div class="f-bottom">
            <span><?= wp_kses_post(get_theme_mod('inp_footer_copyright', '© ' . date('Y') . ' Infer Nepal · infernepal.com')) ?></span>
            <span><?= esc_html(get_theme_mod('inp_footer_tagline', 'Made in Kathmandu 🇳🇵')) ?></span>
          </div>
        </div></footer>
        <div class="menu-backdrop" aria-hidden="true"></div>
        <?php
        return ob_get_clean();
    });

    /* [inp_promo_strip] — top promotional strip from Customizer */
    add_shortcode('inp_promo_strip', function () {
        if (!get_theme_mod('inp_promo_enabled', true)) return '';
        ob_start(); ?>
        <div class="promo-strip">
          <a href="<?= esc_url(get_theme_mod('inp_promo_cta_url', '#')) ?>">
            <span class="live-dot"></span>
            <span class="tag"><?= esc_html(get_theme_mod('inp_promo_tag', 'New')) ?></span>
            <span class="msg"><?= wp_kses_post(get_theme_mod('inp_promo_message', 'Top 10 ERP for Nepali SMBs — 2026 report.')) ?></span>
            <span class="cta"><?= esc_html(get_theme_mod('inp_promo_cta_text', 'Read the report')) ?>
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
            </span>
          </a>
        </div>
        <?php
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
