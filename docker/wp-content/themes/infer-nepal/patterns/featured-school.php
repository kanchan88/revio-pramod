<?php
return [
    'title'       => 'Featured for an industry (live list)',
    'description' => 'Vertical software list — defaults to School & College. Change the industry slug to repurpose.',
    'categories'  => ['infer-nepal'],
    'keywords'    => ['list', 'industry', 'featured'],
    'content'     => <<<HTML
<!-- wp:html -->
<section>
  <div class="container">
    <div class="sec-head">
      <div>
        <h2>Featured for School &amp; College</h2>
        <p>Tools for fees, attendance, exam and parent communication.</p>
      </div>
      <a href="/industry/school-college/" class="more">All school software →</a>
    </div>
    [inp_software_list count="4" industry="school-college" featured_first="1"]
  </div>
</section>
<!-- /wp:html -->
HTML
];
