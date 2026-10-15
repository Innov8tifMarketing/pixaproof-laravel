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
│   └── ImportStatamicContent.php       # pixaproof:import-content (pages + sections, navs, media, Site/Brand/Marketing settings, redirects)
├── Http/
│   ├── Controllers/CspReportController.php   # POST /csp-report → csp log channel
│   └── Middleware/SecurityHeaders.php        # Enforced + report-only CSP (nonce), skipped report-only on /cp
├── Models/User.php                     # Statamic database users
└── Providers/AppServiceProvider.php   # Registers the `heroicons` icon set; shares the Site global with navbar/footer/404

config/
├── marketing-toolkit.php               # Marketing Toolkit (description length 160; share cards on when imagick is loaded)
└── statamic/                           # Statamic config; eloquent-driver.php lists which repositories use the DB

database/
├── content/                            # Shipped copy: home.php, contact.php, privacy.php (page sections), site.php (Site global)
├── seeders/DatabaseSeeder.php          # Runs pixaproof:import-content
└── seo/
    ├── redirects.csv                   # The 22 legacy redirects (toolkit CSV format)
    └── pixaproof-icon.png              # Source of the favicon set (copied into the asset container)

resources/
├── blueprints/
│   ├── collections/pages/page.yaml     # title, the page builder (import: page_builder), slug, SEO tab
│   ├── globals/site_content.yaml       # "Site": navbar button, footer copy and links, 404 copy
│   └── globals/seo.yaml, marketing.yaml # Brand and Marketing settings (created by mt:install)
├── fieldsets/
│   ├── page_builder.yaml               # The `blocks` Replicator: one set per section type
│   └── section_options.yaml            # `anchor`, imported at the end of every set
├── icons/heroicons/                    # Heroicons outline SVGs (no `o-` prefix) for the CP icon picker
├── css/app.css                         # Tailwind v4 + brand colors (primary/neutral/accent)
├── js/app.js                           # Alpine.js (+collapse, +intersect) and Motion helpers
└── views/
    ├── layout.blade.php                # <s:mt:head /> / <s:mt:body />, navbar, footer
    ├── page.blade.php                  # Every page: <x-blocks :blocks="$page->blocks" />
    ├── errors/404.blade.php            # 404 with <s:mt:head status="404" />, copy from the Site global
    └── components/
        ├── blocks.blade.php            # The page builder loop: each enabled set → components/blocks/{set}
        ├── blocks/                     # One view per set: hero, trust-strip, challenge, difference, layers,
        │                               #   how-it-works, demos, industries, stats, comparison, about, faq, cta,
        │                               #   contact-hero, offices, legal-text
        ├── navbar.blade.php            # Links from <s:nav:main>; button from the Site global
        ├── footer.blade.php            # Links from <s:nav:footer>; copy from the Site global
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
| Entries | `home`, `contact`, `privacy` | DB | Collection template `page`; sections in `blocks`; SEO tab per entry |
| Navigations | `main`, `footer` | DB | URL items (anchors) + the home entry |
| Global set | `site_content` (Site) | DB | Navbar button, footer, 404 copy. Not `site`: a global set named `site` would replace the cascade's `site` variable |
| Global set | `seo` (Brand) | DB | Default description and image, favicon, share-card colours |
| Global set | `marketing` (Marketing settings) | DB | GA4 ID, robots lines, Features switches |
| Asset container | `assets` | DB meta, files on `media` disk | `public/media` (production: `shared/data/media`) |
| Redirects | toolkit `mt_redirects` | DB | 22 rows from `database/seo/redirects.csv` |
| Blueprints, fieldsets | `collections.pages.page`, `globals.site_content`, `globals.seo`, `globals.marketing`; `page_builder`, `section_options` | Flat files | Reviewable in git |

`php artisan pixaproof:import-content` creates all of it and updates in place. A page's `blocks` and the Site
global are only filled while empty, and existing assets are left alone, so control panel edits survive either way.
With `--once` (`composer setup`) it does nothing if the `pages` collection exists. Deploys never run it: they
ship code only, so content (entries, globals, navigation trees, assets, redirects) is only changed in the
control panel. Blueprints, fieldsets and the other flat-file structure ship with the release, so control panel
edits to them revert on deploy; structure changes go through git.

## Homepage Sections (Mega Landing)

The homepage is the `home` entry's `blocks`, in this order (admins can reorder, switch off or add sections):

| # | Set | Anchor | Notes |
|---|-----|--------|-------|
| 1 | `hero` | — | Rotating words, two buttons, three phone panels (exactly 3) |
| 2 | `trust_strip` | — | Icon + label items, staggered in |
| 3 | `challenge` | `challenge` | Scroll-scrubbed: title, 3 figures (90 / 67 / 30 %), quote, attack cards. Once per page |
| 4 | `difference` | — | Before/after phones (`prevention-visual`) |
| 5 | `layers` | `solution` | Icon cards + summary card (`data-layer-card` stagger) |
| 6 | `how_it_works` | `how-it-works` | Exactly 3 steps (`solution-flow`) |
| 7 | `demos` | `demos` | Video cards + lightbox (mp4, webm, poster assets in `videos/`) |
| 8 | `industries` | `solutions` | Tabs (desktop) / accordion (mobile); `tab_key` drives Alpine |
| 9 | `stats` | — | Counting figures (`<x-stat>`) |
| 10 | `comparison` | — | `yes` / `no` cells render a tick / cross; one wording on desktop and mobile |
| 11 | `about` | `about` | Timeline, certification cards, link |
| 12 | `faq` | `faq` | Accordion |
| 13 | `cta` | — | Closing heading and buttons |

Contact is `contact_hero` + `offices`; privacy is one `legal_text` (title, last-updated date, markdown).
The old Technology bento grid (`#technology`) was dropped; links point to `#how-it-works`.

## Patterns

### Page builder (after the Penang kit's pattern)
`resources/fieldsets/page_builder.yaml` holds one `blocks` Replicator (`collapse: accordion`), so each section
is a collapsible card in the CP that can be dragged, switched off or duplicated. `components/blocks.blade.php`
loops over the enabled sets and `@include`s `components/blocks/{set}` (underscores → hyphens) with `$block`
(a `Statamic\Fields\Values`) and `$page`; it adds no wrapper element, so the DOM matches the hand-written pages.
A second `challenge` is skipped (the scroll-scrub JS binds the first `[data-scrub-section]` only, with three figures).
Styling is fixed per set; only words, images and icons are editable.

- Reserved handles in sets and grid rows: `id`, `type`, `enabled` (hence `tab_key`).
- Icon fields (`type: icon, set: heroicons`) augment to SVG markup; views use `$row->raw('icon')` and render
  `<x-dynamic-component :component="'heroicon-o-'.$name">` so the classes stay as before.
- Assets (`max_files: 1`) augment to an Asset: `$block->image?->url()` (`/media/...`).
- Grids that the layout or JS needs a fixed count for have `min_rows`/`max_rows` (hero panels, challenge figures, steps).

**Adding a section:** add a set to `page_builder.yaml` (with `display`, `instructions`, an `icon` from
`vendor/statamic/cms/resources/svg/icons`, and `- import: section_options` last), then create
`resources/views/components/blocks/{set-with-hyphens}.blade.php` reading `$block->…`. Render
`id="{{ $block->anchor }}"` (or `<x-section :id="$block->anchor">`). Add seed copy to `database/content/*.php`
only if the section should exist on a new install.

### Homepage Section Components
Most sections are wrapped in `<x-section>`, which owns the `x-data="{ visible: false }"` / `x-intersect.once` reveal and the eyebrow / h2 / description header. Children may read the parent `visible` state (`<x-stat>` does). Repeated markup uses `<x-check-list>`, `<x-icon-card>` and `<x-stat>`. The graphics components (`hero-comparison`, `prevention-visual`, `solution-flow`) take their copy and images as props; the phone-screen illustrations inside them stay fixed.

### Templates and layout
Blade templates with Statamic's names (`page` for every entry, `errors/404`); each `@extends('layout')` (Statamic only auto-applies layouts to
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
*Updated: 2026-10-14 - Page builder: `blocks` Replicator, block views, Site global, heroicons icon set, import fills empty sections*
*Updated: 2026-10-14 - Deploys no longer import content; the leads migrations are gone*
