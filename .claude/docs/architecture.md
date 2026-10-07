# PixaProof Marketing Site - Architecture

## Project Type
B2B marketing website for PixaProof - an enterprise image authenticity verification platform.

## Architecture
Statamic 6 on Laravel 13. Three pages are entries in a structured `pages` collection: a single-page mega
landing (`home`) with anchor navigation, plus `contact` (email the sales team) and `privacy`. Former
multi-page URLs are 301 redirects managed by the Marketing Toolkit. Content is stored in SQLite through
Statamic's Eloquent driver; blueprints stay flat-file. Conversion history:
`DEV_FILES/statamic-conversion/00-plan.md`.

## Directory Structure

```
app/
├── Console/Commands/
│   └── ImportStatamicContent.php       # pixaproof:import-content (pages, navs, assets, Brand + Marketing settings, redirects)
├── Http/
│   ├── Controllers/CspReportController.php   # POST /csp-report → csp log channel
│   └── Middleware/SecurityHeaders.php        # Enforced + report-only CSP (nonce), skipped report-only on /cp
├── Models/User.php                     # Statamic database users
└── Providers/AppServiceProvider.php

config/
├── marketing-toolkit.php               # Marketing Toolkit (description length 160; share cards on when imagick is loaded)
└── statamic/                           # Statamic config; eloquent-driver.php lists which repositories use the DB

database/
├── seeders/DatabaseSeeder.php          # Runs pixaproof:import-content
└── seo/
    ├── redirects.csv                   # The 22 legacy redirects (toolkit CSV format)
    └── pixaproof-icon.png              # Source of the favicon set (copied into the asset container)

resources/
├── blueprints/
│   ├── collections/pages/page.yaml     # title, markdown content, slug, template, SEO tab (marketing-toolkit::seo)
│   └── globals/seo.yaml, marketing.yaml # Brand and Marketing settings (created by mt:install)
├── css/app.css                         # Tailwind v4 + brand colors (primary/neutral/accent)
├── js/app.js                           # Alpine.js (+collapse, +intersect) and Motion helpers
└── views/
    ├── layout.blade.php                # <s:mt:head /> / <s:mt:body />, navbar, footer
    ├── home.blade.php                  # Mega landing (data-driven sections)
    ├── contact.blade.php               # Contact page (mailto)
    ├── default.blade.php               # An entry's title + markdown content (privacy)
    ├── errors/404.blade.php            # 404 with <s:mt:head status="404" />
    └── components/
        ├── navbar.blade.php            # Links from <s:nav:main>
        ├── footer.blade.php            # Links from <s:nav:footer>
        ├── button.blade.php            # Button with variants/sizes
        ├── rotating-text.blade.php     # Animated text rotation
        ├── section.blade.php           # Reveal-on-scroll section shell + centered header
        ├── check-list.blade.php        # <ul> of check-icon bullets (compact variant)
        ├── icon-card.blade.php         # Heroicon tile + title + body (inline/stacked, inverted)
        ├── stat.blade.php              # Animated counter / static figure for the stats strip
        └── graphics/                   # hero-comparison, phone-mockup, prevention-visual, solution-flow
```

## Content Model (Statamic)

| Item | Handle | Storage | Notes |
|------|--------|---------|-------|
| Collection | `pages` | DB | Structured, root = home, route `{parent_uri}/{slug}` |
| Entries | `home`, `contact`, `privacy` | DB | Templates `home`, `contact`, `default`; SEO tab per entry |
| Navigations | `main`, `footer` | DB | URL items (anchors) + the home entry |
| Global set | `seo` (Brand) | DB | Default description and image, favicon, share-card colours |
| Global set | `marketing` (Marketing settings) | DB | GA4 ID, robots lines, Features switches |
| Asset container | `assets` | DB meta, files on `media` disk | `public/media` (production: `shared/data/media`) |
| Redirects | toolkit `mt_redirects` | DB | 22 rows from `database/seo/redirects.csv` |
| Blueprints | `collections.pages.page`, `globals.seo`, `globals.marketing` | Flat files | Reviewable in git |

`php artisan pixaproof:import-content` creates all of it and updates in place; `--once` skips when the
`pages` collection exists (used by deploys and `composer setup`).

## Homepage Sections (Mega Landing)

The homepage (`resources/views/home.blade.php`, the `home` entry's template) contains 9 major sections accessed via anchor navigation:

| # | Section | Anchor ID | Description |
|---|---------|-----------|-------------|
| 1 | Hero | (top) | Innov8tif co-branding, PIXAPROOF wordmark, CTAs |
| 2 | Problem Statement | `#challenge` | Verification Gap + Cost of Compensating Controls |
| 3 | Solution Introduction | `#solution` | PixaProof value prop |
| 4 | How It Works | `#how-it-works` | 3-step: Capture → Analyze → Deliver |
| 5 | Use Cases | `#solutions` | Tabbed: Loan Draw, Insurance, Field Operations & Assets (`$industries` array) |
| 6 | Technology Highlights | `#technology` | Disabled (`@if (false)`) — data accuracy under review; links point to `#how-it-works` |
| 7 | Company Credibility | `#about` | Innov8tif background, certifications, stats |
| 8 | FAQ | `#faq` | Accordion with Alpine.js |
| 9 | Final CTA | (bottom) | "Ready to Eliminate Image Fraud?" + Request Demo |

## Patterns

### Homepage Section Components
Homepage sections 4–13 (except the scroll-scrubbed Challenge and the disabled Technology grid) are wrapped in `<x-section>`, which owns the `x-data="{ visible: false }"` / `x-intersect.once` reveal and the eyebrow / h2 / description header. Children may read the parent `visible` state (`<x-stat>` does). Repeated markup uses `<x-check-list>`, `<x-icon-card>` and `<x-stat>`; repeated copy lives in `@php` arrays (`$industries`, `$milestones`, `$comparisons`, `$faqs`) rendered via `@foreach`, so an industry or FAQ is added or removed in one place. Desktop copy is canonical for industries; the mobile accordion renders the same headline, body and bullets.

## Patterns

### Templates and layout
Blade templates with Statamic's names; each `@extends('layout')` (Statamic only auto-applies layouts to
Antlers templates). The layout's `seo` section holds `<s:mt:head />`; the 404 page overrides it.

### SEO and tracking
Never hand-written: the Marketing Toolkit prints titles, meta, Open Graph, JSON-LD, favicons and GA4/GTM.
Values come from each entry's SEO tab, Marketing → Brand and Marketing → Settings. Tracking prints in production only;
other environments are noindex.

### Anchor Navigation
Navbar links use `/#section-id` anchors instead of separate routes. CSS handles scroll offset:
```css
section[id] {
    scroll-margin-top: 5rem;
}
```

## Routes

| Path | Served by |
|------|-----------|
| `/`, `/contact`, `/privacy` | Statamic entries (`statamic.site` route) |
| Legacy paths (`/technology`, `/solutions/*`, …) | Toolkit redirects (GET/HEAD requests that would 404) |
| `/sitemap.xml`, `/robots.txt`, `/llms.txt`, `/favicon.ico`, `/apple-touch-icon.png`, `/icon-192.png`, `/icon-512.png`, `/site.webmanifest` | Marketing Toolkit |
| `/cp` | Statamic control panel |
| `POST /csp-report` | `routes/web.php` (the only app route) |
| `/up` | Laravel health check |

### Anchor Links (on homepage)
| Link | Target |
|------|--------|
| `/#challenge` | Problem Statement section |
| `/#solution` | Solution Introduction |
| `/#how-it-works` | How It Works (also the target of the "Technology" nav item) |
| `/#solutions` | Use Cases tabs |
| `/#about` | Company Credibility |
| `/#faq` | FAQ |

### 301 Redirects (SEO preservation)
| Old Path | Redirects To |
|----------|-------------|
| `/technology`, `/how-it-works`, `/product` | `/#how-it-works` |
| `/about`, `/company/about` | `/#about` |
| `/company/contact`, `/pricing` | `/contact` |
| `/enterprise` | `/#solutions` |
| `/solutions/*` | `/#solutions` or `/` |
| `/resources/*` | `/` |

## Database
SQLite (`database/database.sqlite` locally, `shared/data/sqlite/database.sqlite` in production): Laravel's
users/cache/jobs tables, Statamic's Eloquent-driver tables (`entries`, `collections`, `trees`,
`navigations`, `global_sets`, `global_set_variables`, `asset_containers`, `assets_meta`, `addon_settings`),
Statamic auth tables, and the toolkit's `mt_redirects`, `mt_404s`, `mt_reports*`, `mt_search_stats`.
The old `leads` table was dropped (the contact form was removed; the table was empty).

---
*Updated: 2026-02-09 - Rewritten to match current single-page mega landing architecture*
*Updated: 2026-09-23 - Removed KYC Onboarding industry; added section, check-list, icon-card and stat components; homepage sections data-driven*
*Updated: 2026-10-06 - Statamic 6 + Marketing Toolkit conversion: entries, templates, navs, toolkit SEO/redirects; Livewire, Actions, Lead and leads removed*
