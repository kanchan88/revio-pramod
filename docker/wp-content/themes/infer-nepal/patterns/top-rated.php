<?php
return [
    'title'       => 'Top-rated software (live, 6 cards)',
    'description' => 'Auto-populated grid of the 6 highest-rated software in the catalogue.',
    'categories'  => ['infer-nepal'],
    'keywords'    => ['software', 'top', 'rated', 'cards'],
    'content'     => <<<HTML
<!-- wp:html -->
<section style="padding-top: 20px;">
  <div class="container">
    <div class="sec-head">
      <div>
        <h2>Top-rated software in Nepal</h2>
        <p>The most-reviewed B2B tools by buyers like you, this quarter.</p>
      </div>
      <a href="/software/" class="more">View all →</a>
    </div>
    [inp_top_software count="6" orderby="rating"]
  </div>
</section>
<!-- /wp:html -->
HTML
];
