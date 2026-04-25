<?php
get_header();

$current_term  = is_tax() ? get_queried_object() : null;
$archive_title = $current_term ? $current_term->name : 'All software';
$archive_desc  = $current_term ? $current_term->description : 'Browse the full catalogue of B2B software tools listed on Infer Nepal.';
$total         = (int) wp_count_posts('software')->publish;
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

<section class="cat-hero">
  <div class="container">
    <div class="row">
      <div>
        <h1><?= esc_html($archive_title) ?></h1>
        <p><?= esc_html($archive_desc ?: ($total . ' verified software in the catalogue.')) ?></p>
      </div>
    </div>
  </div>
</section>

<div class="filter-bar">
  <div class="container" style="display: flex; justify-content: space-between; align-items: center; gap: 20px; width: 100%;">
    <div class="chip-row">
      <span style="font-size: 13px; color: var(--text-muted); font-weight: 500;">Browse:</span>
      <?php
      $industries = get_terms(['taxonomy' => 'industry', 'hide_empty' => false, 'number' => 8]);
      foreach ($industries as $t) {
          $active = ($current_term && $current_term->term_id === $t->term_id) ? 'chip-active' : 'spec-pill';
          echo '<a href="' . esc_url(get_term_link($t)) . '" class="' . $active . '">' . esc_html($t->name) . '</a> ';
      }
      ?>
    </div>
    <div class="sort">
      <span style="color: var(--text-muted);">Sort:</span>
      <select onchange="if(this.value)location.href=this.value">
        <option value="">Most reviewed</option>
        <option value="?orderby=rating">Highest rated</option>
        <option value="?orderby=date">Recently added</option>
      </select>
    </div>
  </div>
</div>

<div class="container">
  <div class="cat-body">

    <aside class="filters">
      <div class="filter-group">
        <h6>Country of origin</h6>
        <?php foreach (get_terms(['taxonomy'=>'country','hide_empty'=>false]) as $t): ?>
          <label><span class="left"><input type="checkbox"/> <?= inp_country_flag($t->name) ?> <?= esc_html($t->name) ?></span><span class="count"><?= (int) $t->count ?></span></label>
        <?php endforeach; ?>
      </div>
      <div class="filter-group">
        <h6>Category</h6>
        <?php foreach (get_terms(['taxonomy'=>'sw_category','hide_empty'=>false]) as $t): ?>
          <label><span class="left"><input type="checkbox"/> <?= esc_html($t->name) ?></span><span class="count"><?= (int) $t->count ?></span></label>
        <?php endforeach; ?>
      </div>
      <div class="filter-group">
        <h6>Mobile &amp; API</h6>
        <label><span class="left"><input type="checkbox"/> Native mobile app</span></label>
        <label><span class="left"><input type="checkbox"/> Public REST API</span></label>
        <label><span class="left"><input type="checkbox"/> Free trial</span></label>
      </div>
    </aside>

    <div>
      <div class="results-bar">
        <span>Showing <strong style="color:var(--text);"><?= (int) $GLOBALS['wp_query']->found_posts ?></strong> matching software</span>
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
            echo '<p style="color:var(--text-muted);">No software found in this section.</p>';
        endif;
        ?>
      </div>

      <div class="pagination">
        <?php
        echo paginate_links([
            'mid_size'  => 1,
            'prev_text' => '←',
            'next_text' => '→',
        ]);
        ?>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>
