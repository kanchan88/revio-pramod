# Infer Nepal — Local WordPress stack

A docker-compose stack that boots WordPress + MariaDB + phpMyAdmin and **automatically
seeds the Software product catalogue** from `Product-Domains.xlsx` into the database
on first run.

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

## Layout

```
docker/
├── docker-compose.yml
├── README.md
└── wp-content/
    ├── mu-plugins/
    │   └── infer-nepal-products.php   ← CPT + taxonomies + seed + admin UI
    └── uploads/
        └── logos/                      ← Tally, Odoo, SAP, Sage, Moodle
```
