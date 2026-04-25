<?php
get_header();

$current_term  = is_tax() ? get_queried_object() : null;
$is_search     = is_search();
$archive_title = $is_search ? 'Search results' : ($current_term ? $current_term->name : 'All software');
$archive_desc  = $is_search ? sprintf('Showing matches for "%s"', esc_html(get_search_query()))
                           : ($current_term ? $current_term->description : 'Browse the full catalogue of B2B software tools listed on Infer Nepal.');

// Helpers
function inp_get($k, $default = '') { return isset($_GET[$k]) ? sanitize_text_field((string) $_GET[$k]) : $default; }
function inp_get_arr($k) { return isset($_GET[$k]) ? array_map('sanitize_title', (array) $_GET[$k]) : []; }
function inp_checked($name, $value) {
    $current = inp_get_arr($name);
    return in_array($value, $current, true) ? 'checked' : '';
}
function inp_active_chip($name, $value, $label) {
    $current = inp_get_arr($name);
    if (!in_array($value, $current, true)) return '';
    $remove = $current;
    $remove = array_diff($remove, [$value]);
    $url = remove_query_arg($name);
    if ($remove) $url = add_query_arg($name . '[]', $remove, $url);
    return sprintf(
        '<span class="chip-active">%s <a href="%s" aria-label="Remove" style="color:inherit;">✕</a></span>',
        esc_html($label),
        esc_url($url)
    );
}

// All filterable options
$industries = get_terms(['taxonomy' => 'industry',    'hide_empty' => false]);
$categories = get_terms(['taxonomy' => 'sw_category', 'hide_empty' => false]);
$countries  = get_terms(['taxonomy' => 'country',     'hide_empty' => false]);
?>

<div class="container">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?= esc_url(home_url('/')) ?>">Home</a>
    <span class="sep">/</span>
    <a href="<?= esc_url(get_post_type_archive_link('software')) ?>">Software</a>
    <?php if ($current_term): ?>
      <span class="sep">/</span>
      <span style="color:var(--text)"><?= esc_html($current_term->name) ?></span>
    <?php endif; ?>
  </nav>
</div>

<?php
// If an admin has created a "Industry: <Name>" / "Category: <Name>" /
// "Country: <Name>" Page, render its BB-designed content here as the hero.
$landing = $current_term ? inp_get_term_landing_page($current_term) : null;
if ($landing && !empty($landing->post_content)) :
    echo '<div class="bb-landing">';
    echo apply_filters('the_content', $landing->post_content);
    echo '</div>';
else :
?>
<section class="cat-hero">
  <div class="container">
    <div class="row">
      <div>
        <?php if ($current_term): ?>
          <div style="display:inline-flex; align-items:center; gap:10px; margin-bottom:6px;">
            <span class="ind-tile <?= esc_attr($current_term->slug) ?>" style="width:38px; height:38px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M8 12l2.5 2.5L16 9"/></svg>
            </span>
            <span style="font-size:11px; letter-spacing:.1em; text-transform:uppercase; color:var(--text-subtle); font-weight:700;">
              <?= esc_html($current_term->taxonomy === 'industry' ? 'Industry' : ($current_term->taxonomy === 'sw_category' ? 'Category' : 'Country')) ?>
            </span>
          </div>
        <?php endif; ?>
        <h1><?= esc_html($archive_title) ?></h1>
        <p>
          <?php
          $count_text = (int) $GLOBALS['wp_query']->found_posts . ' software';
          if (!empty($archive_desc)) {
              echo esc_html($archive_desc);
          } else {
              echo $count_text . ' · verified user reviews. Compare by price, features, deployment and country of origin.';
          }
          ?>
        </p>
      </div>
      <div style="display:flex; gap:8px;">
        <a href="#" class="btn outline">Download buyer's guide</a>
        <a href="#demo" class="btn primary">Get free shortlist</a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Active-filter chip strip + sort -->
<?php
$active_chips = '';
foreach ($industries as $t) $active_chips .= inp_active_chip('industry', $t->slug, 'Industry: ' . $t->name);
foreach ($categories as $t) $active_chips .= inp_active_chip('sw_category', $t->slug, 'Category: ' . $t->name);
foreach ($countries  as $t) $active_chips .= inp_active_chip('country', $t->slug, inp_country_flag($t->name) . ' ' . $t->name);
foreach (['cloud'=>'Cloud','on-premise'=>'On-premise','hybrid'=>'Hybrid'] as $v=>$l)
    if (inp_get('deployment') === $v) $active_chips .= '<span class="chip-active">' . $l . ' <a href="' . esc_url(remove_query_arg('deployment')) . '" style="color:inherit;">✕</a></span>';
foreach (['one-time'=>'One-time','subscription'=>'Subscription','per user'=>'Per user / mo','custom'=>'Custom','free'=>'Free'] as $v=>$l)
    if (inp_get('pricing_model') === $v) $active_chips .= '<span class="chip-active">' . $l . ' <a href="' . esc_url(remove_query_arg('pricing_model')) . '" style="color:inherit;">✕</a></span>';
foreach (['mobile'=>'Mobile app','api'=>'Public API','trial'=>'Free trial'] as $v=>$l)
    if (!empty($_GET[$v])) $active_chips .= '<span class="chip-active">' . $l . ' <a href="' . esc_url(remove_query_arg($v)) . '" style="color:inherit;">✕</a></span>';
if (!empty($_GET['min_rating'])) $active_chips .= '<span class="chip-active">≥ ' . floatval($_GET['min_rating']) . '★ <a href="' . esc_url(remove_query_arg('min_rating')) . '" style="color:inherit;">✕</a></span>';
?>
<div class="filter-bar">
  <div class="container" style="display:flex; justify-content:space-between; align-items:center; gap: 20px; width: 100%;">
    <div class="chip-row">
      <?php if ($active_chips): ?>
        <span style="font-size:13px; color:var(--text-muted); font-weight:500;">Active:</span>
        <?= $active_chips ?>
        <a href="<?= esc_url(strtok($_SERVER['REQUEST_URI'], '?')) ?>" class="reset">Reset all</a>
      <?php else: ?>
        <span style="font-size:13px; color:var(--text-muted); font-weight:500;">No filters · showing all</span>
      <?php endif; ?>
    </div>
    <div class="sort">
      <span style="color: var(--text-muted);">Sort:</span>
      <?php $orderby = inp_get('orderby', 'reviews'); ?>
      <select onchange="
        const u=new URL(location.href);
        if(this.value){u.searchParams.set('orderby',this.value);}else{u.searchParams.delete('orderby');}
        location.href=u.toString();
      ">
        <option value="">Most reviewed</option>
        <option value="rating"   <?= $orderby==='rating'?'selected':''?>>Highest rated</option>
        <option value="reviews"  <?= $orderby==='reviews'?'selected':''?>>Most reviews</option>
        <option value="date"     <?= $orderby==='date'?'selected':''?>>Recently added</option>
      </select>
    </div>
  </div>
</div>

<!-- Body -->
<div class="container">
  <div class="cat-body">

    <aside class="filters">
      <form method="get" action="<?= esc_url(strtok($_SERVER['REQUEST_URI'], '?')) ?>" id="filterForm">
        <?php if ($is_search): ?>
          <input type="hidden" name="s" value="<?= esc_attr(get_search_query()) ?>">
          <input type="hidden" name="post_type" value="software">
        <?php endif; ?>

        <div class="filter-group">
          <h6>Industry <small><?= count($industries) ?></small></h6>
          <?php foreach ($industries as $t): ?>
            <label>
              <span class="left"><input type="checkbox" name="industry[]" value="<?= esc_attr($t->slug) ?>" <?= inp_checked('industry', $t->slug) ?> /> <?= esc_html($t->name) ?></span>
              <span class="count"><?= (int) $t->count ?></span>
            </label>
          <?php endforeach; ?>
        </div>

        <div class="filter-group">
          <h6>Category <small><?= count($categories) ?></small></h6>
          <?php foreach ($categories as $t): ?>
            <label>
              <span class="left"><input type="checkbox" name="sw_category[]" value="<?= esc_attr($t->slug) ?>" <?= inp_checked('sw_category', $t->slug) ?> /> <?= esc_html($t->name) ?></span>
              <span class="count"><?= (int) $t->count ?></span>
            </label>
          <?php endforeach; ?>
        </div>

        <div class="filter-group">
          <h6>Country of origin</h6>
          <?php foreach ($countries as $t): ?>
            <label>
              <span class="left"><input type="checkbox" name="country[]" value="<?= esc_attr($t->slug) ?>" <?= inp_checked('country', $t->slug) ?> /> <?= inp_country_flag($t->name) ?> <?= esc_html($t->name) ?></span>
              <span class="count"><?= (int) $t->count ?></span>
            </label>
          <?php endforeach; ?>
        </div>

        <div class="filter-group">
          <h6>Deployment</h6>
          <?php foreach (['cloud'=>'Cloud / SaaS','on-premise'=>'On-premise','hybrid'=>'Hybrid'] as $v=>$l): ?>
            <label><span class="left"><input type="radio" name="deployment" value="<?= esc_attr($v) ?>" <?= inp_get('deployment')===$v?'checked':'' ?> /> <?= esc_html($l) ?></span></label>
          <?php endforeach; ?>
          <label><span class="left"><input type="radio" name="deployment" value="" <?= inp_get('deployment')===''?'checked':'' ?> /> Any</span></label>
        </div>

        <div class="filter-group">
          <h6>Pricing model</h6>
          <?php foreach (['one-time'=>'One-time license','subscription'=>'Subscription / yearly','per user'=>'Per user / month','custom'=>'Custom quote','free'=>'Free / open-source'] as $v=>$l): ?>
            <label><span class="left"><input type="radio" name="pricing_model" value="<?= esc_attr($v) ?>" <?= inp_get('pricing_model')===$v?'checked':'' ?> /> <?= esc_html($l) ?></span></label>
          <?php endforeach; ?>
          <label><span class="left"><input type="radio" name="pricing_model" value="" <?= inp_get('pricing_model')===''?'checked':'' ?> /> Any</span></label>
        </div>

        <div class="filter-group">
          <h6>Mobile &amp; API</h6>
          <label><span class="left"><input type="checkbox" name="mobile" value="1" <?= !empty($_GET['mobile'])?'checked':'' ?> /> Native mobile app</span></label>
          <label><span class="left"><input type="checkbox" name="api"    value="1" <?= !empty($_GET['api'])?'checked':'' ?> /> Public REST API</span></label>
          <label><span class="left"><input type="checkbox" name="trial"  value="1" <?= !empty($_GET['trial'])?'checked':'' ?> /> Free trial</span></label>
        </div>

        <div class="filter-group">
          <h6>Review score</h6>
          <?php foreach (['4.5'=>'4.5 & up', '4'=>'4.0 & up', '3.5'=>'3.5 & up'] as $v=>$l): ?>
            <label><span class="left"><input type="radio" name="min_rating" value="<?= esc_attr($v) ?>" <?= (inp_get('min_rating')===$v)?'checked':'' ?> /> <?= esc_html($l) ?></span></label>
          <?php endforeach; ?>
          <label><span class="left"><input type="radio" name="min_rating" value="" <?= inp_get('min_rating')===''?'checked':'' ?> /> Any</span></label>
        </div>

        <button type="submit" class="btn primary" style="width:100%; margin-top:14px;">Apply filters</button>
        <a href="<?= esc_url(strtok($_SERVER['REQUEST_URI'], '?')) ?>" class="btn ghost" style="width:100%; margin-top:6px; display:block; text-align:center; border:1px solid var(--border);">Reset</a>
      </form>
    </aside>

    <div>
      <div class="results-bar">
        <span>Showing <strong style="color:var(--text);"><?= (int) $GLOBALS['wp_query']->post_count ?></strong> of <strong style="color:var(--text);"><?= (int) $GLOBALS['wp_query']->found_posts ?></strong> matching software</span>
      </div>

      <div style="display:grid; gap:14px;">
        <?php
        $i = 0;
        if (have_posts()) :
            while (have_posts()) : the_post();
                inp_render_software_row(get_the_ID(), $i === 0);
                $i++;
            endwhile;
        else :
            echo '<div style="padding:40px; text-align:center; border:1px dashed var(--border-strong); border-radius:12px; color:var(--text-muted);">';
            echo '<p style="margin:0 0 6px; font-size:15px; color:var(--text);">No software matches these filters.</p>';
            echo '<p style="margin:0; font-size:13px;">Try removing a filter, or <a href="' . esc_url(strtok($_SERVER['REQUEST_URI'], '?')) . '" style="color:var(--accent-deep); font-weight:600;">reset all</a>.</p>';
            echo '</div>';
        endif;
        ?>
      </div>

      <div class="pagination">
        <?php
        echo paginate_links([
            'mid_size'  => 1,
            'prev_text' => '←',
            'next_text' => '→',
            'add_args'  => array_filter($_GET, fn($k) => $k !== 'paged', ARRAY_FILTER_USE_KEY),
        ]);
        ?>
      </div>

      <!-- Buyer's guide bottom CTA -->
      <div style="margin-top: 48px; border-radius: 16px; padding: 28px; background: var(--accent-soft); display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: center;">
        <div>
          <div style="font-size:11px; letter-spacing:.1em; text-transform:uppercase; color:var(--accent-deep); font-weight:700; margin-bottom:6px;">Free buyer's guide</div>
          <h3 style="font-size: 20px; font-weight: 700; margin: 0 0 4px; color: var(--ink);">12 questions every Nepali buyer should ask before signing.</h3>
          <p style="margin:0; font-size:13.5px; color: var(--text-muted);">Used by 1,400+ admins · 4-page PDF · no email required.</p>
        </div>
        <a href="#" class="btn primary">Download PDF →</a>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>
