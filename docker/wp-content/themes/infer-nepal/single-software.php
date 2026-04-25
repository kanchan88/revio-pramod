<?php
get_header();
the_post();
$sw = inp_get_software(get_the_ID());
$country = !empty($sw['country_terms']) ? $sw['country_terms'][0]->name : '';
$flag    = inp_country_flag($country);
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
      <a href="#faq">FAQ</a>
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
    <span class="sep">/</span>
    <?php if (!empty($sw['categories'])) : $c = $sw['categories'][0]; ?>
      <a href="<?= esc_url(get_term_link($c)) ?>"><?= esc_html($c->name) ?></a>
      <span class="sep">/</span>
    <?php endif; ?>
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

        <p class="summary"><?= esc_html($sw['excerpt']) ?></p>

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
          <?php if ($country): ?>
          <div class="qi">
            <div class="ic"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/></svg></div>
            <div><div class="lbl">Origin</div><div class="val"><?= $flag ?> <?= esc_html($country) ?></div></div>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="ratings-block">
        <div class="top">
          <span class="big"><?= esc_html(number_format($sw['rating'], 1)) ?></span><span class="out">/5</span>
          <?= inp_stars($sw['rating']) ?>
        </div>
        <div class="total">Based on <b><?= esc_html(number_format($sw['review_count'])) ?> verified reviews</b></div>
        <div class="cta-stack">
          <a href="#demo" class="btn primary">Get free demo</a>
          <a href="#pricing" class="btn outline">See pricing</a>
          <?php if ($sw['website_url']): ?>
            <a href="<?= esc_url($sw['website_url']) ?>" target="_blank" rel="noopener" class="btn ghost" style="border:1px solid var(--border);">Visit official website ↗</a>
          <?php endif; ?>
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
    <a href="#reviews">Reviews</a>
    <a href="#alternatives">Alternatives</a>
    <a href="#faq">FAQ</a>
  </nav>
</div>

<div class="container" style="padding-bottom: 60px;">
  <div class="content-grid">
    <div>

      <section id="overview" class="tab-panel active">
        <div class="verdict-sw">
          <h3>About <?= esc_html($sw['title']) ?></h3>
          <div><?= apply_filters('the_content', get_the_content()) ?></div>
        </div>

        <?php if (!empty($sw['industries'])) : ?>
          <h3 style="margin: 32px 0 12px; font-size: 18px; font-weight: 700;">Industries it serves</h3>
          <div class="ind-grid">
            <?php foreach ($sw['industries'] as $t) : ?>
              <a href="<?= esc_url(get_term_link($t)) ?>" class="ind-chip">
                <span class="ind-tile"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/></svg></span>
                <?= esc_html($t->name) ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($sw['included']) || !empty($sw['excluded'])) : ?>
          <h3 style="margin: 32px 0 12px; font-size: 18px; font-weight: 700;">At a glance</h3>
          <div class="proscons">
            <?php if (!empty($sw['included'])): ?>
            <div class="col pros">
              <h5>Included features</h5>
              <ul>
                <?php foreach ($sw['included'] as $f) echo '<li>' . esc_html($f) . '</li>'; ?>
              </ul>
            </div>
            <?php endif; ?>
            <?php if (!empty($sw['excluded'])): ?>
            <div class="col cons">
              <h5>Not included</h5>
              <ul>
                <?php foreach ($sw['excluded'] as $f) echo '<li>' . esc_html($f) . '</li>'; ?>
              </ul>
            </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </section>

      <section id="features" class="tab-panel" style="margin-top: 48px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 18px;">Features</h2>
        <div class="fm-card">
          <h4>What's included</h4>
          <ul>
            <?php foreach ($sw['included'] as $f) echo '<li>' . esc_html($f) . '</li>'; ?>
          </ul>
        </div>
      </section>

      <section id="pricing" class="tab-panel" style="margin-top: 48px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 6px;">Pricing</h2>
        <p style="color:var(--text-muted); font-size: 14px; margin: 0 0 18px;"><?= esc_html($sw['pricing_model']) ?> · <strong style="color:var(--text);"><?= esc_html($sw['price']) ?></strong></p>
        <div class="awards">
          <span class="label">Get a custom quote</span>
          <a href="#demo" class="btn primary sm">Request quote</a>
        </div>
      </section>

      <section id="reviews" class="tab-panel" style="margin-top: 48px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 16px;">User reviews <small style="color:var(--text-subtle); font-weight:500;">· <?= esc_html(number_format($sw['review_count'])) ?> verified</small></h2>
        <div class="review-summary">
          <div class="overall">
            <div class="num"><?= esc_html(number_format($sw['rating'], 1)) ?></div>
            <?= inp_stars($sw['rating']) ?>
            <div class="total"><?= esc_html(number_format($sw['review_count'])) ?> reviews</div>
          </div>
        </div>
        <p style="color:var(--text-muted);">User reviews coming soon — be the first to <a href="#" style="color:var(--accent-deep); font-weight:600;">write a review</a>.</p>
      </section>

      <section id="alternatives" class="tab-panel" style="margin-top: 48px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 18px;">Top alternatives</h2>
        <?php
        // alternatives = same primary category, exclude current, top 5 by rating
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
              <tr><th>Software</th><th>Rating</th><th class="center">Deployment</th><th class="center">Origin</th><th>Price</th><th></th></tr>
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
          <p style="color:var(--text-muted);">No alternatives found in this category.</p>
        <?php endif; ?>
      </section>

      <section id="faq" class="tab-panel" style="margin-top: 48px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 18px;">FAQ</h2>
        <div class="faq-list">
          <details class="faq-item" open>
            <summary>Is there a free trial?</summary>
            <div class="body"><?= $sw['free_trial'] ? 'Yes — a free trial is available. Request a demo above and the vendor will set you up.' : 'A free trial is not advertised — request a demo and ask the vendor about their evaluation policy.' ?></div>
          </details>
          <details class="faq-item">
            <summary>How is it deployed?</summary>
            <div class="body"><?= esc_html($sw['deployment'] ?: 'Contact the vendor for deployment options.') ?></div>
          </details>
          <details class="faq-item">
            <summary>Does it have a mobile app or API?</summary>
            <div class="body">
              Mobile app: <strong><?= $sw['has_mobile_app'] ? 'Yes' : 'No' ?></strong> · Public API: <strong><?= $sw['has_api'] ? 'Yes' : 'No' ?></strong>
            </div>
          </details>
        </div>
      </section>

    </div>

    <aside>
      <div id="demo" class="demo-form">
        <h4>Get a free demo</h4>
        <p class="lede">A certified partner near you will reach out within 24 hours.</p>
        <form action="#" method="post">
          <label>Your name</label>
          <input type="text" name="name" placeholder="Ramesh Sharma" required>
          <label>Work email</label>
          <input type="email" name="email" placeholder="ramesh@yourcompany.com" required>
          <label>Phone</label>
          <input type="tel" name="phone" placeholder="+977 98XXXXXXXX">
          <label>Industry</label>
          <select name="industry">
            <?php foreach (get_terms(['taxonomy'=>'industry','hide_empty'=>false]) as $t) echo '<option>' . esc_html($t->name) . '</option>'; ?>
          </select>
          <button class="btn primary" type="submit">Request demo →</button>
        </form>
        <div class="trust">🔒 We share your details only with the vendor.</div>
      </div>

      <div class="vendor-info">
        <h5>Vendor at a glance</h5>
        <dl>
          <dt>Vendor</dt><dd><?= esc_html($sw['vendor']) ?></dd>
          <?php if (!empty($sw['maturity'])): ?><dt>Maturity</dt><dd><?= esc_html($sw['maturity']) ?></dd><?php endif; ?>
          <?php if ($country): ?><dt>HQ</dt><dd><?= $flag ?> <?= esc_html($country) ?></dd><?php endif; ?>
          <?php if ($sw['website_url']): ?><dt>Website</dt><dd><a href="<?= esc_url($sw['website_url']) ?>" target="_blank" rel="noopener">Visit ↗</a></dd><?php endif; ?>
          <dt>Reviews</dt><dd><?= esc_html(number_format($sw['review_count'])) ?></dd>
        </dl>
      </div>

      <nav class="toc" style="margin-top: 16px;">
        <h6>On this page</h6>
        <ul>
          <li><a href="#overview" class="active">Overview</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#pricing">Pricing</a></li>
          <li><a href="#reviews">Reviews</a></li>
          <li><a href="#alternatives">Alternatives</a></li>
          <li><a href="#faq">FAQ</a></li>
        </ul>
      </nav>
    </aside>
  </div>
</div>

<?php get_footer(); ?>
