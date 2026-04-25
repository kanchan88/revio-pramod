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
        'deployment'         => 'string',
        'company_size'       => 'string',
        'rating'             => 'number',
        'review_count'       => 'integer',
        'logo_url'           => 'string',
        'included_features'  => 'string',
        'excluded_features'  => 'string',

        /* Rich fields — used by the single-software page templates */
        'tagline'            => 'string',
        'languages'          => 'string',  // English · नेपाली · हिंदी
        'headquarters'       => 'string',  // Bengaluru, India
        'founded'            => 'string',  // 1986
        'customers'          => 'string',  // 2M+ worldwide
        'partners_in_nepal'  => 'string',  // 14
        'support_hours'      => 'string',
        'verdict'            => 'string',  // Editorial recommendation paragraph
        'best_fit_for'       => 'string',  // 1–2 lines
        'look_elsewhere'     => 'string',  // 1–2 lines
        /* JSON-encoded arrays for repeating sections */
        'awards_json'        => 'string',
        'pros_json'          => 'string',
        'cons_json'          => 'string',
        'feature_modules_json' => 'string', // [{section: "Accounting", cards: [{icon, title, items[]}]}]
        'pricing_tiers_json' => 'string',   // [{name, blurb, price_cur, price_num, per, items[], cta_label, popular}]
        'sample_reviews_json'=> 'string',   // [{name, role, industry, size, time, rating, title, body, pros, cons, tags[]}]
        'faq_json'           => 'string',   // [{q, a}]
        'specs_json'         => 'string',   // [{title, rows: {label: value}}]
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
   Skip while WordPress is being installed / before the schema exists.
   ================================================================= */
add_action('init', function () {
    if (defined('WP_INSTALLING') && WP_INSTALLING)              return;
    if (function_exists('wp_installing') && wp_installing())    return;
    if (!function_exists('is_blog_installed') || !is_blog_installed()) return;

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

        /* ---------- Tally Prime (Editor's pick — full showcase) ---------- */
        [
            'title'      => 'Tally Prime',
            'excerpt'    => 'Desktop-first accounting, inventory, GST/VAT and compliance suite trusted by 2M+ SMBs across South Asia.',
            'content'    => "<p>Tally Prime is a desktop-first accounting, inventory and compliance suite trusted by over <strong>2 million SMBs</strong> across South Asia. It bundles double-entry bookkeeping, GST/VAT returns, multi-godown inventory, payroll, banking, e-invoicing and 400+ pre-built reports — designed to be operated by a single accountant without IT support.</p>\n<p>Widely deployed across <strong>trading companies, manufacturing, hotels and online stores</strong> in Nepal.</p>",
            'meta'       => [
                'vendor'            => 'Tally Solutions Pvt. Ltd.',
                'website_url'       => 'https://tallysolutions.com',
                'pricing_model'     => 'One-time',
                'price'             => 'NPR 22,500',
                'maturity'          => 'Mature · 30+ yrs',
                'has_mobile_app'    => true,
                'has_api'           => true,
                'free_trial'        => true,
                'deployment'        => 'On-premise + Cloud add-on',
                'company_size'      => 'Small & Mid (10–500)',
                'rating'            => 4.6,
                'review_count'      => 1240,
                'logo_url'          => $base . 'tally.png',
                'included_features' => 'Books & Ledgers, VAT Return, Inventory, Payroll, Banking, GST, e-Invoicing',
                'excluded_features' => 'Native CRM, Project costing, Hospital module, E-commerce storefront, Multi-user real-time editing',
                'tagline'           => 'Tally Prime is a desktop-first accounting, inventory and compliance suite trusted by over 2 million SMBs across South Asia. Widely deployed across trading companies, manufacturing, hotels and online stores in Nepal.',
                'languages'         => 'English · नेपाली · हिंदी',
                'headquarters'      => 'Bengaluru, India 🇮🇳',
                'founded'           => '1986',
                'customers'         => '2,000,000+ worldwide',
                'partners_in_nepal' => '14 (Kathmandu, Pokhara, Biratnagar, Butwal)',
                'support_hours'     => 'Sun–Fri, 10:00–18:00 NPT (partner)',
                'verdict'           => "For Nepali SMBs that need solid double-entry accounting, multi-godown inventory and a painless VAT return workflow, Tally Prime remains the most cost-effective pick in the market. Its keyboard-first UI is the fastest way for an experienced accountant to enter 200+ vouchers an hour, and the local partner network for training and AMC is unmatched. We mark it down only on collaboration — it isn't built for multiple simultaneous users without the cloud add-on.",
                'best_fit_for'      => 'Trading companies, distributors, retailers, small manufacturing & chartered accountants managing 5–500 books.',
                'look_elsewhere'    => 'You need a true multi-user web ERP, custom workflows, or HR & project modules natively (consider Odoo or Synergy).',
                'awards_json'       => json_encode([
                    ['title' => "Editor's Pick · Accounting", 'medal' => 'brand'],
                    ['title' => '#1 in Trading sector',         'medal' => '1'],
                    ['title' => '#2 in Small Manufacturing',    'medal' => 'silver'],
                    ['title' => '#3 in Hotels & Hospitality',   'medal' => 'bronze'],
                ]),
                'pros_json'         => json_encode([
                    ['title' => 'Keyboard-driven entry is the fastest in the category', 'body' => 'accountants regularly post 200+ vouchers/hour'],
                    ['title' => 'Built-in VAT/GST return generator',  'body' => 'reconciles against sales & purchase registers'],
                    ['title' => 'Multi-godown inventory',             'body' => 'with batch, expiry, MRP and serial-number tracking'],
                    ['title' => 'One-time license',                   'body' => 'no per-user SaaS bloat, predictable TCO'],
                    ['title' => 'Strong local partner network',       'body' => 'Kathmandu, Pokhara & Biratnagar for training & AMC'],
                ]),
                'cons_json'         => json_encode([
                    ['title' => 'Multi-user requires Cloud add-on',  'body' => '≈ NPR 600/user/month'],
                    ['title' => 'Reports UI feels dated',            'body' => 'compared to modern web ERPs'],
                    ['title' => 'Custom workflows need a TDL dev',   'body' => 'e.g. multi-step approvals'],
                    ['title' => 'Mobile app is read-only',           'body' => 'entry must still happen on Windows'],
                ]),
                'feature_modules_json' => json_encode([
                    ['title' => 'Accounting & finance', 'cards' => [
                        ['title' => 'Books & Ledgers',  'items' => ['Unlimited groups, ledgers and cost centres','Multi-currency with auto-revaluation','Cheque printing & bank reconciliation','Post-dated & recurring vouchers']],
                        ['title' => 'VAT / GST & Compliance', 'items' => ['VAT return for Nepal IRD (monthly & quarterly)','e-Invoice JSON with QR (India GST)','TDS, TCS and excise reports','Audit trail with edit-log lock']],
                    ]],
                    ['title' => 'Sales & receivables', 'cards' => [
                        ['title' => 'Order & Invoicing', 'items' => ['Sales orders → delivery → invoice flow','Customisable invoice templates (TDL)','Price lists by party, region, season','Discount & scheme management']],
                        ['title' => 'Receivables & Reminders', 'items' => ['Outstanding aging by party & group','Automated WhatsApp/email reminders','Credit-limit blocking on new sales','Receipt allocation & on-account tracking']],
                    ]],
                    ['title' => 'Inventory & warehousing', 'cards' => [
                        ['title' => 'Stock control', 'items' => ['Multi-godown with location-wise stock','Batch, expiry, MRP, serial & barcode','Manufacturing journals & BOM','Re-order level & min/max alerts']],
                        ['title' => 'Purchase & payables', 'items' => ['Purchase order → GRN → bill workflow','Vendor scorecard & lead-time tracking','Landed cost allocation','Payable aging & cheque printing']],
                    ]],
                    ['title' => 'Operations', 'cards' => [
                        ['title' => 'Payroll (basic)', 'items' => ['Salary structure with allowances & deductions','Attendance import from biometric','Provident fund, CIT & SST computations','Payslip PDF email']],
                        ['title' => 'Banking', 'items' => ['Auto bank statement import (CSV/Excel)','Reconciliation with one-click matching','Connected banking with NIC ASIA, NIBL, HBL','Cheque printing for 30+ Nepali banks']],
                    ]],
                ]),
                'pricing_tiers_json' => json_encode([
                    ['name' => 'Silver · Single user', 'blurb' => 'Perfect for a sole proprietor or single-accountant office.',
                     'price_cur' => 'रु', 'price_num' => '22,500', 'per' => 'one-time',
                     'items' => ['1 user · 1 PC','All accounting & inventory features','VAT return & compliance','1 year free upgrades',
                                 ['label'=>'Multi-user access','no'=>true],['label'=>'Remote access','no'=>true]],
                     'cta_label' => 'Get Silver'],
                    ['name' => 'Gold · Multi user', 'blurb' => 'For SMBs with 2–10 simultaneous users on a LAN.',
                     'price_cur' => 'रु', 'price_num' => '67,500', 'per' => 'one-time',
                     'items' => ['Unlimited users on a LAN','All Silver features','Connected banking & payroll','Priority partner support','1 year free upgrades',
                                 ['label'=>'Browser/mobile access (add Cloud)','no'=>true]],
                     'cta_label' => 'Get Gold', 'popular' => true],
                    ['name' => 'Cloud add-on', 'blurb' => 'Add browser & mobile access on top of any Silver / Gold license.',
                     'price_cur' => 'रु', 'price_num' => '600', 'per' => '/user / month',
                     'items' => ['Use Tally from any browser','Daily encrypted backups','99.9% uptime SLA','Region-locked Mumbai datacentre','Min 12-month commit'],
                     'cta_label' => 'Talk to sales'],
                ]),
                'sample_reviews_json' => json_encode([
                    ['name'=>'Sushant K.', 'role'=>'Director of Finance', 'industry'=>'Trading', 'size'=>'11–50',
                     'time'=>'3 weeks ago', 'rating'=>5,
                     'title'=>'3 years in — still the fastest way to close month-end VAT.',
                     'body'=>'We migrated from a manual Excel process to Tally Gold in 2023. Our month-end VAT filing went from 5 days to half a day. The keyboard shortcuts take a week to learn but after that, our accountant posts at machine speed. Multi-godown was the dealbreaker for us — we run 4 warehouses.',
                     'pros'=>'Speed of entry, VAT auto-reconciliation, partner support is excellent in Kathmandu.',
                     'cons'=>'Mobile app is read-only. We are paying for the Cloud add-on just to give the boss read access on his phone.',
                     'tags'=>['Multi-godown','VAT','Month-end close','Switched from Excel']],
                    ['name'=>'Anita P.', 'role'=>'Chartered Accountant', 'industry'=>'Independent practice', 'size'=>'1',
                     'time'=>'2 months ago', 'rating'=>5,
                     'title'=>'I manage 32 client books on a single laptop.',
                     'body'=>'As a sole CA, I need software that respects my time. Tally Prime lets me switch between client companies in a key-press, audit-lock posted entries, and export GST/VAT-ready files. The license pays for itself in one billing cycle.',
                     'pros'=>'Multi-company switching, granular user roles, exportable audit trail.',
                     'cons'=>'No native cloud sync between offices — I use Google Drive as a workaround.',
                     'tags'=>['Multi-company','Audit trail','Sole practitioner']],
                    ['name'=>'Rohan B.', 'role'=>'IT Manager', 'industry'=>'Hotel', 'size'=>'51–200',
                     'time'=>'1 month ago', 'rating'=>3,
                     'title'=>'Great accounting, but not really an ERP.',
                     'body'=>'Tally is rock-solid for our books and inventory but we ended up running it alongside a separate PMS for room booking and a separate HR tool. The "ERP" framing is a stretch — for a hotel chain, evaluate Synergy or Odoo if you need everything in one place.',
                     'pros'=>'Bookkeeping is bulletproof. The TDS & payroll exports save a lot of manual work.',
                     'cons'=>'No native PMS, F&B costing or housekeeping. Customisation needs a TDL developer.',
                     'tags'=>['Hospitality','Needed PMS add-on','TDL customisation']],
                ]),
                'faq_json'           => json_encode([
                    ['q'=>'Does Tally Prime support Nepal IRD VAT return formats?',
                     'a'=>'Yes. Tally Prime ships with built-in Nepal VAT configuration including the IRD-prescribed monthly return format, sales register, purchase register and adjustment vouchers. Returns can be exported as Excel for upload to the IRD portal.'],
                    ['q'=>'Can I use Tally Prime on a Mac or from a browser?',
                     'a'=>'Tally Prime is a Windows-native desktop application. To use it on Mac or via a browser, you need the <strong>Tally on Cloud</strong> add-on (≈ रु 600 per user/month), which streams your Tally environment from a Mumbai datacentre.'],
                    ['q'=>'Is the data secure if I run it on-premise?',
                     'a'=>'Yes — your data lives on your own server / PC. Tally encrypts the company files and supports role-based user permissions plus an edit-log audit trail (Gold edition). For business continuity we recommend pairing on-premise with a daily off-site or cloud backup.'],
                    ['q'=>'How does pricing compare to Swastik or Odoo?',
                     'a'=>'Tally Silver (single user) at रु 22,500 sits between Swastik (रु 18,000) and Odoo Enterprise ($31/user/month). Tally\'s TCO over 5 years is usually the lowest because it\'s a one-time license; Odoo wins if you specifically need open-source customisation or multi-user web access from day one.'],
                    ['q'=>'Will my staff need formal training?',
                     'a'=>'Most accountants in Nepal are already familiar with Tally. For new users, plan on 2–5 days of partner-led onboarding. Authorised partners in Kathmandu, Pokhara, Biratnagar and Butwal run regular open batches.'],
                    ['q'=>'Can it integrate with my e-commerce or POS?',
                     'a'=>'Yes — through Tally\'s XML/HTTP API, partner connectors are available for Daraz, Sastodeal, eSewa, Khalti and most POS systems. Custom connectors are written in TDL.'],
                ]),
                'specs_json'         => json_encode([
                    ['title'=>'Deployment & access', 'rows'=>[
                        'Deployment'        => 'On-premise (Windows) · optional Tally on Cloud',
                        'Operating system'  => 'Windows 10 / 11 · Windows Server 2016+',
                        'Mobile app'        => 'iOS & Android (read-only dashboards)',
                        'Browser access'    => 'Via Tally on Cloud add-on only',
                        'Offline mode'      => 'Yes — works fully offline',
                    ]],
                    ['title'=>'Vendor & product', 'rows'=>[
                        'Vendor'                => 'Tally Solutions Pvt. Ltd.',
                        'Headquartered in'      => 'Bengaluru, India 🇮🇳',
                        'Founded'               => '1986',
                        'Maturity'              => '30+ years · Tally Prime since 2020',
                        'Customers worldwide'   => '2,000,000+',
                        'Partners in Nepal'     => '14 (Kathmandu, Pokhara, Biratnagar, Butwal)',
                    ]],
                    ['title'=>'Integrations & API', 'rows'=>[
                        'Public API'      => 'Yes — XML-based HTTP/ODBC API',
                        'SDK'             => 'TDL (Tally Definition Language) for customisation',
                        'Banking'         => 'NIC ASIA, NIBL, HBL, Nabil, Standard Chartered',
                        'E-commerce'      => 'Daraz, Sastodeal (via partner connectors)',
                        'Payment gateways'=> 'eSewa, Khalti, IME Pay (via partner connectors)',
                        'Office suite'    => 'Excel, Word export native',
                    ]],
                    ['title'=>'Compliance & security', 'rows'=>[
                        'Nepal IRD VAT return'  => 'Built-in',
                        'India GST e-invoicing' => 'Built-in (with QR)',
                        'Audit trail'           => 'Edit-log lock available (Gold)',
                        'Backup'                => 'Manual + scheduled · cloud backup with add-on',
                        'User roles'            => 'Granular role-based permissions',
                        'Data residency'        => 'On-premise (you control)',
                    ]],
                    ['title'=>'Training & support', 'rows'=>[
                        'Onboarding'         => 'Local partner-led, 2–5 days typical',
                        'Support hours'      => 'Sun–Fri, 10:00–18:00 NPT (partner)',
                        'Languages supported'=> 'English, Nepali, Hindi',
                        'Knowledge base'     => 'help.tallysolutions.com (English)',
                        'Community'          => 'Active forum & Telegram groups',
                    ]],
                ]),
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
    if (defined('WP_INSTALLING') && WP_INSTALLING) return;
    if (function_exists('wp_installing') && wp_installing()) return;
    if (!function_exists('is_blog_installed') || !is_blog_installed()) return;

    if (get_option('inp_rewrite_flushed') !== '1') {
        flush_rewrite_rules(false);
        update_option('inp_rewrite_flushed', '1');
    }
}, 99);

/* =================================================================
   7b. Auto-create editable pages (Home, About, Contact, Vendors) so
       admins can compose / edit them entirely in the Gutenberg editor.
       Idempotent — only runs if the option flag isn't set.
   ================================================================= */
add_action('init', function () {
    if (defined('WP_INSTALLING') && WP_INSTALLING) return;
    if (function_exists('wp_installing') && wp_installing()) return;
    if (!function_exists('is_blog_installed') || !is_blog_installed()) return;
    if (get_option('inp_pages_seeded') === '1') return;

    /* Build a Home page made entirely of pattern references — admins can
     * insert/remove/reorder patterns from the block editor, or click any
     * pattern to detach + edit its individual blocks. */
    $home_blocks = <<<HTML
<!-- wp:pattern {"slug":"infer-nepal/hero"} /-->
<!-- wp:pattern {"slug":"infer-nepal/industry-tiles"} /-->
<!-- wp:pattern {"slug":"infer-nepal/top-rated"} /-->
<!-- wp:pattern {"slug":"infer-nepal/featured-school"} /-->
<!-- wp:pattern {"slug":"infer-nepal/compare-teaser"} /-->
<!-- wp:pattern {"slug":"infer-nepal/pricing-tiers"} /-->
<!-- wp:pattern {"slug":"infer-nepal/vendor-stats"} /-->
HTML;

    $pages = [
        'home' => [
            'title'   => 'Home',
            'content' => $home_blocks,
        ],
        'about' => [
            'title'   => 'About Infer Nepal',
            'content' => '<!-- wp:heading -->' .
                '<h2>About Infer Nepal</h2>' .
                '<!-- /wp:heading -->' .
                '<!-- wp:paragraph --><p>Infer Nepal is the independent B2B software discovery platform for Nepali businesses. We publish verified user reviews, transparent pricing and editor scores. No pay-to-play rankings.</p><!-- /wp:paragraph -->' .
                '<!-- wp:heading {"level":3} --><h3>How we score</h3><!-- /wp:heading -->' .
                '<!-- wp:paragraph --><p>Reviews are verified via work email + invoice. Editor scores combine 1,200+ user reviews per product with 14 hands-on feature tests.</p><!-- /wp:paragraph -->',
        ],
        'contact' => [
            'title'   => 'Contact',
            'content' => '<!-- wp:heading --><h2>Contact us</h2><!-- /wp:heading -->' .
                '<!-- wp:paragraph --><p>Reach our editorial team at <a href="mailto:hello@infernepal.com">hello@infernepal.com</a>.</p><!-- /wp:paragraph -->' .
                '<!-- wp:paragraph --><p>For vendor listings and partnership enquiries, email <a href="mailto:vendors@infernepal.com">vendors@infernepal.com</a>.</p><!-- /wp:paragraph -->',
        ],
        'vendors' => [
            'title'   => 'List your software',
            'content' => '<!-- wp:pattern {"slug":"infer-nepal/vendor-stats"} /-->' .
                '<!-- wp:heading --><h2>Why list with Infer Nepal?</h2><!-- /wp:heading -->' .
                '<!-- wp:paragraph --><p>Reach Nepali decision-makers actively shopping for ERP, accounting, school, hotel and HR tools. Premium and Top listings deliver verified demo requests directly to your sales team.</p><!-- /wp:paragraph -->',
        ],
    ];

    $home_id = 0;
    foreach ($pages as $slug => $p) {
        $existing = get_page_by_path($slug);
        if ($existing) { if ($slug === 'home') $home_id = $existing->ID; continue; }
        $id = wp_insert_post([
            'post_title'   => $p['title'],
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => $p['content'],
        ]);
        if ($id && !is_wp_error($id) && $slug === 'home') $home_id = $id;
    }

    // Set Home as the static front page
    if ($home_id) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', (int) $home_id);
    }

    // Permalinks: pretty URLs so /software/<slug>/ works
    if (!get_option('permalink_structure')) {
        update_option('permalink_structure', '/%postname%/');
    }

    update_option('inp_pages_seeded', '1');
}, 25);

/* =================================================================
   7. Auto-activate the Infer Nepal theme.
   We use pre_option_* filters so the override applies on the SAME
   request — switch_theme() in after_setup_theme is too late
   (the theme is already loaded by then).
   ================================================================= */
add_filter('pre_option_template', 'inp_force_theme', 10, 1);
add_filter('pre_option_stylesheet', 'inp_force_theme', 10, 1);
function inp_force_theme($value) {
    // Don't interfere during the WordPress installer — the options table
    // doesn't exist yet, and the installer needs the bundled theme.
    if (defined('WP_INSTALLING') && WP_INSTALLING) return $value;
    if (function_exists('wp_installing') && wp_installing()) return $value;

    // Avoid recursion: pre_option_* filters run inside get_option().
    static $depth = 0;
    if ($depth > 0) return $value;
    $depth++;
    $userChose = get_option('inp_theme_user_chose');
    $depth--;
    if ($userChose === '1') return $value;

    $theme_dir = WP_CONTENT_DIR . '/themes/infer-nepal';
    if (is_dir($theme_dir) && file_exists($theme_dir . '/style.css')) {
        return 'infer-nepal';
    }
    return $value;
}

// If the admin manually picks a different theme, stop forcing ours.
add_action('switch_theme', function ($new_name, $new_theme) {
    if ($new_theme && $new_theme->get_stylesheet() !== 'infer-nepal') {
        update_option('inp_theme_user_chose', '1');
    } else {
        delete_option('inp_theme_user_chose');
    }
}, 10, 2);
