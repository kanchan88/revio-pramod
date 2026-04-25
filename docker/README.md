# Infer Nepal — Local WordPress stack

A docker-compose stack that boots WordPress + MariaDB + phpMyAdmin, **automatically
seeds the Software product catalogue** from `Product-Domains.xlsx`, and ships the
**Infer Nepal custom theme** with Gutenberg block patterns as a lightweight page
builder.

---

## ▶ How to run (Windows)

### 1. Install Docker Desktop (one-time)

1. Download Docker Desktop for Windows: <https://www.docker.com/products/docker-desktop>
2. Install it (next, next, finish — accept WSL 2 if it asks).
3. **Reboot** when it asks.
4. Open the **Docker Desktop** app from the Start menu and wait for the green
   "Engine running" indicator (taskbar bottom-left). Leave Docker Desktop running.

> If you don't have Docker, the rest of this won't work. Verify with
> `docker --version` in a terminal — you should see something like
> `Docker version 24.x`.

### 2. Start the stack

Open **PowerShell** (or Git Bash) in this folder:

```powershell
cd C:\Users\Kanchan\Desktop\Pramod\docker
docker compose up -d
```

That `-d` runs it in the background. First boot takes ~60 seconds while
WordPress and MariaDB initialize. Watch progress with:

```powershell
docker compose logs -f wordpress
```

(press `Ctrl+C` to stop watching the logs — that doesn't stop the stack.)

### 3. Open the site

| URL                                | What                                |
|------------------------------------|-------------------------------------|
| http://localhost:8080               | WordPress front-end                 |
| http://localhost:8080/wp-admin      | Admin dashboard                     |
| http://localhost:8081               | phpMyAdmin (root / `rootpass`)      |

The first time you open `http://localhost:8080` WordPress will run its 5-second
install wizard — pick a site title (e.g. "Infer Nepal"), an admin username and
password, click **Install WordPress**.

After install, in the admin:

1. The **Infer Nepal** theme is **auto-activated** by the seed plugin —
   refresh the home page and you'll see it. (If you want to switch themes
   manually, go to **Appearance → Themes**.)
2. Visit **Software** in the left admin menu — all 21 products
   (Tally, Veda, Swastik, Odoo, SAP, …) are already seeded. Click any of them
   to edit fields like price, rating, deployment, mobile-app/API flags.
3. (Optional) Go to **Appearance → Menus** and create a menu called
   **Primary**. Add top-level items like "Industries" / "Categories" — items
   with children automatically become **mega-menu triggers** with their
   children rendered as columns inside. Each menu item supports a
   "Description" field (enable via **Screen Options** at the top right).
4. Open **Appearance → Customize** to tweak:
    * **Brand → Brand colors** — primary blue / charcoal / etc.
    * **Brand → Promo strip** — top yellow strip text + CTA URL
    * **Brand → Mega menu — feature cards** — for every top-level menu item
      with children, set the right-side feature card (image, tag, title, URL)
    * **Brand → Footer** — blurb, copyright, tagline
    * **Brand → Platform stats** — "38K+ buyers" numbers in the home vendor panel

### 4. Stop / restart / reset

```powershell
# Stop (preserves data — your changes survive)
docker compose stop

# Restart
docker compose start

# Stop and DELETE all data (full reset — removes WP install + DB)
docker compose down -v
```

### Common issues

* **Port 8080 already in use** — change `"8080:80"` in `docker-compose.yml`
  to a free port (e.g. `"9080:80"`) and re-run `docker compose up -d`.
* **`docker compose` says command not found** — Docker Desktop isn't running.
  Open it from the Start menu.
* **WordPress shows a database connection error** — wait 30 more seconds
  (MariaDB takes a moment on first boot) and refresh.

---

## What's inside

| Service     | Image                          | URL                          | Notes                                  |
|-------------|--------------------------------|------------------------------|----------------------------------------|
| WordPress   | `wordpress:6.5-php8.2-apache`  | http://localhost:8080         | Site (admin at `/wp-admin`)            |
| MariaDB     | `mariadb:10.11`                | `localhost:3307` (mapped)    | DB name `wordpress`, user `wp`/`wppass`|
| phpMyAdmin  | `phpmyadmin:5.2`               | http://localhost:8081         | DB browser, root user pre-filled       |

The `wp-content/mu-plugins/infer-nepal-products.php` is mounted into WordPress as a
**must-use plugin** — meaning it auto-loads on every request, without needing to be
activated through the admin. On first boot it:

1. Registers a `software` custom post type (with REST + Gutenberg support)
2. Registers `industry`, `sw_category`, `country` taxonomies
3. Registers all the meta fields (vendor, pricing, deployment, mobile/API flags, rating, …)
4. Seeds **all the products** from `Product-Domains.xlsx` (Tally, Veda, Swastik, Odoo,
   SAP, NetSuite, Sage, Moodle, Bidhee, Synergy, Paathsahala, eShikshya, …)
5. Adds an admin meta box so you can edit fields without touching code
6. Adds Vendor / Rating / Price columns to the admin list view

The seed runs **once** — it sets the `inp_products_seeded` option flag so re-running
the stack won't duplicate. Delete that option from `wp_options` to re-seed.

Logos from `wp-content/uploads/logos/` are served at
`http://localhost:8080/wp-content/uploads/logos/<file>` and are referenced from each
product's `logo_url` meta field.

## First run

```bash
cd docker
docker compose up -d
```

Wait ~30 seconds for MariaDB to initialize, then visit:

* **http://localhost:8080** — finish the WordPress install wizard (pick any
  site title, admin user/password). Once installed, head to `/wp-admin/edit.php?post_type=software`
  and you'll see the seeded software list.
* **http://localhost:8081** — phpMyAdmin (root / rootpass) to browse the raw tables:
  `wp_posts`, `wp_postmeta`, `wp_terms`, `wp_term_relationships`.

> **Important:** the seed plugin runs on the `init` hook **after** WordPress is
> installed. So if you visit `/wp-admin` and the catalogue is empty, just refresh —
> it inserts on the next request once `wp_posts` exists.

## Useful commands

```bash
# Tail logs
docker compose logs -f wordpress

# Stop everything (keeps data)
docker compose stop

# Stop and wipe data (full reset)
docker compose down -v

# Open a shell in the WordPress container
docker exec -it infernepal_wp bash

# Re-trigger the seed (only do this once you've deleted the flag)
docker exec -it infernepal_wp wp option delete inp_products_seeded --allow-root
# then refresh any page on http://localhost:8080
```

## Database schema (the relevant slice)

Software products live in standard WordPress tables — no custom schema:

* `wp_posts` — one row per software, `post_type='software'`
* `wp_postmeta` — fields like `vendor`, `pricing_model`, `price`, `rating`,
  `has_mobile_app`, `has_api`, `included_features`, `logo_url`, …
* `wp_terms` + `wp_term_taxonomy` + `wp_term_relationships` — links to industries,
  categories and country of origin

A query to dump everything:

```sql
SELECT  p.post_title                                 AS software,
        MAX(CASE WHEN m.meta_key='vendor'        THEN m.meta_value END) AS vendor,
        MAX(CASE WHEN m.meta_key='pricing_model' THEN m.meta_value END) AS pricing_model,
        MAX(CASE WHEN m.meta_key='price'         THEN m.meta_value END) AS price,
        MAX(CASE WHEN m.meta_key='rating'        THEN m.meta_value END) AS rating,
        MAX(CASE WHEN m.meta_key='deployment'    THEN m.meta_value END) AS deployment
FROM    wp_posts p
LEFT JOIN wp_postmeta m ON m.post_id = p.ID
WHERE   p.post_type = 'software'
  AND   p.post_status = 'publish'
GROUP BY p.ID
ORDER BY p.post_title;
```

## REST API

The CPT registers with `show_in_rest = true`, so once you've finished the install:

* `GET http://localhost:8080/wp-json/wp/v2/software` — list software
* `GET http://localhost:8080/wp-json/wp/v2/software/<id>` — single software (incl. meta)
* `GET http://localhost:8080/wp-json/wp/v2/industry` — industries
* `GET http://localhost:8080/wp-json/wp/v2/sw_category` — categories
* `GET http://localhost:8080/wp-json/wp/v2/country` — countries

That's how the static `infernepal.com` site (the HTML in the parent folder) can later
pull live data instead of hard-coded markup.

## The "Infer Nepal" theme — lightweight builder

The theme ships in `wp-content/themes/infer-nepal/` and is **auto-activated** on
first boot. Everything an editor needs lives in either **Appearance → Customize**
or the **Pattern picker** inside any page/post — no third-party page builder
plugin required.

### Editing the navigation

1. **Appearance → Menus** → create a new menu, set its location to **Primary**.
2. Add top-level items. Any top-level item with children becomes a **mega-menu
   trigger**; the children render as the columns inside the panel.
3. Each item supports a **Description** field (enable via **Screen Options**) —
   shown as the small subtitle under each child link.
4. For the **right-side feature card** in each mega panel, go to
   **Customize → Brand → Mega menu — feature cards**. Per top-level item, set:
   tag, title, description, image URL, link URL.

### Block patterns (the "lightweight builder")

In any page or post, click the **`+`** Block inserter → **Patterns** tab →
**Infer Nepal** category. Drop one of these in and edit text inline:

| Pattern                          | What it does                                                 |
|----------------------------------|--------------------------------------------------------------|
| **Hero — Search & trending**     | Big home-style headline + search box + trending pills        |
| **Industry tiles (live)**        | Auto-populated grid of industry taxonomies                   |
| **Top-rated software (live, 6 cards)** | Grid of top-6 software by rating                       |
| **Featured for an industry (live list)** | Vertical list filtered by industry slug                |
| **Pricing tiers (3 plans)**      | Silver / Gold / Cloud add-on pricing block                   |
| **Compare teaser**               | Three-column compare promo                                   |
| **Vendor stats CTA**             | Dark "List your software" panel                              |

For a fully editor-built home page:

1. Go to **Pages → Add new** → name it "Home".
2. Insert any combination of patterns from the list above.
3. **Settings → Reading** → set "Your homepage displays" to **A static page**
   and pick "Home".

The theme's `front-page.php` will render the page content as-is, so admins
have full control over the home composition without touching code.

### Live data via shortcodes

Patterns above use these shortcodes — you can drop them inside any block too:

```
[inp_industry_grid count="10"]
[inp_top_software count="6" orderby="rating"]
[inp_top_software count="6" orderby="reviews"]
[inp_software_list count="4" industry="school-college" featured_first="1"]
[inp_software_list count="6" category="erp"]
[inp_vendor_stats]
```

### REST endpoints

The Software CPT exposes itself at `wp-json/wp/v2/software` (incl. all custom
fields), industries at `/industry`, categories at `/sw_category`, countries at
`/country`. Useful if you later want to power the static `infernepal.com` site
from the same WordPress backend.

## Layout

```
docker/
├── docker-compose.yml
├── README.md
└── wp-content/
    ├── mu-plugins/
    │   └── infer-nepal-products.php   ← CPT + taxonomies + seed + admin UI
    ├── themes/
    │   └── infer-nepal/                ← Custom theme
    │       ├── style.css                  Theme header
    │       ├── theme.json                 Block-editor palette / fonts
    │       ├── functions.php              Theme bootstrap
    │       ├── header.php  / footer.php
    │       ├── front-page.php             Home (renders blocks if a page is set)
    │       ├── single-software.php        Software detail page
    │       ├── archive-software.php       Catalogue listing
    │       ├── taxonomy-*.php             Industry / Category / Country archives
    │       ├── inc/
    │       │   ├── customizer.php         Brand colors + promo + footer
    │       │   ├── nav-walker.php         Mega-menu walker + feature-card editor
    │       │   ├── helpers.php            Render fns (vlogo, software card, …)
    │       │   └── block-patterns.php     Pattern loader + live shortcodes
    │       ├── patterns/                  ← The "lightweight builder" surface
    │       │   ├── hero.php
    │       │   ├── industry-tiles.php
    │       │   ├── top-rated.php
    │       │   ├── featured-school.php
    │       │   ├── compare-teaser.php
    │       │   ├── pricing-tiers.php
    │       │   └── vendor-stats.php
    │       └── assets/
    │           ├── styles.css             Same stylesheet as the static site
    │           ├── app.js                 Mega menu + mobile drawer + theme toggle
    │           └── logo.png
    └── uploads/
        └── logos/                          Tally, Odoo, SAP, Sage, Moodle
```
