<?php
/**
 * Plugin Name: Infer Nepal — Software Products
 * Description: Registers the "Software" custom post type, the Industry / Category / Country taxonomies,
 *              and seeds the product catalog from Product-Domains.xlsx on first boot.
 *              Drop-in must-use plugin; no activation step required.
 * Version:     1.0.0
 * Author:      Infer Nepal
 */

if (!defined('ABSPATH')) { exit; }

/* =================================================================
   1. Custom post type:  software
   ================================================================= */
add_action('init', function () {

    register_post_type('software', [
        'label'         => 'Software',
        'labels'        => [
            'name'               => 'Software',
            'singular_name'      => 'Software',
            'add_new_item'       => 'Add new software',
            'edit_item'          => 'Edit software',
            'view_item'          => 'View software',
            'search_items'       => 'Search software',
            'not_found'          => 'No software found',
            'menu_name'          => 'Software',
        ],
        'public'        => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-screenoptions',
        'menu_position' => 6,
        'supports'      => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'rewrite'       => ['slug' => 'software'],
        'show_in_rest'  => true,
    ]);

    /* ---------- Taxonomies ---------- */
    register_taxonomy('industry', 'software', [
        'label'             => 'Industry',
        'hierarchical'      => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'industry'],
    ]);

    register_taxonomy('sw_category', 'software', [
        'label'             => 'Category',
        'hierarchical'      => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'category'],
    ]);

    register_taxonomy('country', 'software', [
        'label'             => 'Country of origin',
        'hierarchical'      => false,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'country'],
    ]);
});

/* =================================================================
   2. Register custom-field meta keys (so they show in REST + admin)
   ================================================================= */
add_action('init', function () {
    $fields = [
        'vendor'             => 'string',
        'website_url'        => 'string',
        'pricing_model'      => 'string',  // One-time | Subscription | Per user/month | Custom | Free
        'price'              => 'string',
        'maturity'           => 'string',  // years in market or "Mature 30+ yrs"
        'has_mobile_app'     => 'boolean',
        'has_api'            => 'boolean',
        'free_trial'         => 'boolean',
        'deployment'         => 'string',  // On-premise | Cloud | Hybrid
        'company_size'       => 'string',  // 1-10 | 11-50 | 51-200 | 200+
        'rating'             => 'number',
        'review_count'       => 'integer',
        'logo_url'           => 'string',
        'included_features'  => 'string',  // comma separated
        'excluded_features'  => 'string',
    ];
    foreach ($fields as $key => $type) {
        register_post_meta('software', $key, [
            'type'         => $type,
            'single'       => true,
            'show_in_rest' => true,
            'auth_callback' => function () { return current_user_can('edit_posts'); },
        ]);
    }
});

/* =================================================================
   3. Seed the product catalogue (idempotent — runs once)
   ================================================================= */
add_action('init', function () {
    if (get_option('inp_products_seeded') === '1') return;
    if (!post_type_exists('software'))            return;

    $base = trailingslashit(content_url()) . 'uploads/logos/';

    /* Industry & category terms */
    $industries = [
        'School & College', 'Small Manufacturing', 'Large Manufacturing',
        'Hotels', 'Trading', 'Online Stores', 'NGO',
        'Travel & Trekking', 'Hydro Power', 'Hospital',
    ];
    foreach ($industries as $name) {
        if (!term_exists($name, 'industry')) wp_insert_term($name, 'industry');
    }
    $categories = [
        'Accounting', 'ERP', 'School ERP', 'Hotel PMS', 'Hospital HMS',
        'HRMS', 'Inventory', 'CRM', 'POS', 'LMS', 'Document Management',
    ];
    foreach ($categories as $name) {
        if (!term_exists($name, 'sw_category')) wp_insert_term($name, 'sw_category');
    }

    /* Product catalogue — sourced from Product-Domains.xlsx */
    $products = [

        /* ---------- Tally Prime (Editor's pick) ---------- */
        [
            'title'      => 'Tally Prime',
            'excerpt'    => 'Desktop-first accounting, inventory, GST/VAT and compliance suite trusted by 2M+ SMBs across South Asia.',
            'content'    => 'Tally Prime ships double-entry bookkeeping, multi-godown inventory, payroll, banking, e-invoicing and 400+ pre-built reports. Designed to be operated by a single accountant without IT support.',
            'meta'       => [
                'vendor'            => 'Tally Solutions Pvt. Ltd.',
                'website_url'       => 'https://tallysolutions.com',
                'pricing_model'     => 'One-time',
                'price'             => 'NPR 22,500 (Silver) · NPR 67,500 (Gold)',
                'maturity'          => 'Mature · 30+ yrs',
                'has_mobile_app'    => true,
                'has_api'           => true,
                'free_trial'        => true,
                'deployment'        => 'On-premise + Cloud add-on',
                'company_size'      => '11-200',
                'rating'            => 4.6,
                'review_count'      => 1240,
                'logo_url'          => $base . 'tally.png',
                'included_features' => 'Books & Ledgers, VAT Return, Inventory, Payroll, Banking, GST, e-Invoicing',
                'excluded_features' => 'Native CRM, Project costing, Hospital module, E-commerce storefront',
            ],
            'industries' => ['Trading', 'Small Manufacturing', 'Hotels', 'Online Stores'],
            'categories' => ['Accounting', 'Inventory'],
            'country'    => 'India',
        ],

        /* ---------- ERP ---------- */
        [
            'title' => 'Swastik',
            'excerpt' => 'Made-in-Nepal ERP for trading and small manufacturing with strong VAT-ready compliance.',
            'meta' => [
                'vendor' => 'Swastik Technosoft',
                'pricing_model' => 'One-time',
                'price' => 'NPR 18,000',
                'maturity' => '15+ yrs',
                'has_mobile_app' => true, 'has_api' => false, 'free_trial' => true,
                'deployment' => 'On-premise + Cloud',
                'rating' => 4.5, 'review_count' => 312,
                'included_features' => 'ERP, VAT-NP, POS, Manufacturing, Inventory',
            ],
            'industries' => ['Small Manufacturing', 'Trading'],
            'categories' => ['ERP', 'POS'],
            'country' => 'Nepal',
        ],
        [
            'title' => 'Synergy ERP',
            'excerpt' => 'Cloud-first Nepali ERP for hotels, manufacturing and trading. Built and supported in Kathmandu.',
            'meta' => [
                'vendor' => 'Synergy Tech',
                'pricing_model' => 'Per user / month', 'price' => 'NPR 1,200 / user / mo',
                'maturity' => '8 yrs',
                'has_mobile_app' => true, 'has_api' => true, 'free_trial' => true,
                'deployment' => 'Cloud',
                'rating' => 4.2, 'review_count' => 186,
                'included_features' => 'Cloud ERP, PMS, HR, Multi-branch',
            ],
            'industries' => ['Hotels', 'Small Manufacturing', 'Trading'],
            'categories' => ['ERP', 'Hotel PMS', 'HRMS'],
            'country' => 'Nepal',
        ],
        [
            'title' => 'Odoo Community',
            'excerpt' => 'Open-source modular ERP. Sales, CRM, inventory, HR, project — all in one. Self-hostable.',
            'meta' => [
                'vendor' => 'Odoo S.A.',
                'pricing_model' => 'Free', 'price' => 'Free (community)',
                'maturity' => '20+ yrs',
                'has_mobile_app' => true, 'has_api' => true, 'free_trial' => true,
                'deployment' => 'Cloud + Self-host',
                'rating' => 4.4, 'review_count' => 2108,
                'logo_url' => $base . 'odoo.svg',
                'included_features' => 'ERP, CRM, HR, Project, Inventory',
            ],
            'industries' => ['Small Manufacturing', 'Trading', 'Online Stores'],
            'categories' => ['ERP', 'CRM', 'HRMS'],
            'country' => 'Belgium',
        ],
        [
            'title' => 'SAP Business One',
            'excerpt' => 'Enterprise-grade ERP suite for mid-market. Strong financial controls and global compliance.',
            'meta' => [
                'vendor' => 'SAP SE',
                'pricing_model' => 'Per user / month', 'price' => 'USD 108 / user / mo',
                'maturity' => 'Mature · 50+ yrs',
                'has_mobile_app' => true, 'has_api' => true, 'free_trial' => false,
                'deployment' => 'On-premise + Cloud',
                'rating' => 4.3, 'review_count' => 942,
                'logo_url' => $base . 'sap.svg',
                'included_features' => 'Finance, MRP, Inventory, CRM, Reporting',
            ],
            'industries' => ['Large Manufacturing', 'Trading'],
            'categories' => ['ERP', 'Accounting'],
            'country' => 'Germany',
        ],
        [
            'title' => 'Microsoft Dynamics 365',
            'excerpt' => 'Cloud business apps from Microsoft — finance, supply chain, sales, customer service.',
            'meta' => [
                'vendor' => 'Microsoft',
                'pricing_model' => 'Per user / month', 'price' => 'USD 70 / user / mo',
                'maturity' => 'Mature',
                'has_mobile_app' => true, 'has_api' => true, 'free_trial' => true,
                'deployment' => 'Cloud',
                'rating' => 4.2, 'review_count' => 654,
                'included_features' => 'Finance, Sales, Supply Chain, Customer Service',
            ],
            'industries' => ['Large Manufacturing', 'Hotels'],
            'categories' => ['ERP', 'CRM'],
            'country' => 'USA',
        ],
        [
            'title' => 'NetSuite',
            'excerpt' => 'Oracle\'s cloud ERP for fast-growing mid-market companies.',
            'meta' => [
                'vendor' => 'Oracle Corporation',
                'pricing_model' => 'Subscription', 'price' => 'USD 999 / mo + USD 99 / user',
                'maturity' => 'Mature · 20+ yrs',
                'has_mobile_app' => true, 'has_api' => true, 'free_trial' => true,
                'deployment' => 'Cloud',
                'rating' => 4.1, 'review_count' => 482,
                'included_features' => 'Financials, CRM, Inventory, E-commerce',
            ],
            'industries' => ['Large Manufacturing', 'Online Stores'],
            'categories' => ['ERP', 'Accounting'],
            'country' => 'USA',
        ],
        [
            'title' => 'Sage 300',
            'excerpt' => 'Mid-market accounting and operations software with strong multi-currency support.',
            'meta' => [
                'vendor' => 'Sage Group plc',
                'pricing_model' => 'Subscription', 'price' => 'USD 1,800 / yr',
                'maturity' => 'Mature · 40+ yrs',
                'has_mobile_app' => false, 'has_api' => true, 'free_trial' => true,
                'deployment' => 'On-premise + Cloud',
                'rating' => 4.0, 'review_count' => 320,
                'logo_url' => $base . 'sage.svg',
                'included_features' => 'Accounting, Multi-currency, Inventory, Project Costing',
            ],
            'industries' => ['Trading', 'Small Manufacturing'],
            'categories' => ['Accounting', 'ERP'],
            'country' => 'UK',
        ],
        [
            'title' => 'Marg ERP 9+',
            'excerpt' => 'Industry-vertical ERP from India — pharma, FMCG, distribution and POS.',
            'meta' => [
                'vendor' => 'Marg Compusoft',
                'pricing_model' => 'One-time', 'price' => 'NPR 12,600',
                'maturity' => '20+ yrs',
                'has_mobile_app' => true, 'has_api' => false, 'free_trial' => true,
                'deployment' => 'On-premise',
                'rating' => 4.1, 'review_count' => 842,
                'included_features' => 'Pharma, FMCG, Distribution, POS, GST',
            ],
            'industries' => ['Trading', 'Hospital'],
            'categories' => ['ERP', 'POS', 'Inventory'],
            'country' => 'India',
        ],

        /* ---------- School / College ---------- */
        [
            'title' => 'Veda School',
            'excerpt' => 'School ERP with attendance, exam, library and parent app. Used in 600+ schools across Nepal.',
            'meta' => [
                'vendor' => 'Veda Apps',
                'pricing_model' => 'Subscription', 'price' => 'NPR 8,500 / yr / school',
                'maturity' => '10 yrs',
                'has_mobile_app' => true, 'has_api' => true, 'free_trial' => true,
                'deployment' => 'Cloud + On-premise',
                'rating' => 4.7, 'review_count' => 412,
                'included_features' => 'Attendance, Exam, Fees, Library, Parent app, SMS',
            ],
            'industries' => ['School & College'],
            'categories' => ['School ERP'],
            'country' => 'Nepal',
        ],
        [
            'title' => 'Paathsahala',
            'excerpt' => 'School management with strong online classes and digital exam tooling.',
            'meta' => [
                'vendor' => 'Paathsahala Inc.',
                'pricing_model' => 'Subscription', 'price' => 'NPR 12,000 / yr / school',
                'maturity' => '8 yrs',
                'has_mobile_app' => true, 'has_api' => false, 'free_trial' => true,
                'deployment' => 'Cloud',
                'rating' => 4.4, 'review_count' => 218,
                'included_features' => 'Online classes, Digital exam, Result publishing, Parent app',
            ],
            'industries' => ['School & College'],
            'categories' => ['School ERP', 'LMS'],
            'country' => 'Nepal',
        ],
        [
            'title' => 'eShikshya',
            'excerpt' => 'Lightweight modular school management — strong on transcripts, fees and ID-card design.',
            'meta' => [
                'vendor' => 'Young Innovations',
                'pricing_model' => 'Subscription', 'price' => 'NPR 4,800 / yr / school',
                'maturity' => '12 yrs',
                'has_mobile_app' => false, 'has_api' => false, 'free_trial' => true,
                'deployment' => 'Cloud',
                'rating' => 4.2, 'review_count' => 142,
                'included_features' => 'Transcripts, Fee receipt, ID cards, Lightweight',
            ],
            'industries' => ['School & College'],
            'categories' => ['School ERP'],
            'country' => 'Nepal',
        ],
        [
            'title' => 'Vidyapith',
            'excerpt' => 'Cloud-first school ERP focused on a parent-first experience with auto SMS notifications.',
            'meta' => [
                'vendor' => 'Vidyapith Solutions',
                'pricing_model' => 'Subscription', 'price' => 'NPR 10,000 / yr / school',
                'maturity' => '7 yrs',
                'has_mobile_app' => true, 'has_api' => true, 'free_trial' => true,
                'deployment' => 'Cloud',
                'rating' => 4.4, 'review_count' => 98,
                'included_features' => 'Parent-first, Auto SMS, Fees, Mobile app',
            ],
            'industries' => ['School & College'],
            'categories' => ['School ERP'],
            'country' => 'Nepal',
        ],
        [
            'title' => 'Digital Nepal School',
            'excerpt' => 'Modular school suite with strong library and transport modules. Pricing scales by student count.',
            'meta' => [
                'vendor' => 'Digital Nepal Pvt. Ltd.',
                'pricing_model' => 'Subscription', 'price' => 'NPR 24 / student / yr',
                'maturity' => '11 yrs',
                'has_mobile_app' => true, 'has_api' => false, 'free_trial' => true,
                'deployment' => 'Cloud',
                'rating' => 4.1, 'review_count' => 124,
                'included_features' => 'Library, Transport, Hostel, Fees',
            ],
            'industries' => ['School & College'],
            'categories' => ['School ERP'],
            'country' => 'Nepal',
        ],
        [
            'title' => 'Nimble Infosys School ERP',
            'excerpt' => 'Veteran Nepali vendor with strong on-premise option for schools that prefer offline-first.',
            'meta' => [
                'vendor' => 'Nimble Infosys',
                'pricing_model' => 'One-time', 'price' => 'NPR 35,000',
                'maturity' => '14 yrs',
                'has_mobile_app' => false, 'has_api' => false, 'free_trial' => true,
                'deployment' => 'On-premise',
                'rating' => 4.0, 'review_count' => 88,
                'included_features' => 'On-premise first, Offline mode, Library, Hostel',
            ],
            'industries' => ['School & College'],
            'categories' => ['School ERP'],
            'country' => 'Nepal',
        ],
        [
            'title' => 'EAcademy Nepal',
            'excerpt' => 'End-to-end academic platform with strong exam, marksheet and result publishing.',
            'meta' => [
                'vendor' => 'EAcademy',
                'pricing_model' => 'Custom', 'price' => 'Custom · per college',
                'maturity' => '9 yrs',
                'has_mobile_app' => true, 'has_api' => true, 'free_trial' => true,
                'deployment' => 'Cloud + On-premise',
                'rating' => 4.3, 'review_count' => 186,
                'included_features' => 'College ERP, Exam & result, Marksheet, Hostel',
            ],
            'industries' => ['School & College'],
            'categories' => ['School ERP'],
            'country' => 'Nepal',
        ],
        [
            'title' => 'Vedmarg ERP',
            'excerpt' => 'School management system from India focused on multi-branch chains and franchise models.',
            'meta' => [
                'vendor' => 'Vedmarg Technosoft',
                'pricing_model' => 'Subscription', 'price' => 'INR 18,000 / yr',
                'maturity' => '10 yrs',
                'has_mobile_app' => true, 'has_api' => true, 'free_trial' => true,
                'deployment' => 'Cloud',
                'rating' => 4.0, 'review_count' => 64,
                'included_features' => 'Multi-branch, Franchise, Online admission, Library',
            ],
            'industries' => ['School & College'],
            'categories' => ['School ERP'],
            'country' => 'India',
        ],
        [
            'title' => 'MultiTechsys ERP',
            'excerpt' => 'Versatile ERP that ships with school, college and small institute templates.',
            'meta' => [
                'vendor' => 'MultiTechsys',
                'pricing_model' => 'One-time', 'price' => 'NPR 28,000',
                'maturity' => '6 yrs',
                'has_mobile_app' => false, 'has_api' => false, 'free_trial' => true,
                'deployment' => 'On-premise',
                'rating' => 3.9, 'review_count' => 42,
                'included_features' => 'School, College, Institute, Reports',
            ],
            'industries' => ['School & College'],
            'categories' => ['School ERP'],
            'country' => 'Nepal',
        ],
        [
            'title' => 'Moodle',
            'excerpt' => 'World\'s most-deployed LMS. Free and open-source. Strong on assignments, quizzes, SCORM.',
            'meta' => [
                'vendor' => 'Moodle HQ',
                'pricing_model' => 'Free', 'price' => 'Free (open-source)',
                'maturity' => 'Mature · 23+ yrs',
                'has_mobile_app' => true, 'has_api' => true, 'free_trial' => true,
                'deployment' => 'Self-host + Cloud',
                'rating' => 4.3, 'review_count' => 3940,
                'logo_url' => $base . 'moodle.svg',
                'included_features' => 'LMS, Assignments, Quizzes, SCORM, Plugins',
            ],
            'industries' => ['School & College', 'NGO'],
            'categories' => ['LMS', 'School ERP'],
            'country' => 'Australia',
        ],

        /* ---------- Hospital ---------- */
        [
            'title' => 'Bidhee Hospital ERP',
            'excerpt' => 'Hospital management system covering OPD/IPD, pharmacy, lab, billing and insurance claims.',
            'meta' => [
                'vendor' => 'Bidhee Pvt. Ltd.',
                'pricing_model' => 'Custom', 'price' => 'Custom quote',
                'maturity' => '12 yrs',
                'has_mobile_app' => true, 'has_api' => true, 'free_trial' => false,
                'deployment' => 'On-premise + Cloud',
                'rating' => 4.5, 'review_count' => 96,
                'included_features' => 'OPD, IPD, Pharmacy, Lab, Billing, Insurance',
            ],
            'industries' => ['Hospital'],
            'categories' => ['Hospital HMS'],
            'country' => 'Nepal',
        ],

        /* ---------- Trading / Distribution ---------- */
        [
            'title' => 'Nexus',
            'excerpt' => 'Distribution-focused ERP with strong route accounting, salesman beat plans and trade schemes.',
            'meta' => [
                'vendor' => 'Nexus Software',
                'pricing_model' => 'One-time', 'price' => 'NPR 32,000',
                'maturity' => '11 yrs',
                'has_mobile_app' => true, 'has_api' => false, 'free_trial' => true,
                'deployment' => 'On-premise + Cloud',
                'rating' => 4.2, 'review_count' => 142,
                'included_features' => 'Route accounting, Beat plan, Trade schemes, Inventory',
            ],
            'industries' => ['Trading'],
            'categories' => ['ERP', 'Inventory'],
            'country' => 'Nepal',
        ],
    ];

    foreach ($products as $p) {
        // Skip if a software with this title already exists
        $exists = get_page_by_title($p['title'], OBJECT, 'software');
        if ($exists) continue;

        $post_id = wp_insert_post([
            'post_title'   => $p['title'],
            'post_excerpt' => $p['excerpt'] ?? '',
            'post_content' => $p['content'] ?? ($p['excerpt'] ?? ''),
            'post_status'  => 'publish',
            'post_type'    => 'software',
        ]);
        if (is_wp_error($post_id) || !$post_id) continue;

        // Custom fields
        if (!empty($p['meta'])) {
            foreach ($p['meta'] as $k => $v) {
                update_post_meta($post_id, $k, $v);
            }
        }

        // Taxonomies
        if (!empty($p['industries'])) wp_set_object_terms($post_id, $p['industries'], 'industry');
        if (!empty($p['categories'])) wp_set_object_terms($post_id, $p['categories'], 'sw_category');
        if (!empty($p['country']))    wp_set_object_terms($post_id, [$p['country']], 'country');
    }

    update_option('inp_products_seeded', '1');
}, 20);

/* =================================================================
   4. Admin meta box — quick edit of the custom fields
   ================================================================= */
add_action('add_meta_boxes', function () {
    add_meta_box(
        'inp_software_meta',
        'Software details',
        'inp_render_meta_box',
        'software',
        'normal',
        'high'
    );
});

function inp_render_meta_box($post) {
    $fields = [
        'vendor'            => 'Vendor',
        'website_url'       => 'Website URL',
        'pricing_model'     => 'Pricing model (One-time / Subscription / Per user / Custom / Free)',
        'price'             => 'Price',
        'maturity'          => 'Maturity',
        'deployment'        => 'Deployment (On-premise / Cloud / Hybrid)',
        'company_size'      => 'Best for company size',
        'rating'            => 'Rating (0–5)',
        'review_count'      => 'Review count',
        'logo_url'          => 'Logo URL',
        'included_features' => 'Included features (comma separated)',
        'excluded_features' => 'Excluded features (comma separated)',
    ];
    $bools = ['has_mobile_app' => 'Has mobile app', 'has_api' => 'Has public API', 'free_trial' => 'Free trial'];

    wp_nonce_field('inp_meta_save', 'inp_meta_nonce');
    echo '<table class="form-table"><tbody>';
    foreach ($fields as $key => $label) {
        $val = esc_attr(get_post_meta($post->ID, $key, true));
        echo '<tr><th><label for="' . $key . '">' . esc_html($label) . '</label></th>';
        echo '<td><input type="text" id="' . $key . '" name="inp_' . $key . '" value="' . $val . '" style="width:100%"></td></tr>';
    }
    foreach ($bools as $key => $label) {
        $checked = get_post_meta($post->ID, $key, true) ? 'checked' : '';
        echo '<tr><th>' . esc_html($label) . '</th>';
        echo '<td><input type="checkbox" id="' . $key . '" name="inp_' . $key . '" value="1" ' . $checked . '></td></tr>';
    }
    echo '</tbody></table>';
}

add_action('save_post_software', function ($post_id) {
    if (!isset($_POST['inp_meta_nonce']) || !wp_verify_nonce($_POST['inp_meta_nonce'], 'inp_meta_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $text_keys = ['vendor','website_url','pricing_model','price','maturity','deployment','company_size','rating','review_count','logo_url','included_features','excluded_features'];
    foreach ($text_keys as $k) {
        if (isset($_POST['inp_' . $k])) update_post_meta($post_id, $k, sanitize_text_field(wp_unslash($_POST['inp_' . $k])));
    }
    foreach (['has_mobile_app','has_api','free_trial'] as $k) {
        update_post_meta($post_id, $k, isset($_POST['inp_' . $k]) ? '1' : '');
    }
});

/* =================================================================
   5. Admin columns — see vendor, country, rating in the list view
   ================================================================= */
add_filter('manage_software_posts_columns', function ($cols) {
    $new = [];
    foreach ($cols as $k => $v) {
        $new[$k] = $v;
        if ($k === 'title') {
            $new['vendor']  = 'Vendor';
            $new['rating']  = 'Rating';
            $new['price']   = 'Price';
        }
    }
    return $new;
});
add_action('manage_software_posts_custom_column', function ($col, $post_id) {
    if ($col === 'vendor') echo esc_html(get_post_meta($post_id, 'vendor', true));
    if ($col === 'rating') {
        $r = get_post_meta($post_id, 'rating', true);
        $n = get_post_meta($post_id, 'review_count', true);
        echo $r ? '★ ' . esc_html($r) . ' <small>(' . esc_html($n) . ')</small>' : '—';
    }
    if ($col === 'price')  echo esc_html(get_post_meta($post_id, 'price', true));
}, 10, 2);

/* =================================================================
   6. (Optional) flush rewrite rules once after first seed
   ================================================================= */
add_action('init', function () {
    if (get_option('inp_rewrite_flushed') !== '1') {
        flush_rewrite_rules(false);
        update_option('inp_rewrite_flushed', '1');
    }
}, 99);
