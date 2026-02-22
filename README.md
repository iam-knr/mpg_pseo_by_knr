=== PSEO PRO by KNR ===
Contributors: iamknr
Tags: seo, programmatic seo, bulk pages, csv
Requires at least: 5.5
Tested up to: 6.9
Stable tag: 2.0.1
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generate thousands of SEO-optimised pages from CSV, JSON or REST API. Unlimited rows, built-in schema, meta, sitemap, cron & WP-CLI.

== Description ==

**PSEO PRO by KNR** is a WordPress plugin built for marketers, agencies, and SEO professionals who need to generate large volumes of location-based, service-based, or data-driven pages at scale — without touching code.

---

# PSEO PRO by KNR
### Programmatic SEO – Bulk Page Generator for WordPress

![Version](https://img.shields.io/badge/version-2.0.1-blue)
![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-purple)
![License](https://img.shields.io/badge/license-GPL--2.0-green)
![Author](https://img.shields.io/badge/author-Kailas%20(KNR)%20Nath%20R-orange)

> Generate thousands of SEO-optimised pages from CSV, JSON or REST API.
> Unlimited rows · Built-in Schema · Meta Tags · XML Sitemap · Auto-Sync · WP-CLI — **100% Free.**

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Requirements](#requirements)
4. [Installation](#installation)
5. [Quick Start](#quick-start)
6. [Data Sources](#data-sources)
7. [Template Syntax](#template-syntax)
8. [Project Settings Reference](#project-settings-reference)
9. [Schema Markup](#schema-markup)
10. [Auto-Sync & Cron](#auto-sync--cron)
11. [XML Sitemap](#xml-sitemap)
12. [WP-CLI Commands](#wp-cli-commands)
13. [Hooks & Filters](#hooks--filters)
14. [Database Tables](#database-tables)
15. [File Structure](#file-structure)
16. [Troubleshooting](#troubleshooting)
17. [FAQ](#faq)
18. [Changelog](#changelog)
19. [Author](#author)
20. [License](#license)

---

## Overview

**PSEO PRO by KNR** is a WordPress plugin built for marketers, agencies, and SEO professionals who need to generate large volumes of location-based, service-based, or data-driven pages at scale — without touching code.

### What is Programmatic SEO?

Programmatic SEO is the practice of creating hundreds or thousands of SEO-optimised pages from a structured dataset, where each page targets a specific keyword combination (e.g. "Plumbing in Bangalore", "Plumbing in Mumbai", "Electrician in Bangalore").

**Traditional approach:** Create each page manually → 500 rows = 500 hours of work
**With PSEO PRO:** Upload a 500-row CSV → click Generate → done in 30 seconds

### Real-World Use Cases

| Industry | Page Type | Scale |
|---|---|---|
| Home Services | `{service}` in `{city}` | 1,000+ pages |
| Real Estate | Properties in `{city}`, `{locality}` | 10,000+ pages |
| Jobs / Hiring | `{job_title}` jobs in `{city}` | 5,000+ pages |
| eCommerce | `{product}` price in `{city}` | 2,000+ pages |
| Education | `{course}` colleges in `{city}` | 3,000+ pages |
| Travel | Hotels in `{city}`, `{area}` | 8,000+ pages |
| SaaS / Directories | `{tool}` alternatives / reviews | 500+ pages |
| Finance | `{loan_type}` in `{city}` | 1,500+ pages |

---

## Features

### Core Features
- ✅ **Unlimited rows** — no artificial page limits
- ✅ **Unlimited projects** — run multiple campaigns simultaneously
- ✅ **5 data sources** — CSV URL, CSV Upload, JSON, REST API
- ✅ **Smart update engine** — existing pages are updated, not duplicated
- ✅ **Orphan detection** — auto-delete pages removed from the data source
- ✅ **Any post type** — Pages, Posts, or any custom post type

### Template Engine
- ✅ `{{placeholder}}` — column value substitution (HTML-escaped)
- ✅ `{{raw:placeholder}}` — unescaped HTML column output
- ✅ `{Option A|Option B|Option C}` — spintax for content variation
- ✅ `[if:column=value]...[/if]` — conditional blocks
- ✅ Supports `=`, `!=`, `>`, `<`, `>=`, `<=` conditional operators

### SEO Features
- ✅ Custom title tag per page with placeholders
- ✅ Custom meta description per page with placeholders
- ✅ Robots meta control (index/noindex per project)
- ✅ Canonical URL auto-injected
- ✅ 6 Schema types — Article, LocalBusiness, Product, FAQPage, BreadcrumbList, JobPosting
- ✅ JSON-LD schema output in `<head>`
- ✅ Custom XML sitemap at `/pseo-sitemap.xml`
- ✅ Compatible with Yoast SEO & Rank Math

### Automation
- ✅ **Auto-sync** — hourly, daily, weekly via WP Cron
- ✅ **WP-CLI** — generate/delete/list from terminal
- ✅ Scheduled cron runs independently of user action

### Developer Features
- ✅ PSR-4 style class autoloader
- ✅ `pseo_schema` filter hook for custom schema
- ✅ Clean database with 3 custom tables
- ✅ Nonce-protected AJAX endpoints
- ✅ Full `manage_options` capability checks

---

## Requirements

| Requirement | Minimum | Recommended |
|---|---|---|
| WordPress | 5.5+ | 6.0+ |
| PHP | 7.4+ | 8.1+ |
| MySQL | 5.6+ | 8.0+ |
| PHP Extensions | `json`, `mbstring` | — |

---

## Installation

### Method 1 — Upload ZIP (Recommended)
1. Zip the `pseo-bulk-generator/` folder
2. Go to **WP Admin → Plugins → Add New → Upload Plugin**
3. Select zip → **Install Now** → **Activate**
4. Go to **Settings → Permalinks → Save Changes** *(required)*

### Method 2 — Manual FTP
1. Upload `pseo-bulk-generator/` to `/wp-content/plugins/`
2. Go to **Plugins → Installed Plugins** → Activate
3. Go to **Settings → Permalinks → Save Changes**

### Method 3 — WP-CLI
```bash
wp plugin install /path/to/pseo-bulk-generator.zip --activate
wp rewrite flush
```

---

## Quick Start

```
Step 1 → Prepare CSV with column headers
Step 2 → Upload CSV
Step 3 → Create a template page with {{placeholders}}
Step 4 → Prog SEO → + New Project → fill the form
Step 5 → Click ⚡ Generate Pages Now
Step 6 → Visit /your-url-pattern/ to see live pages
Step 7 → Submit /pseo-sitemap.xml to Google Search Console
```

---

## Data Sources

### 1. CSV via URL
```
Source Type : CSV via URL
URL         : https://yourdomain.com/data.csv
              
```

### 2. CSV Upload (Server Path)
```
Source Type : CSV Upload (server path)
Path        : /var/www/html/wp-content/uploads/2026/02/data.csv
```

### 3. JSON URL
```
Source Type : JSON URL
URL         : https://api.example.com/data.json
Data Path   : data.items
```

### 4. REST API (Paginated)
```
Source Type  : REST API
API URL      : https://api.example.com/v1/listings
Data Path    : results
Page Param   : page
Per Page     : 100
Max Pages    : 10
Auth Header  : Bearer your-token
```

---

## Template Syntax

| Syntax | Output |
|---|---|
| `{{column}}` | HTML-escaped value |
| `{{raw:column}}` | Unescaped raw HTML value |
| `{A\|B\|C}` | Random option (spintax) |
| `[if:col=val]text[/if]` | Conditional block |
| `[if:col>0]text[/if]` | Numeric condition |
| `[if:col!=val]text[/if]` | Not-equal condition |

---

## Project Settings Reference

### URL Pattern Examples
```
{{service}}/{{city}}                  → /plumbing/bangalore/
services/{{service}}-in-{{city}}      → /services/plumbing-in-bangalore/
{{state}}/{{city}}/{{service}}        → /karnataka/bangalore/plumbing/
jobs/{{job_title}}/{{city}}           → /jobs/developer/bangalore/
```

### SEO Meta
| Field | Recommended |
|---|---|
| Title Tag | Under 60 chars after substitution |
| Meta Description | 120–160 chars after substitution |
| Robots | `index,follow` for live pages |

---

## Schema Markup

| Schema Type | Required CSV Columns |
|---|---|
| Article | *(none — auto-populated)* |
| LocalBusiness | `city`, `address`, `phone` |
| Product | `product_name`, `price` |
| FAQPage | `faq_q1`, `faq_a1`, `faq_q2`, `faq_a2` |
| BreadcrumbList | *(none — auto-generated)* |
| JobPosting | `job_title`, `company`, `city` |

---

## Auto-Sync & Cron

WP Cron event `pseo_cron_sync` runs hourly and regenerates pages for all non-manual projects automatically.

In `wp-config.php`:
```php
define( 'DISABLE_WP_CRON', true );
```

cPanel crontab (every 15 minutes):
```bash
*/15 * * * * wget -q -O - https://yourdomain.com/wp-cron.php?doing_wp_cron >/dev/null 2>&1
```

---

## XML Sitemap

**URL:** `https://yourdomain.com/pseo-sitemap.xml`

> If sitemap returns 404 → Settings → Permalinks → Save Changes

---

## WP-CLI Commands

```bash
wp pseo list
wp pseo list --format=json
wp pseo generate --id=1
wp pseo generate --id=1 --delete-orphans
wp pseo generate --all
wp pseo generate --all --delete-orphans
wp pseo delete-pages --id=1
```

---

## Hooks & Filters

### `pseo_schema`
```php
add_filter( 'pseo_schema', function( $schema, $type, $row, $post ) {
    if ( $type === 'LocalBusiness' ) {
        $schema['openingHours'] = 'Mo-Su 09:00-21:00';
        $schema['image']        = get_the_post_thumbnail_url( $post->ID, 'large' );
    }
    return $schema;
}, 10, 4 );
```

---

## Database Tables

| Table | Purpose |
|---|---|
| `wp_pseo_projects` | Project configurations |
| `wp_pseo_data_rows` | Fetched data snapshots with row hashes |
| `wp_pseo_pages` | Map of generated post IDs to projects |

---

## File Structure

```
pseo-bulk-generator/
├── pseo-bulk-generator.php
├── README.md
├── includes/
│   ├── class-pseo-database.php
│   ├── class-pseo-datasource.php
│   ├── class-pseo-template.php
│   ├── class-pseo-generator.php
│   ├── class-pseo-seometa.php
│   ├── class-pseo-schema.php
│   ├── class-pseo-sitemap.php
│   ├── class-pseo-ajax.php
│   ├── class-pseo-admin.php
│   └── class-pseo-cli.php
└── admin/
    ├── views/
    │   ├── page-projects.php
    │   ├── page-project-edit.php
    │   └── page-settings.php
    ├── images/
    │   └── icon.png
    ├── css/
    │   └── pseo-admin.css
    └── js/
        └── pseo-admin.js
```

---

## Troubleshooting

| Problem | Fix |
|---|---|
| No data returned | Check CSV is public; use 👁 Preview Data to debug |
| Sitemap 404 | Settings → Permalinks → Save Changes |
| `{{city}}` shows in page | Column name in CSV header must match exactly |
| Pages duplicating | URL pattern must produce unique slugs per row |
| Schema not detected | Check required columns exist in CSV |
| Auto-sync not running | Set up real server cron (see above) |
| Icon not showing | Ensure icon.png is 20×20px in `admin/images/` |

---

## FAQ

**Q: Does it work with Elementor or Divi?**
A: Yes. Set your Elementor/Divi-built page as the Content Template — placeholders inside it will be replaced.

**Q: Will it conflict with Yoast SEO or Rank Math?**
A: No. PSEO PRO's meta tags fire at priority 1. Both can coexist.

**Q: Can I edit generated pages manually?**
A: Yes, but manual edits are overwritten on the next Generate run. Use the CSV and template instead.

**Q: Is there a page limit?**
A: No plugin limit. For 10,000+ pages use WP-CLI to avoid PHP timeouts.

**Q: Can I use custom post types?**
A: Yes. Any public post type appears in the "Generate as Post Type" dropdown.

---

== Changelog ==

= 2.0.1 — 2026-02-22 =
- 🔧 Fixed text domain mismatch (pseo → pseo-pro-knr)
- 🔧 Fixed README missing WordPress.org required headers
- 🔧 Removed deprecated load_plugin_textdomain() call
- 🔧 Fixed unescaped output variables in view files
- 🔧 Added nonce verification to AJAX handlers
- 🔧 Prefixed global variables in view templates
- 🔧 Added phpcs:ignore for direct DB queries

= 1.0.0 — 2026-02-22 =
- 🎉 Initial release
- ✅ 5 data source types
- ✅ Template engine (placeholders, spintax, conditionals)
- ✅ 6 JSON-LD schema types
- ✅ Custom XML sitemap
- ✅ WP-CLI integration
- ✅ Hourly WP Cron auto-sync
- ✅ Full AJAX admin UI with preview modal
- ✅ Nonce-protected AJAX security

---

== Upgrade Notice ==

= 2.0.1 =
Fixes text domain, security hardening, and WordPress Plugin Check compliance. Recommended for all users.

---

## Author

**Kailas (KNR) Nath R**
Digital Marketing Strategist | WordPress Developer | SEO Expert

- 🌐 LinkedIn: [linkedin.com/in/iamknr](https://linkedin.com/in/iamknr)

---

## License

GPL-2.0-or-later — https://www.gnu.org/licenses/gpl-2.0.html

---

*Built with ❤️ by [Kailas (KNR) Nath R](https://linkedin.com/in/iamknr) — PSEO PRO v2.0.1*
