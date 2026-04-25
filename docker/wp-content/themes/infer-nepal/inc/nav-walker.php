<?php
/**
 * Custom mega-menu walker for the Primary navigation.
 *
 * Renders top-level menu items (added in Appearance → Menus) using the existing
 * .nav-item / .mega CSS structure, so admins can:
 *   - add / reorder top-level items (these become mega triggers if they have children,
 *     or plain links if not)
 *   - add child items as the columns of links inside the mega panel
 *   - write a "Description" per menu item (enable via Screen Options → Description)
 *
 * Plus the theme exposes per-trigger Customizer fields for the optional
 * right-side "feature card" (image + headline + URL) shown inside each mega.
 */

if (!defined('ABSPATH')) { exit; }

class INP_Mega_Walker extends Walker_Nav_Menu {

    public function start_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0) {
            // The mega panel that wraps all child items
            $output .= '<div class="mega wide" role="menu"><div class="mega-body"><div class="mega-left"><div class="mega-col">';
        }
    }

    public function end_lvl(&$output, $depth = 0, $args = null) {
        if ($depth === 0) {
            // Optional right-side "feature card" — pulled from Customizer per top-level item
            $output .= '</div></div>';
            $output .= INP_Mega_Walker::render_feature_card($args);
            $output .= '</div></div>';
        }
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $url    = !empty($item->url) ? esc_url($item->url) : '#';
        $title  = esc_html($item->title);
        $desc   = !empty($item->description) ? esc_html($item->description) : '';
        $hasKids = in_array('menu-item-has-children', (array) $item->classes, true);

        if ($depth === 0) {
            if ($hasKids) {
                // Mega trigger
                $output .= '<div class="nav-item" data-menu-id="' . (int) $item->ID . '">';
                $output .= '<button class="nav-trigger" aria-haspopup="true">' . $title;
                $output .= '<svg class="caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>';
                $output .= '</button>';
            } else {
                // Plain link
                $output .= '<a class="nav-trigger" href="' . $url . '">' . $title . '</a>';
            }
        } else {
            // Child item — render as a .mega-link tile
            $output .= '<a href="' . $url . '" class="mega-link">';
            $output .= '<span class="ico"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M9 12l2.5 2.5L16 9"/></svg></span>';
            $output .= '<span class="body"><span class="title">' . $title . '</span>';
            if ($desc) $output .= '<span class="desc">' . $desc . '</span>';
            $output .= '</span></a>';
        }
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        if ($depth === 0 && in_array('menu-item-has-children', (array) $item->classes, true)) {
            $output .= '</div>'; // close .nav-item
        }
    }

    /**
     * Render the optional right-side feature card for a top-level item.
     * Settings live in Customizer → Brand → Mega cards.
     */
    public static function render_feature_card($args) {
        // We can't easily know which menu item this lvl belongs to from inside
        // end_lvl, so we walk back up via the buffer position. As a simpler
        // approximation, the closest preceding data-menu-id wins.
        global $inp_current_top_id;
        if (!$inp_current_top_id) return '';

        $img   = get_theme_mod("inp_mega_{$inp_current_top_id}_img", '');
        $tag   = get_theme_mod("inp_mega_{$inp_current_top_id}_tag", '');
        $title = get_theme_mod("inp_mega_{$inp_current_top_id}_title", '');
        $desc  = get_theme_mod("inp_mega_{$inp_current_top_id}_desc", '');
        $url   = get_theme_mod("inp_mega_{$inp_current_top_id}_url", '');

        if (!$tag && !$title && !$desc) return ''; // nothing configured

        $bg = $img ? "background-image:url('" . esc_url($img) . "'); background-size:cover; background-position:center;"
                   : "background: var(--accent-soft);";

        return '<div class="mega-right">'
             . '<a href="' . esc_url($url ?: '#') . '" class="feature-card" style="aspect-ratio:16/10; ' . $bg . ' display:flex; align-items:end; padding:18px;">'
             . '<div style="position:relative; z-index:2;">'
             . ($tag ? '<span class="tag" style="background: var(--accent); color:#fff; padding:3px 8px; border-radius:999px; font-size:10px; font-weight:700; letter-spacing:.06em;">' . esc_html($tag) . '</span>' : '')
             . ($title ? '<h5 style="margin:8px 0 4px; color:#fff; font-size:16px; font-weight:700; text-shadow: 0 1px 2px rgba(0,0,0,.35);">' . esc_html($title) . '</h5>' : '')
             . ($desc ? '<p style="margin:0; color:rgba(255,255,255,.9); font-size:12px;">' . esc_html($desc) . '</p>' : '')
             . '</div></a></div>';
    }
}

/* Track which top-level menu item we're inside, so render_feature_card() can pick
 * up the right Customizer mods. We hook display_element to set the global. */
add_filter('walker_nav_menu_start_el', function ($item_output, $item, $depth, $args) {
    if ($depth === 0) {
        $GLOBALS['inp_current_top_id'] = (int) $item->ID;
    }
    return $item_output;
}, 10, 4);

/* =================================================================
 * Customizer panel: per-mega feature-card editor
 * Adds a section with editable fields for every top-level menu item
 * in the Primary menu that has children.
 * ================================================================= */
add_action('customize_register', function (WP_Customize_Manager $wp_customize) {
    $locations = get_nav_menu_locations();
    if (empty($locations['primary'])) return;
    $items = wp_get_nav_menu_items($locations['primary']);
    if (!$items) return;

    // top-level items (parent = 0) that HAVE children
    $top_level = array_filter($items, fn($i) => (int) $i->menu_item_parent === 0);
    $kids_of   = [];
    foreach ($items as $i) $kids_of[(int)$i->menu_item_parent][] = $i;

    if (empty($top_level)) return;

    $wp_customize->add_section('inp_mega_cards', [
        'title'       => __('Mega menu — feature cards', 'infer-nepal'),
        'panel'       => 'inp_brand',
        'description' => 'For each top-level menu item that has child items, the right side of its mega panel can show a "feature card". Leave blank to hide.',
    ]);

    foreach ($top_level as $top) {
        if (empty($kids_of[$top->ID])) continue;

        $heading_id = "inp_mega_{$top->ID}_heading";
        $wp_customize->add_setting($heading_id, ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control($heading_id, [
            'type'    => 'hidden',
            'section' => 'inp_mega_cards',
            'description' => '<h4 style="margin: 16px 0 6px; padding-top: 12px; border-top:1px solid #ddd;">' . esc_html($top->title) . '</h4>',
        ]);

        $fields = [
            'tag'   => ['Tag (small label)', 'text'],
            'title' => ['Title',              'text'],
            'desc'  => ['Description',        'text'],
            'url'   => ['Link URL',           'url'],
            'img'   => ['Image URL (optional)', 'url'],
        ];
        foreach ($fields as $f => [$label, $type]) {
            $sid = "inp_mega_{$top->ID}_{$f}";
            $wp_customize->add_setting($sid, ['default' => '', 'sanitize_callback' => $type === 'url' ? 'esc_url_raw' : 'sanitize_text_field']);
            $wp_customize->add_control($sid, ['type' => $type, 'label' => esc_html($top->title) . ': ' . $label, 'section' => 'inp_mega_cards']);
        }
    }
});
