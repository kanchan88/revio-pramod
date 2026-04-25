<form role="search" method="get" class="hero-search" action="<?= esc_url(home_url('/')) ?>" style="max-width:520px;">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="color:var(--text-subtle)"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
  <input type="search" name="s" placeholder="<?php esc_attr_e('Search software…', 'infer-nepal'); ?>" value="<?= esc_attr(get_search_query()) ?>"/>
  <input type="hidden" name="post_type" value="software" />
  <button class="btn primary sm" type="submit">Search</button>
</form>
