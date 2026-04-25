<?php get_header(); ?>

<?php
/* If the static "front page" is set in Settings → Reading and contains
 * blocks, render those (so admins can fully arrange the page using
 * block patterns). Otherwise render the default home layout. */
if (have_posts()) :
    while (have_posts()) : the_post();
        if (has_blocks(get_the_content())) {
            echo '<div class="page-content">';
            the_content();
            echo '</div>';
        } else {
            echo do_shortcode(<<<HTML
            [inp_industry_grid count="10"]
            HTML);
        }
    endwhile;
else :
    /* No page assigned: render the default home composition */
    ?>
    <section class="hero">
      <div class="container">
        <div class="eyebrow"><span class="dot"></span> 642 software · 18,400+ verified reviews · updated weekly</div>
        <h1>Find the right software <em>for every Nepali business.</em></h1>
        <p class="lede">Independent reviews, transparent pricing and side-by-side comparison for ERP, accounting, school, hotel, hospital and HR software — built for buyers in 🇳🇵 Nepal.</p>
        <div class="hero-search">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="color:var(--text-subtle)"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
          <form role="search" method="get" action="<?= esc_url(home_url('/')) ?>" style="display:contents;">
            <input name="s" placeholder="Try Tally vs Swastik, school ERP, hospital HMS…" />
            <input type="hidden" name="post_type" value="software" />
            <button class="btn primary" type="submit">Search</button>
          </form>
        </div>
      </div>
    </section>

    <section style="padding-top: 30px;">
      <div class="container">
        <div class="sec-head">
          <div>
            <h2>Browse by industry</h2>
            <p>Software that's been deployed in your sector — by people you can call.</p>
          </div>
          <a href="<?= esc_url(get_post_type_archive_link('software')) ?>" class="more">All industries →</a>
        </div>
        <?= do_shortcode('[inp_industry_grid count="10"]') ?>
      </div>
    </section>

    <section style="padding-top: 20px;">
      <div class="container">
        <div class="sec-head">
          <div>
            <h2>Top-rated software in Nepal</h2>
            <p>The most-reviewed B2B tools by buyers like you, this quarter.</p>
          </div>
          <a href="<?= esc_url(get_post_type_archive_link('software')) ?>" class="more">View all →</a>
        </div>
        <?= do_shortcode('[inp_top_software count="6" orderby="rating"]') ?>
      </div>
    </section>

    <section>
      <div class="container">
        <div class="sec-head">
          <div>
            <h2>Featured for School &amp; College</h2>
            <p>Tools for fees, attendance, exam and parent communication.</p>
          </div>
        </div>
        <?= do_shortcode('[inp_software_list count="4" industry="school-college" featured_first="1"]') ?>
      </div>
    </section>

    <?php
endif;

get_footer();
