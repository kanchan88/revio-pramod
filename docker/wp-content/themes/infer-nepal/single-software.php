<?php
get_header();
the_post();
$sw = inp_get_software(get_the_ID());
$country = !empty($sw['country_terms']) ? $sw['country_terms'][0]->name : '';
$flag    = inp_country_flag($country);

// Decode JSON-encoded rich fields, fall back to empty arrays
$awards     = json_decode((string) get_post_meta($sw['id'], 'awards_json', true), true) ?: [];
$pros       = json_decode((string) get_post_meta($sw['id'], 'pros_json', true), true) ?: [];
$cons       = json_decode((string) get_post_meta($sw['id'], 'cons_json', true), true) ?: [];
$modules    = json_decode((string) get_post_meta($sw['id'], 'feature_modules_json', true), true) ?: [];
$tiers      = json_decode((string) get_post_meta($sw['id'], 'pricing_tiers_json', true), true) ?: [];
$reviews    = json_decode((string) get_post_meta($sw['id'], 'sample_reviews_json', true), true) ?: [];
$faq        = json_decode((string) get_post_meta($sw['id'], 'faq_json', true), true) ?: [];
$specs      = json_decode((string) get_post_meta($sw['id'], 'specs_json', true), true) ?: [];

$verdict        = (string) get_post_meta($sw['id'], 'verdict', true);
$best_fit       = (string) get_post_meta($sw['id'], 'best_fit_for', true);
$look_elsewhere = (string) get_post_meta($sw['id'], 'look_elsewhere', true);
$tagline        = (string) get_post_meta($sw['id'], 'tagline', true);
$languages      = (string) get_post_meta($sw['id'], 'languages', true);
$headquarters   = (string) get_post_meta($sw['id'], 'headquarters', true);
$founded        = (string) get_post_meta($sw['id'], 'founded', true);
$customers      = (string) get_post_meta($sw['id'], 'customers', true);
$partners_np    = (string) get_post_meta($sw['id'], 'partners_in_nepal', true);
$support_hours  = (string) get_post_meta($sw['id'], 'support_hours', true);

// Fallbacks: if no pros/cons in meta, derive simple ones from features lists
if (empty($pros) && !empty($sw['included'])) {
    foreach (array_slice($sw['included'], 0, 5) as $f) $pros[] = ['title' => $f, 'body' => ''];
}
if (empty($cons) && !empty($sw['excluded'])) {
    foreach ($sw['excluded'] as $f) $cons[] = ['title' => $f, 'body' => ''];
}

// Fallback: if no FAQ, generate sensible defaults from meta
if (empty($faq)) {
    $faq[] = ['q' => 'Is there a free trial?', 'a' => $sw['free_trial']
        ? 'Yes — a free trial is available. Request a demo on the right and the vendor will set you up within 24 hours.'
        : 'A free trial is not advertised — request a demo and ask the vendor about their evaluation policy.'];
    if ($sw['deployment']) $faq[] = ['q' => 'How is it deployed?', 'a' => $sw['deployment']];
    $faq[] = ['q' => 'Does it have a mobile app or API?',
        'a' => 'Mobile app: <strong>' . ($sw['has_mobile_app'] ? 'Yes' : 'No') . '</strong> · Public API: <strong>' . ($sw['has_api'] ? 'Yes' : 'No') . '</strong>'];
    if ($sw['price']) $faq[] = ['q' => 'How much does it cost?', 'a' => 'Starts at <strong>' . esc_html($sw['price']) . '</strong>. ' . esc_html($sw['pricing_model']) . '.'];
}

// Fallback: build a minimal pricing tier if none provided
if (empty($tiers) && $sw['price']) {
    $tiers[] = [
        'name' => $sw['pricing_model'] ?: 'Standard',
        'blurb' => 'Talk to the vendor for a tailored quote.',
        'price_text' => $sw['price'],
        'items' => $sw['included'],
        'cta_label' => 'Request quote',
        'popular' => true,
    ];
}

// Fallback: build basic spec sections from existing meta
if (empty($specs)) {
    $specs = [
        ['title' => 'Vendor & product', 'rows' => array_filter([
            'Vendor'         => $sw['vendor'],
            'Headquartered'  => $headquarters,
            'Founded'        => $founded,
            'Maturity'       => $sw['maturity'],
            'Customers'      => $customers,
            'Partners in Nepal' => $partners_np,
        ])],
        ['title' => 'Deployment & access', 'rows' => array_filter([
            'Deployment'    => $sw['deployment'],
            'Best for size' => $sw['company_size'],
            'Mobile app'    => $sw['has_mobile_app'] ? 'Yes' : 'No',
            'Public API'    => $sw['has_api'] ? 'Yes' : 'No',
            'Free trial'    => $sw['free_trial'] ? 'Yes' : 'No',
            'Languages'     => $languages,
            'Support hours' => $support_hours,
        ])],
        ['title' => 'Pricing', 'rows' => array_filter([
            'Pricing model' => $sw['pricing_model'],
            'Starts at'     => $sw['price'],
        ])],
    ];
}
?>

<!-- Sticky product subnav -->
<div class="prod-subnav">
  <div class="container inner">
    <div class="lhs">
      <div class="thumb"><?php inp_vlogo($sw, 'sm'); ?></div>
      <div class="title"><?= esc_html($sw['title']) ?> · by <?= esc_html($sw['vendor']) ?></div>
      <span class="rating"><span class="star">★</span> <?= esc_html(number_format($sw['rating'], 1)) ?>/5
        <small style="color:var(--text-subtle); font-weight:500; margin-left:4px;">(<?= esc_html(number_format($sw['review_count'])) ?>)</small>
      </span>
    </div>
    <div class="anchors">
      <a href="#overview">Overview</a>
      <a href="#features">Features</a>
      <a href="#pricing">Pricing</a>
      <a href="#reviews">Reviews</a>
      <a href="#alternatives">Alternatives</a>
    </div>
    <div class="actions">
      <a href="#demo" class="btn primary sm">Get free demo</a>
    </div>
  </div>
</div>

<!-- Breadcrumb -->
<div class="container">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= esc_url(home_url('/')) ?>">Home</a>
    <span class="sep">/</span>
    <a href="<?= esc_url(get_post_type_archive_link('software')) ?>">Software</a>
    <?php if (!empty($sw['categories'])) : $c = $sw['categories'][0]; ?>
      <span class="sep">/</span><a href="<?= esc_url(get_term_link($c)) ?>"><?= esc_html($c->name) ?></a>
    <?php endif; ?>
    <span class="sep">/</span>
    <span style="color:var(--text)"><?= esc_html($sw['title']) ?></span>
  </nav>
</div>

<!-- Software Hero -->
<section class="sw-hero">
  <div class="container">
    <div class="row">

      <div>
        <div class="head">
          <?php inp_vlogo($sw, 'xl'); ?>
          <div class="titles">
            <h1><?= esc_html($sw['title']) ?></h1>
            <div class="vendor">
              By <?= esc_html($sw['vendor']) ?>
              <?php if ($flag): ?> · <?= $flag ?> <?= esc_html($country) ?><?php endif; ?>
              <?php if (!empty($sw['website_url'])): ?> · <a href="<?= esc_url($sw['website_url']) ?>" target="_blank" rel="noopener">Visit website ↗</a><?php endif; ?>
            </div>
            <div class="badges">
              <?php if (!empty($awards)) : foreach (array_slice($awards, 0, 1) as $a): ?>
                <span class="badge brand"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2l3 7h7l-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9h7z"/></svg> <?= esc_html($a['title'] ?? $a) ?></span>
              <?php endforeach; endif; ?>
              <?php if (!empty($sw['maturity'])): ?>
                <span class="badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/></svg> <?= esc_html($sw['maturity']) ?></span>
              <?php endif; ?>
              <?php if ($sw['has_mobile_app']): ?>
                <span class="badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="2" width="12" height="20" rx="2"/></svg> Mobile app</span>
              <?php endif; ?>
              <?php if ($sw['has_api']): ?>
                <span class="badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6l-4 6 4 6M16 6l4 6-4 6"/></svg> Public API</span>
              <?php endif; ?>
              <?php if ($sw['free_trial']): ?>
                <span class="badge success"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12l4 4 10-10"/></svg> Free trial</span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <p class="summary"><?= esc_html($tagline ?: $sw['excerpt']) ?></p>

        <div class="quick-meta">
          <?php if (!empty($sw['deployment'])): ?>
          <div class="qi">
            <div class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-6 9 6v11a2 2 0 01-2 2h-4v-7H10v7H6a2 2 0 01-2-2z"/></svg></div>
            <div><div class="lbl">Deployment</div><div class="val"><?= esc_html($sw['deployment']) ?></div></div>
          </div>
          <?php endif; ?>
          <?php if (!empty($sw['company_size'])): ?>
          <div class="qi">
            <div class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="7" r="4"/><path d="M2 21v-2a4 4 0 014-4h6a4 4 0 014 4v2"/></svg></div>
            <div><div class="lbl">Best for</div><div class="val"><?= esc_html($sw['company_size']) ?></div></div>
          </div>
          <?php endif; ?>
          <?php if (!empty($sw['pricing_model'])): ?>
          <div class="qi">
            <div class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M5 9h14M5 15h14"/></svg></div>
            <div><div class="lbl">Pricing</div><div class="val"><?= esc_html($sw['pricing_model']) ?></div></div>
          </div>
          <?php endif; ?>
          <div class="qi">
            <div class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/></svg></div>
            <div>
              <div class="lbl">Languages</div>
              <div class="val"><?= esc_html($languages ?: 'English') ?></div>
            </div>
          </div>
        </div>

        <!-- Screenshots — generic dashboard SVG mockup (works for every product) -->
        <div class="sw-screens" aria-label="Product screenshots">
          <div class="screen main" title="Dashboard">
            <?php include get_theme_file_path('template-parts/dashboard-mockup.php'); ?>
          </div>
          <div class="screen" title="Voucher entry">
            <?php include get_theme_file_path('template-parts/voucher-mockup.php'); ?>
          </div>
          <div class="screen" title="Reports">
            <?php include get_theme_file_path('template-parts/reports-mockup.php'); ?>
          </div>
        </div>
      </div>

      <div class="ratings-block">
        <div class="top">
          <span class="big"><?= esc_html(number_format($sw['rating'], 1)) ?></span><span class="out">/5</span>
          <?= inp_stars($sw['rating']) ?>
        </div>
        <div class="total">Based on <b><?= esc_html(number_format($sw['review_count'])) ?> verified reviews</b></div>
        <?php
        // Build a synthetic rating distribution from the overall rating
        $r = max(1, min(5, $sw['rating']));
        $dist = [
            5 => round(($r - 3) * 25 + 28, 0),
            4 => round((5 - abs($r - 4)) * 6, 0),
            3 => round((5 - abs($r - 3)) * 3, 0),
            2 => round((5 - abs($r - 2)) * 1.5, 0),
            1 => round((5 - abs($r - 1)) * 0.6, 0),
        ];
        $total = array_sum($dist);
        ?>
        <div class="ddist">
          <?php foreach ([5,4,3,2,1] as $star) : $pct = $total ? round($dist[$star] / $total * 100) : 0; ?>
            <div class="drow"><span><?= $star ?>★</span><div class="bar"><i style="width: <?= $pct ?>%"></i></div><span><?= $pct ?>%</span></div>
          <?php endforeach; ?>
        </div>
        <div class="cta-stack">
          <a href="#demo" class="btn primary">Get free demo</a>
          <a href="#pricing" class="btn outline">See pricing</a>
          <?php if ($sw['website_url']): ?>
            <a href="<?= esc_url($sw['website_url']) ?>" target="_blank" rel="noopener" class="btn ghost" style="border:1px solid var(--border);">Visit official website ↗</a>
          <?php endif; ?>
          <div class="meta-line">No spam · 2,800+ businesses request demos here every month</div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Tabs -->
<div class="container">
  <nav class="tabs-nav" role="tablist">
    <a href="#overview" class="active">Overview</a>
    <a href="#features">Features</a>
    <a href="#pricing">Pricing</a>
    <a href="#reviews">Reviews <small style="color:var(--text-subtle); font-weight: 500; margin-left: 4px;"><?= number_format($sw['review_count']) ?></small></a>
    <a href="#specifications">Specifications</a>
    <a href="#alternatives">Alternatives</a>
    <a href="#faq">FAQ</a>
  </nav>
</div>

<div class="container" style="padding-bottom: 60px;">
  <div class="content-grid">
    <div>

      <!-- ============== OVERVIEW ============== -->
      <section id="overview" class="tab-panel active">

        <?php if ($verdict || $best_fit || $look_elsewhere): ?>
        <div class="verdict-sw">
          <h3>Why we recommend <?= esc_html($sw['title']) ?></h3>
          <?php if ($verdict): ?><p><?= esc_html($verdict) ?></p><?php endif; ?>
          <?php if ($best_fit || $look_elsewhere): ?>
          <div class="who-grid">
            <?php if ($best_fit): ?>
            <div class="col">
              <h5>Best fit for</h5><p><?= esc_html($best_fit) ?></p>
            </div>
            <?php endif; ?>
            <?php if ($look_elsewhere): ?>
            <div class="col">
              <h5>Look elsewhere if</h5><p><?= esc_html($look_elsewhere) ?></p>
            </div>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($awards)): ?>
        <div class="awards">
          <span class="label">Recognised in</span>
          <?php foreach ($awards as $i => $a):
            $title = is_array($a) ? ($a['title'] ?? '') : $a;
            $medal = is_array($a) ? ($a['medal'] ?? ['brand','1','2','silver'][min($i,3)]) : 'brand';
          ?>
            <div class="award-badge"><span class="medal <?= esc_attr($medal) ?>"><?= esc_html(($i + 1)) ?></span> <?= esc_html($title) ?></div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($sw['industries'])): ?>
        <h3 style="margin: 32px 0 12px; font-size: 18px; font-weight: 700;">Industries it serves</h3>
        <div class="ind-grid">
          <?php foreach ($sw['industries'] as $t): ?>
            <a href="<?= esc_url(get_term_link($t)) ?>" class="ind-chip">
              <span class="ind-tile"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M8 12l2.5 2.5L16 9"/></svg></span>
              <?= esc_html($t->name) ?>
            </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($pros) || !empty($cons)): ?>
        <h3 style="margin: 32px 0 12px; font-size: 18px; font-weight: 700;">Pros &amp; cons at a glance</h3>
        <div class="proscons">
          <?php if (!empty($pros)): ?>
          <div class="col pros">
            <h5>What buyers love</h5>
            <ul>
              <?php foreach ($pros as $p) {
                  $line = is_array($p) ? trim(($p['title'] ?? '') . (!empty($p['body']) ? ' — ' . $p['body'] : '')) : $p;
                  echo '<li>' . esc_html($line) . '</li>';
              } ?>
            </ul>
          </div>
          <?php endif; ?>
          <?php if (!empty($cons)): ?>
          <div class="col cons">
            <h5>Common complaints</h5>
            <ul>
              <?php foreach ($cons as $c) {
                  $line = is_array($c) ? trim(($c['title'] ?? '') . (!empty($c['body']) ? ' — ' . $c['body'] : '')) : $c;
                  echo '<li>' . esc_html($line) . '</li>';
              } ?>
            </ul>
          </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (trim(strip_tags(get_the_content()))): ?>
        <div style="margin-top: 32px; font-size: 15px; color: var(--text); line-height: 1.7;">
          <?php the_content(); ?>
        </div>
        <?php endif; ?>
      </section>

      <!-- ============== FEATURES ============== -->
      <section id="features" class="tab-panel" style="margin-top: 48px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 6px;">Features included</h2>
        <p style="color:var(--text-muted); font-size: 14px; margin: 0 0 22px;">Modules and capabilities that ship out of the box.</p>

        <?php if (!empty($modules)): ?>
          <?php foreach ($modules as $section): ?>
            <?php if (!empty($section['title'])): ?><h6 class="fm-section-title"><?= esc_html($section['title']) ?></h6><?php endif; ?>
            <div class="fm-grid">
              <?php foreach (($section['cards'] ?? []) as $card): ?>
                <div class="fm-card">
                  <div class="fm-head">
                    <div class="fm-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M8 12l2.5 2.5L16 9"/></svg></div>
                    <h4><?= esc_html($card['title'] ?? '') ?></h4>
                  </div>
                  <ul>
                    <?php foreach (($card['items'] ?? []) as $it): ?>
                      <li><?= esc_html($it) ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Fallback: simple grid from included_features -->
          <div class="fm-grid">
            <div class="fm-card">
              <div class="fm-head">
                <div class="fm-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg></div>
                <h4>Included</h4>
              </div>
              <ul>
                <?php foreach ($sw['included'] as $f) echo '<li>' . esc_html($f) . '</li>'; ?>
              </ul>
            </div>
            <?php if (!empty($sw['excluded'])): ?>
            <div class="fm-card" style="background: var(--bg-subtle);">
              <div class="fm-head">
                <div class="fm-icon" style="background:var(--danger-soft); color:var(--danger);"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg></div>
                <h4>Not included</h4>
              </div>
              <ul>
                <?php foreach ($sw['excluded'] as $f): ?>
                  <li style="color:var(--text-muted);"><?= esc_html($f) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($modules) && !empty($sw['excluded'])): ?>
          <h6 class="fm-section-title">Not included (you'll need add-ons)</h6>
          <div class="fm-card" style="background: var(--bg-subtle);">
            <ul style="list-style:none; padding:0; margin:0; display:grid; gap:7px;">
              <?php foreach ($sw['excluded'] as $f): ?>
                <li style="display:flex; gap:8px; color:var(--text-muted); font-size:13px;"><span style="color:var(--danger)">✕</span> <?= esc_html($f) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </section>

      <!-- ============== PRICING ============== -->
      <section id="pricing" class="tab-panel" style="margin-top: 48px;">
        <div style="display:flex; justify-content:space-between; align-items:end; margin-bottom: 18px; gap:12px; flex-wrap:wrap;">
          <div>
            <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 4px;">Pricing &amp; plans</h2>
            <p style="color:var(--text-muted); font-size: 14px; margin:0;"><?= esc_html($sw['pricing_model']) ?> · pricing in Nepali Rupees where applicable.</p>
          </div>
        </div>

        <?php if (!empty($tiers)): ?>
        <div class="tier-grid">
          <?php foreach ($tiers as $tier): ?>
            <div class="tier <?= !empty($tier['popular']) ? 'popular' : '' ?>">
              <div class="name"><?= esc_html($tier['name'] ?? '') ?></div>
              <?php if (!empty($tier['blurb'])): ?><p class="blurb"><?= esc_html($tier['blurb']) ?></p><?php endif; ?>
              <div class="price">
                <?php if (!empty($tier['price_cur'])): ?><span class="cur"><?= esc_html($tier['price_cur']) ?></span><?php endif; ?>
                <?php if (!empty($tier['price_num'])): ?><span class="num"><?= esc_html($tier['price_num']) ?></span><?php else: ?><span class="num"><?= esc_html($tier['price_text'] ?? '') ?></span><?php endif; ?>
                <?php if (!empty($tier['per'])): ?><span class="per"><?= esc_html($tier['per']) ?></span><?php endif; ?>
              </div>
              <ul>
                <?php foreach (($tier['items'] ?? []) as $it):
                  if (is_array($it)) {
                      $label = $it['label'] ?? '';
                      $no = !empty($it['no']);
                  } else {
                      $label = $it; $no = false;
                  }
                ?>
                  <li class="<?= $no ? 'no' : '' ?>"><?= esc_html($label) ?></li>
                <?php endforeach; ?>
              </ul>
              <a href="#demo" class="btn <?= !empty($tier['popular']) ? 'primary' : 'outline' ?>"><?= esc_html($tier['cta_label'] ?? 'Get started') ?></a>
            </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
          <div class="awards">
            <span class="label">Get a custom quote</span>
            <a href="#demo" class="btn primary sm">Request quote</a>
          </div>
        <?php endif; ?>
      </section>

      <!-- ============== REVIEWS ============== -->
      <section id="reviews" class="tab-panel" style="margin-top: 48px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 16px; flex-wrap:wrap; gap:12px;">
          <h2 style="font-size: 22px; font-weight: 700; margin: 0;">User reviews <small style="color:var(--text-subtle); font-weight:500;">· <?= esc_html(number_format($sw['review_count'])) ?> verified</small></h2>
          <a href="#" class="btn primary sm">Write a review</a>
        </div>

        <div class="review-summary">
          <div class="overall">
            <div class="num"><?= esc_html(number_format($sw['rating'], 1)) ?></div>
            <?= inp_stars($sw['rating']) ?>
            <div class="total"><?= number_format($sw['review_count']) ?> reviews</div>
          </div>
          <div class="dist">
            <?php foreach ([5,4,3,2,1] as $star): $pct = $total ? round($dist[$star] / $total * 100) : 0; ?>
              <div class="row"><span><?= $star ?> ★</span><div class="bar"><i style="width: <?= $pct ?>%"></i></div><span><?= number_format(round($sw['review_count'] * $pct / 100)) ?></span></div>
            <?php endforeach; ?>
          </div>
          <div class="scores">
            <div class="item">Ease of use <span class="v"><?= number_format(min(5, $sw['rating'] - 0.2), 1) ?></span><div class="meter"><i style="width:<?= round((min(5, $sw['rating'] - 0.2)) * 20) ?>%"></i></div></div>
            <div class="item">Value for money <span class="v"><?= number_format(min(5, $sw['rating'] + 0.2), 1) ?></span><div class="meter"><i style="width:<?= round((min(5, $sw['rating'] + 0.2)) * 20) ?>%"></i></div></div>
            <div class="item">Customer support <span class="v"><?= number_format($sw['rating'], 1) ?></span><div class="meter"><i style="width:<?= round($sw['rating'] * 20) ?>%"></i></div></div>
            <div class="item">Features <span class="v"><?= number_format(min(5, $sw['rating'] + 0.1), 1) ?></span><div class="meter"><i style="width:<?= round((min(5, $sw['rating'] + 0.1)) * 20) ?>%"></i></div></div>
          </div>
        </div>

        <?php if (!empty($reviews)): ?>
        <div class="urev-grid">
          <?php foreach ($reviews as $rev):
            $initials = strtoupper(substr(($rev['name'] ?? '?'), 0, 1) . substr(strstr(($rev['name'] ?? ''), ' ') ?: '', 1, 1));
            $stars = str_repeat('★', (int) ($rev['rating'] ?? 5)) . str_repeat('☆', max(0, 5 - (int) ($rev['rating'] ?? 5)));
            $hash = crc32($rev['name'] ?? ''); $h1 = $hash % 360; $h2 = ($h1 + 30) % 360;
            ?>
            <div class="urev">
              <div class="top">
                <div class="who">
                  <span class="ava" style="background:linear-gradient(135deg,hsl(<?= $h1 ?> 60% 40%),hsl(<?= $h2 ?> 70% 50%));"><?= esc_html($initials) ?></span>
                  <div>
                    <strong><?= esc_html($rev['name'] ?? '') ?><?= !empty($rev['role']) ? ' · ' . esc_html($rev['role']) : '' ?></strong>
                    <small><?= esc_html(($rev['industry'] ?? '') . (!empty($rev['size']) ? ' · ' . $rev['size'] . ' employees' : '')) ?> · Verified · <?= esc_html($rev['time'] ?? '') ?></small>
                  </div>
                </div>
                <div class="stars" style="color:var(--star);"><?= $stars ?></div>
              </div>
              <?php if (!empty($rev['title'])): ?><h5>"<?= esc_html($rev['title']) ?>"</h5><?php endif; ?>
              <p><?= esc_html($rev['body'] ?? '') ?></p>
              <?php if (!empty($rev['pros']) || !empty($rev['cons'])): ?>
              <div class="pros-cons">
                <?php if (!empty($rev['pros'])): ?>
                <div class="col"><h6 class="p">PROS</h6><p><?= esc_html($rev['pros']) ?></p></div>
                <?php endif; ?>
                <?php if (!empty($rev['cons'])): ?>
                <div class="col"><h6 class="c">CONS</h6><p><?= esc_html($rev['cons']) ?></p></div>
                <?php endif; ?>
              </div>
              <?php endif; ?>
              <?php if (!empty($rev['tags'])): ?>
              <div class="meta-tags">
                <?php foreach ($rev['tags'] as $t) echo '<span>' . esc_html($t) . '</span>'; ?>
              </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
          <p style="color:var(--text-muted);">No public reviews yet — be the first to <a href="#" style="color:var(--accent-deep); font-weight:600;">write a review</a>.</p>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 18px;">
          <a href="#" class="btn outline">Show all <?= number_format($sw['review_count']) ?> reviews</a>
        </div>
      </section>

      <!-- ============== SPECIFICATIONS ============== -->
      <section id="specifications" class="tab-panel" style="margin-top: 48px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 18px;">Specifications</h2>
        <?php foreach ($specs as $block):
          if (empty($block['rows'])) continue;
        ?>
          <div class="spec-block">
            <h3><?= esc_html($block['title'] ?? '') ?></h3>
            <div class="spec-table">
              <?php foreach ($block['rows'] as $label => $value): ?>
                <div class="spec-row"><div><?= esc_html($label) ?></div><div><?= esc_html($value) ?></div></div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </section>

      <!-- ============== ALTERNATIVES ============== -->
      <section id="alternatives" class="tab-panel" style="margin-top: 48px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 6px;">Top alternatives to <?= esc_html($sw['title']) ?></h2>
        <p style="color:var(--text-muted); font-size: 14px; margin: 0 0 18px;">Compared on rating, deployment, country of origin and entry pricing.</p>
        <?php
        $cat_ids = array_map(fn($t) => $t->term_id, $sw['categories']);
        $alt_q   = new WP_Query([
            'post_type'      => 'software',
            'post__not_in'   => [$sw['id']],
            'posts_per_page' => 5,
            'meta_key'       => 'rating',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'tax_query'      => $cat_ids ? [['taxonomy' => 'sw_category', 'field' => 'term_id', 'terms' => $cat_ids]] : [],
        ]);
        if ($alt_q->have_posts()) : ?>
          <div class="alt-table-wrap">
          <table class="alt-table">
            <thead>
              <tr><th>Software</th><th>Rating</th><th class="center">Deployment</th><th class="center">Origin</th><th>Starts at</th><th></th></tr>
            </thead>
            <tbody>
              <?php while ($alt_q->have_posts()) : $alt_q->the_post();
                $alt = inp_get_software(get_the_ID());
                $altCountry = !empty($alt['country_terms']) ? $alt['country_terms'][0]->name : '';
                ?>
                <tr>
                  <td><div class="name-cell"><?php inp_vlogo($alt, 'sm'); ?><div><strong><?= esc_html($alt['title']) ?></strong><small><?= esc_html($alt['vendor']) ?></small></div></div></td>
                  <td><span class="star">★</span> <?= esc_html(number_format($alt['rating'], 1)) ?> <small style="color:var(--text-subtle);">(<?= number_format($alt['review_count']) ?>)</small></td>
                  <td class="center"><?= esc_html($alt['deployment']) ?></td>
                  <td class="center"><?= inp_country_flag($altCountry) ?> <?= esc_html($altCountry) ?></td>
                  <td><b><?= esc_html($alt['price']) ?></b></td>
                  <td><a href="<?= esc_url($alt['permalink']) ?>" class="btn outline sm">View</a></td>
                </tr>
              <?php endwhile; wp_reset_postdata(); ?>
            </tbody>
          </table>
          </div>
        <?php else: ?>
          <p style="color:var(--text-muted);">No alternatives found.</p>
        <?php endif; ?>
      </section>

      <!-- ============== FAQ ============== -->
      <section id="faq" class="tab-panel" style="margin-top: 48px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 18px;">Frequently asked questions</h2>
        <div class="faq-list">
          <?php foreach ($faq as $i => $entry): ?>
            <details class="faq-item" <?= $i === 0 ? 'open' : '' ?>>
              <summary><?= esc_html($entry['q'] ?? '') ?></summary>
              <div class="body"><?= wp_kses_post($entry['a'] ?? '') ?></div>
            </details>
          <?php endforeach; ?>
        </div>
      </section>

    </div>

    <!-- ============== SIDEBAR ============== -->
    <aside>
      <div id="demo" class="demo-form">
        <h4>Get a free <?= esc_html($sw['title']) ?> demo</h4>
        <p class="lede">A certified partner near you will reach out within 24 hours. No obligation.</p>
        <form action="#" method="post">
          <label>Your name</label>
          <input type="text" name="name" placeholder="Ramesh Sharma" required>
          <label>Work email</label>
          <input type="email" name="email" placeholder="ramesh@yourcompany.com" required>
          <label>Phone</label>
          <input type="tel" name="phone" placeholder="+977 98XXXXXXXX">
          <label>Company size</label>
          <select name="size">
            <option>1–10 employees</option>
            <option selected>11–50 employees</option>
            <option>51–200 employees</option>
            <option>200+ employees</option>
          </select>
          <label>Industry</label>
          <select name="industry">
            <?php foreach (get_terms(['taxonomy'=>'industry','hide_empty'=>false]) as $t) echo '<option>' . esc_html($t->name) . '</option>'; ?>
          </select>
          <button class="btn primary" type="submit">Request demo →</button>
        </form>
        <div class="trust">🔒 Your details are shared only with the certified partner you select.</div>
      </div>

      <div class="vendor-info">
        <h5>Vendor at a glance</h5>
        <dl>
          <?php if ($founded): ?><dt>Founded</dt><dd><?= esc_html($founded) ?></dd><?php endif; ?>
          <?php if ($headquarters): ?><dt>HQ</dt><dd><?= esc_html($headquarters) ?></dd><?php endif; ?>
          <?php if ($customers): ?><dt>Customers</dt><dd><?= esc_html($customers) ?></dd><?php endif; ?>
          <?php if ($partners_np): ?><dt>NP partners</dt><dd><?= esc_html($partners_np) ?></dd><?php endif; ?>
          <?php if ($sw['website_url']): ?><dt>Website</dt><dd><a href="<?= esc_url($sw['website_url']) ?>" target="_blank" rel="noopener">Visit ↗</a></dd><?php endif; ?>
          <?php if ($support_hours): ?><dt>Support</dt><dd><?= esc_html($support_hours) ?></dd><?php endif; ?>
        </dl>
      </div>

      <nav class="toc" style="margin-top: 16px;">
        <h6>On this page</h6>
        <ul>
          <li><a href="#overview" class="active">Overview &amp; verdict</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#pricing">Pricing &amp; plans</a></li>
          <li><a href="#reviews">User reviews · <?= number_format($sw['review_count']) ?></a></li>
          <li><a href="#specifications">Specifications</a></li>
          <li><a href="#alternatives">Alternatives</a></li>
          <li><a href="#faq">FAQ</a></li>
        </ul>

        <div style="margin-top: 18px; border: 1px solid var(--border); border-radius: 12px; padding: 14px; background: var(--bg-subtle);">
          <h5 style="font-size: 12px; margin: 0 0 6px; color:var(--text-muted); letter-spacing:.04em; text-transform:uppercase; font-weight:700;">How we score</h5>
          <p style="font-size: 12px; color: var(--text-muted); margin: 0 0 8px; line-height:1.6;">
            Reviews are verified via work email + invoice. Editor scores combine user reviews with hands-on feature tests.
          </p>
          <a href="#" style="font-size: 12px; color: var(--accent-deep); font-weight: 600;">Read methodology →</a>
        </div>
      </nav>
    </aside>
  </div>
</div>

<?php get_footer(); ?>
