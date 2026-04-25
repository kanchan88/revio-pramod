<?php
$s = $module->settings;
$count   = max(1, (int) ($s->count ?? 10));
$columns = max(1, min(6, (int) ($s->columns ?? 5)));
$terms = get_terms(['taxonomy' => 'industry', 'hide_empty' => false, 'number' => $count]);
if (is_wp_error($terms) || empty($terms)) return;
printf('<div class="cat-grid" style="grid-template-columns: repeat(%d, 1fr);">', $columns);
foreach ($terms as $term) inp_render_industry_card($term);
echo '</div>';
