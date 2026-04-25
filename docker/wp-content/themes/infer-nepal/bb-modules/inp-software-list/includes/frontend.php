<?php
/* $module->settings has: industry, category, count, orderby, display, featured_first */
$s = $module->settings;
$count    = max(1, (int) ($s->count ?? 6));
$industry = esc_attr($s->industry ?? '');
$category = esc_attr($s->category ?? '');
$orderby  = esc_attr($s->orderby ?? 'rating');
$display  = $s->display ?? 'cards';

if ($display === 'rows') {
    echo do_shortcode(sprintf(
        '[inp_software_list count="%d" industry="%s" category="%s" featured_first="%d"]',
        $count, $industry, $category, (int) ($s->featured_first ?? 1)
    ));
} else {
    echo do_shortcode(sprintf(
        '[inp_top_software count="%d" industry="%s" category="%s" orderby="%s"]',
        $count, $industry, $category, $orderby
    ));
}
