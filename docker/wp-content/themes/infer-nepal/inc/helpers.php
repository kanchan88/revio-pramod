<?php
/**
 * Render helpers — kept tiny on purpose so block patterns and templates
 * can call them directly. Each function is HTML-safe (escaped where needed).
 */

if (!defined('ABSPATH')) { exit; }

/**
 * Get a software's meta + taxonomy terms in one call.
 */
function inp_get_software($post_id) {
    return [
        'id'             => $post_id,
        'title'          => get_the_title($post_id),
        'permalink'      => get_permalink($post_id),
        'excerpt'        => get_the_excerpt($post_id),
        'vendor'         => get_post_meta($post_id, 'vendor', true),
        'website_url'    => get_post_meta($post_id, 'website_url', true),
        'pricing_model'  => get_post_meta($post_id, 'pricing_model', true),
        'price'          => get_post_meta($post_id, 'price', true),
        'maturity'       => get_post_meta($post_id, 'maturity', true),
        'has_mobile_app' => (bool) get_post_meta($post_id, 'has_mobile_app', true),
        'has_api'        => (bool) get_post_meta($post_id, 'has_api', true),
        'free_trial'     => (bool) get_post_meta($post_id, 'free_trial', true),
        'deployment'     => get_post_meta($post_id, 'deployment', true),
        'company_size'   => get_post_meta($post_id, 'company_size', true),
        'rating'         => (float) get_post_meta($post_id, 'rating', true),
        'review_count'   => (int) get_post_meta($post_id, 'review_count', true),
        'logo_url'       => get_post_meta($post_id, 'logo_url', true),
        'included'       => array_filter(array_map('trim', explode(',', (string) get_post_meta($post_id, 'included_features', true)))),
        'excluded'       => array_filter(array_map('trim', explode(',', (string) get_post_meta($post_id, 'excluded_features', true)))),
        'industries'     => wp_get_post_terms($post_id, 'industry'),
        'categories'     => wp_get_post_terms($post_id, 'sw_category'),
        'country_terms'  => wp_get_post_terms($post_id, 'country'),
    ];
}

/**
 * Compact vendor logo tile. Falls back to the first 1–2 letters when no image.
 *
 * @param array  $sw   Result of inp_get_software()
 * @param string $size sm | md | lg | xl
 */
function inp_vlogo($sw, $size = 'lg') {
    $size = in_array($size, ['sm','md','lg','xl'], true) ? $size : 'lg';
    if (!empty($sw['logo_url'])) {
        printf(
            '<span class="vlogo %s has-img"><img src="%s" alt="%s"/></span>',
            esc_attr($size),
            esc_url($sw['logo_url']),
            esc_attr($sw['title'])
        );
        return;
    }
    // letter fallback — first letter of each word, max 2 chars
    $words = preg_split('/\s+/', trim((string) $sw['title']));
    $abbr = '';
    foreach ($words as $w) {
        $abbr .= mb_strtoupper(mb_substr($w, 0, 1));
        if (mb_strlen($abbr) >= 2) break;
    }
    // deterministic gradient based on title
    $hash = crc32((string) $sw['title']);
    $h1   = $hash % 360;
    $h2   = ($h1 + 24) % 360;
    $bg   = "background:linear-gradient(135deg, hsl({$h1} 65% 35%), hsl({$h2} 70% 50%));color:#fff;";
    printf(
        '<span class="vlogo %s" style="%s">%s</span>',
        esc_attr($size),
        esc_attr($bg),
        esc_html($abbr)
    );
}

/**
 * Render a country flag (best-effort — uses common ISO names).
 */
function inp_country_flag($name) {
    $map = [
        'Nepal' => '🇳🇵', 'India' => '🇮🇳', 'USA' => '🇺🇸', 'UK' => '🇬🇧',
        'Germany' => '🇩🇪', 'Belgium' => '🇧🇪', 'Australia' => '🇦🇺',
        'France' => '🇫🇷', 'Singapore' => '🇸🇬',
    ];
    return $map[$name] ?? '';
}

/**
 * Render a star rating.
 */
function inp_stars($rating) {
    $rating = max(0, min(5, (float) $rating));
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5;
    $out  = str_repeat('★', $full) . ($half ? '½' : '') . str_repeat('☆', max(0, 5 - $full - ($half ? 1 : 0)));
    return '<span class="stars" style="color:var(--star);">' . esc_html($out) . '</span>';
}

/**
 * Render a single software card (.s-card style).
 */
function inp_render_software_card($post_id) {
    $sw = inp_get_software($post_id);
    $country = !empty($sw['country_terms']) ? $sw['country_terms'][0]->name : '';
    $flag    = inp_country_flag($country);
    ?>
    <a href="<?= esc_url($sw['permalink']) ?>" class="s-card">
      <div class="head">
        <?php inp_vlogo($sw, 'lg'); ?>
        <div class="meta">
          <h4 class="name"><?= esc_html($sw['title']) ?></h4>
          <div class="vendor">
            <?= esc_html($sw['vendor']) ?>
            <?php if ($flag): ?> · <span class="flag"><?= $flag ?> <?= esc_html($country) ?></span><?php endif; ?>
          </div>
        </div>
      </div>
      <p class="desc"><?= esc_html($sw['excerpt']) ?></p>
      <?php if (!empty($sw['included'])): ?>
        <div class="feature-pills">
          <?php foreach (array_slice($sw['included'], 0, 4) as $f): ?>
            <span><?= esc_html($f) ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <div class="stats">
        <span class="rating"><span class="star">★</span> <?= esc_html(number_format($sw['rating'], 1)) ?>/5
          <small style="color:var(--text-subtle); font-weight:500;">(<?= esc_html(number_format($sw['review_count'])) ?>)</small>
        </span>
        <?php if (!empty($sw['price'])): ?>
          <span class="price"><?= esc_html($sw['price']) ?></span>
        <?php endif; ?>
      </div>
    </a>
    <?php
}

/**
 * Render a horizontal "row" software card (.s-row style — used in catalogue listings).
 */
function inp_render_software_row($post_id, $featured = false) {
    $sw = inp_get_software($post_id);
    $country = !empty($sw['country_terms']) ? $sw['country_terms'][0]->name : '';
    $flag    = inp_country_flag($country);
    $extra   = $featured ? ' style="border-color: var(--accent); background: linear-gradient(180deg, rgba(10,122,169,.04), transparent 30%);"' : '';
    ?>
    <a href="<?= esc_url($sw['permalink']) ?>" class="s-row"<?= $extra ?>>
      <?php inp_vlogo($sw, 'lg'); ?>
      <div class="body">
        <?php if ($featured): ?>
          <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
            <span style="font-size:10px; letter-spacing:.08em; text-transform:uppercase; font-weight:700; color:#fff; background:var(--accent); padding:3px 8px; border-radius:999px;">★ Top listing</span>
            <span style="font-size:11px; color:var(--text-subtle);">Verified vendor</span>
          </div>
        <?php endif; ?>
        <h3><?= esc_html($sw['title']) ?></h3>
        <div class="vendor"><?= esc_html($sw['vendor']) ?> <?php if ($flag): ?>· <?= $flag ?> <?= esc_html($country) ?><?php endif; ?></div>
        <p><?= esc_html($sw['excerpt']) ?></p>
        <?php if (!empty($sw['included'])): ?>
        <div class="pills">
          <?php foreach (array_slice($sw['included'], 0, 6) as $f): ?>
            <span><?= esc_html($f) ?></span>
          <?php endforeach; ?>
          <?php if (!empty($sw['deployment'])): ?><span><?= esc_html($sw['deployment']) ?></span><?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="side">
        <div class="rating-block">
          <div class="big"><?= esc_html(number_format($sw['rating'], 1)) ?><small>/5</small></div>
          <?= inp_stars($sw['rating']) ?>
          <small class="reviews"><?= esc_html(number_format($sw['review_count'])) ?> reviews</small>
        </div>
        <?php if (!empty($sw['price'])): ?>
          <div class="price-tag"><b><?= esc_html($sw['price']) ?></b></div>
        <?php endif; ?>
        <div class="actions">
          <span class="btn primary sm">Get free demo</span>
          <span class="btn outline sm">Compare</span>
        </div>
      </div>
    </a>
    <?php
}

/**
 * Default mega menu — used when no menu is configured in WP admin.
 * Pulls live taxonomies so it stays in sync with the catalogue.
 * Defined here (not in header.php) so it's available to the [inp_nav]
 * shortcode used inside block-template parts.
 */
function inp_render_default_nav() {
    $industries = get_terms(['taxonomy' => 'industry',    'hide_empty' => false, 'number' => 8]);
    $categories = get_terms(['taxonomy' => 'sw_category', 'hide_empty' => false, 'number' => 8]);
    ?>
    <div class="nav-item">
      <button class="nav-trigger" aria-haspopup="true">Industries
        <svg class="caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <div class="mega wide">
        <div class="mega-body">
          <div class="mega-left">
            <div class="mega-col">
              <h4>By industry</h4>
              <?php foreach (array_slice($industries, 0, 4) as $t) : ?>
                <a href="<?= esc_url(get_term_link($t)) ?>" class="mega-link">
                  <span class="ico"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/></svg></span>
                  <span class="body"><span class="title"><?= esc_html($t->name) ?></span><span class="desc"><?= (int) $t->count ?> software listed</span></span>
                </a>
              <?php endforeach; ?>
            </div>
            <div class="mega-col">
              <h4>More industries</h4>
              <ul class="mini-list">
                <?php foreach (array_slice($industries, 4) as $t) : ?>
                  <li><a href="<?= esc_url(get_term_link($t)) ?>"><?= esc_html($t->name) ?> <small><?= (int) $t->count ?> tools</small></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
          <div class="mega-right">
            <a href="<?= esc_url(get_post_type_archive_link('software')) ?>" class="feature-card" style="aspect-ratio:16/10; background: var(--accent-soft); display:flex; align-items:end; padding:18px;">
              <div style="position:relative; z-index:2;">
                <span class="tag" style="background: var(--accent); color:#fff; padding:3px 8px; border-radius:999px; font-size:10px; font-weight:700; letter-spacing:.06em;">EDITOR'S PICK</span>
                <h5 style="margin:8px 0 4px; color:var(--ink); font-size:16px; font-weight:700;">Tally Prime — fastest VAT close in the market</h5>
                <p style="margin:0; color:var(--text-muted); font-size:12px;">2,000+ verified deployments.</p>
              </div>
            </a>
          </div>
        </div>
        <div class="mega-foot">
          <div class="links"><a href="#">Buyer's guides</a></div>
          <a href="<?= esc_url(get_post_type_archive_link('software')) ?>" class="viewall">Browse all
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></a>
        </div>
      </div>
    </div>

    <div class="nav-item">
      <button class="nav-trigger" aria-haspopup="true">Categories
        <svg class="caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <div class="mega wide">
        <div class="mega-body">
          <div class="mega-left">
            <div class="mega-col">
              <h4>Most searched</h4>
              <?php foreach (array_slice($categories, 0, 4) as $t) : ?>
                <a href="<?= esc_url(get_term_link($t)) ?>" class="mega-link">
                  <span class="ico"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="3"/></svg></span>
                  <span class="body"><span class="title"><?= esc_html($t->name) ?></span><span class="desc"><?= (int) $t->count ?> tools</span></span>
                </a>
              <?php endforeach; ?>
            </div>
            <div class="mega-col">
              <h4>By function</h4>
              <ul class="mini-list">
                <?php foreach (array_slice($categories, 4) as $t) : ?>
                  <li><a href="<?= esc_url(get_term_link($t)) ?>"><?= esc_html($t->name) ?> <small><?= (int) $t->count ?> tools</small></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <a class="nav-trigger" href="<?= esc_url(get_post_type_archive_link('software')) ?>">Top Software</a>
    <a class="nav-trigger" href="<?= esc_url(home_url('/compare/')) ?>">Compare</a>
    <?php
}

/**
 * Industry tile-card on the home page.
 */
function inp_render_industry_card($term) {
    $count = (int) $term->count;
    $slug  = sanitize_html_class($term->slug);
    ?>
    <a href="<?= esc_url(get_term_link($term)) ?>" class="cat-card">
      <div class="ind-tile <?= esc_attr($slug) ?>">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M8 12l2.5 2.5L16 9"/></svg>
      </div>
      <div>
        <h3><?= esc_html($term->name) ?></h3>
        <small><?= $count ?> software</small>
      </div>
      <div class="thumb" style="background:linear-gradient(135deg,var(--accent-soft),#cfe6f3); display:grid; place-items:center;">
        <svg width="68" height="48" viewBox="0 0 100 60" fill="none">
          <rect x="6" y="14" width="88" height="40" rx="4" fill="#fff" stroke="var(--accent-deep)" stroke-width="2"/>
          <rect x="14" y="22" width="36" height="6" rx="2" fill="var(--accent)" opacity=".4"/>
          <rect x="14" y="32" width="72" height="3" rx="1.5" fill="var(--accent)" opacity=".3"/>
          <rect x="14" y="40" width="50" height="3" rx="1.5" fill="var(--accent)" opacity=".25"/>
        </svg>
      </div>
    </a>
    <?php
}
