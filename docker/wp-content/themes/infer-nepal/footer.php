<!-- Footer -->
<footer class="site">
  <div class="container">
    <div class="f-grid">
      <div>
        <a href="<?= esc_url(home_url('/')) ?>" class="brand">
          <?php if (has_custom_logo()) the_custom_logo(); else : ?>
            <img src="<?= esc_url(get_theme_file_uri('assets/logo.png')) ?>" alt="<?php bloginfo('name'); ?>" class="brand-logo" />
          <?php endif; ?>
        </a>
        <p style="color:var(--text-muted); font-size: 13px; margin-top:12px; max-width: 320px;">
          <?= esc_html(get_theme_mod('inp_footer_blurb', "Nepal's independent B2B software discovery platform.")) ?>
        </p>
        <form class="newsletter" action="#" method="post">
          <input name="email" placeholder="you@company.com" />
          <button type="submit">Subscribe</button>
        </form>
        <div class="f-tag">One short digest every Friday. Nepali SMB software news.</div>
      </div>

      <?php
      $footer_cols = [
          'footer-industries' => 'Industries',
          'footer-categories' => 'Categories',
          'footer-vendors'    => 'For vendors',
          'footer-company'    => 'Company',
      ];
      foreach ($footer_cols as $location => $heading) :
        if (!has_nav_menu($location)) :
          // Sensible default — link to the relevant taxonomy/CPT archive
          ?>
          <div>
            <h5><?= esc_html($heading) ?></h5>
            <ul>
              <?php
              if ($location === 'footer-industries') {
                  $terms = get_terms(['taxonomy'=>'industry','hide_empty'=>false,'number'=>8]);
                  foreach ($terms as $t) echo '<li><a href="' . esc_url(get_term_link($t)) . '">' . esc_html($t->name) . '</a></li>';
              } elseif ($location === 'footer-categories') {
                  $terms = get_terms(['taxonomy'=>'sw_category','hide_empty'=>false,'number'=>8]);
                  foreach ($terms as $t) echo '<li><a href="' . esc_url(get_term_link($t)) . '">' . esc_html($t->name) . '</a></li>';
              } elseif ($location === 'footer-vendors') {
                  echo '<li><a href="#">List your software</a></li>';
                  echo '<li><a href="#">Premium listing</a></li>';
                  echo '<li><a href="#">Lead generation</a></li>';
                  echo '<li><a href="#">Advertise</a></li>';
              } else {
                  echo '<li><a href="' . esc_url(home_url('/about/')) . '">About</a></li>';
                  echo '<li><a href="#">How we score</a></li>';
                  echo '<li><a href="#">Editorial policy</a></li>';
                  echo '<li><a href="' . esc_url(home_url('/contact/')) . '">Contact</a></li>';
              }
              ?>
            </ul>
          </div>
          <?php
        else : ?>
          <div>
            <h5><?= esc_html($heading) ?></h5>
            <?php wp_nav_menu(['theme_location' => $location, 'container' => false, 'depth' => 1, 'items_wrap' => '<ul>%3$s</ul>']); ?>
          </div>
        <?php endif;
      endforeach; ?>

    </div>
    <div class="f-bottom">
      <span><?= wp_kses_post(get_theme_mod('inp_footer_copyright', '© ' . date('Y') . ' Infer Nepal · infernepal.com')) ?></span>
      <span><?= esc_html(get_theme_mod('inp_footer_tagline', 'Made in Kathmandu 🇳🇵')) ?></span>
    </div>
  </div>
</footer>

<div class="menu-backdrop" aria-hidden="true"></div>
<?php wp_footer(); ?>
</body>
</html>
