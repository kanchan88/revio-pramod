<?php
return [
    'title'       => 'Industry tiles (live)',
    'description' => 'Auto-populated grid of industry taxonomies — pulls live counts from the Software CPT.',
    'categories'  => ['infer-nepal'],
    'keywords'    => ['industries', 'category', 'grid'],
    'content'     => <<<HTML
<!-- wp:html -->
<section style="padding-top: 30px;">
  <div class="container">
    <div class="sec-head">
      <div>
        <h2>Browse by industry</h2>
        <p>Software that's been deployed in your sector — by people you can call.</p>
      </div>
      <a href="/software/" class="more">All industries →</a>
    </div>
    [inp_industry_grid count="10"]
  </div>
</section>
<!-- /wp:html -->
HTML
];
