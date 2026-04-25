<!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="light">
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="<?= esc_url(get_theme_file_uri('assets/logo.png')) ?>" />
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php if (get_theme_mod('inp_promo_enabled', true)) : ?>
<!-- Promotional strip -->
<div class="promo-strip">
  <a href="<?= esc_url(get_theme_mod('inp_promo_cta_url', '#')) ?>">
    <span class="live-dot"></span>
    <span class="tag"><?= esc_html(get_theme_mod('inp_promo_tag', 'New')) ?></span>
    <span class="msg"><?= wp_kses_post(get_theme_mod('inp_promo_message', 'Top 10 ERP for Nepali SMBs — 2026 report.')) ?></span>
    <span class="cta">
      <?= esc_html(get_theme_mod('inp_promo_cta_text', 'Read the report')) ?>
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
    </span>
  </a>
</div>
<?php endif; ?>

<!-- Primary Nav -->
<div class="nav-wrap">
  <div class="container">
    <nav class="primary">
      <a href="<?= esc_url(home_url('/')) ?>" class="brand">
        <?php if (has_custom_logo()) : the_custom_logo(); else : ?>
          <img src="<?= esc_url(get_theme_file_uri('assets/logo.png')) ?>" alt="<?php bloginfo('name'); ?>" class="brand-logo" />
        <?php endif; ?>
      </a>

      <button class="menu-toggle" aria-label="Open menu" aria-controls="primary-nav-links">
        <svg class="icon-bars" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        <svg class="icon-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>

      <div class="nav-links" id="primary-nav-links">
        <?php
        // If a Primary menu is configured in Appearance → Menus, use it
        // (with our mega walker). Otherwise fall back to auto-built mega
        // menus from the Industry & Category taxonomies.
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

      <button class="search-trigger" aria-label="Open search" onclick="this.querySelector('input')?.focus()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <span class="label">Search ERP, school, hotel, accounting…</span>
        <span class="kbd">⌘K</span>
      </button>

      <div class="nav-actions">
        <button class="icon-btn" id="themeBtn" aria-label="Toggle theme" title="Toggle theme">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
        </button>
        <a href="<?= esc_url(wp_login_url()) ?>" class="btn ghost">Sign in</a>
      </div>
    </nav>
  </div>
</div>

<?php /* inp_render_default_nav() lives in inc/helpers.php so it's available
        to the [inp_nav] shortcode used inside block-template parts too. */ ?>
