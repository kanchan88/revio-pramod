<?php
return [
    'title'       => 'Vendor stats CTA (List your software)',
    'description' => 'Dark "list your software" panel — numbers come from Customizer → Brand → Platform stats.',
    'categories'  => ['infer-nepal'],
    'keywords'    => ['vendors', 'stats', 'cta'],
    'content'     => <<<HTML
<!-- wp:html -->
<section style="padding-top: 0;">
  <div class="container">
    <div class="compare-teaser" style="background: var(--ink); color: #fff; border-color: var(--ink);">
      <div>
        <h2 style="color:#fff;">List your software on Infer Nepal.</h2>
        <p style="color: rgba(255,255,255,.78);">Reach Nepali decision-makers shopping for ERP, accounting, school, hotel and HR tools. Premium and Top listings available.</p>
        <a href="#" class="btn warm">Become a vendor →</a>
      </div>
      [inp_vendor_stats]
    </div>
  </div>
</section>
<!-- /wp:html -->
HTML
];
