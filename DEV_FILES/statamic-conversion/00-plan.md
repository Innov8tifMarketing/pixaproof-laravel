# PixaProof → Statamic 6 + Marketing Toolkit: plan

Branch: `statamic-conversion`. Written 2026-10-06 (Phase 0).

The site should end up looking as if it had been built with Statamic 6 and the Marketing Toolkit
(`jotham-lec/statamic-marketing-toolkit`) from the start. Each decision takes the first option that
works, in this order: **Statamic default → Toolkit → Laravel default → Custom**.

References:

- Statamic in an existing Laravel app: https://statamic.dev/installing/laravel
- Eloquent driver: https://github.com/statamic/eloquent-driver and https://statamic.dev/tips/storing-content-in-a-database
- Database users: https://statamic.dev/tips/storing-users-in-a-database
- Structured collections and routes: https://statamic.dev/collections#routing and https://statamic.dev/structures
- Navigation: https://statamic.dev/navigation and https://statamic.dev/tags/nav
- Blade with Statamic (`<s:…>` tags): https://statamic.dev/blade
- Asset containers: https://statamic.dev/assets
- Toolkit: `vendor/jotham-lec/statamic-marketing-toolkit/docs/` (`getting-started.md`, `developers.md`, `editors.md`)

## Phase 0 results

| Item | Result |
|---|---|
| `composer update` / `npm update` | Commit `bba3ed4`. 35 Composer packages moved (Laravel 13.35.0, Livewire 3.8.10, **Guzzle 8.2.0**); 7 npm packages |
| Gates after the update | 66 tests pass, Pint passes, `sheath:lint` passes, `npm run build` passes, PHPStan level 5 has no errors **with `--memory-limit=1G`** (the 128M default crashes a worker; environmental) |
| Safety net | Commit `8d02b30`: `tests/Feature/Guard/UrlSnapshotTest.php` + `tests/__snapshots__/urls.json` (30 fixtures + static `robots.txt`) |
| Baselines | `/tmp/baseline-laravel` (laravel/laravel, framework 13.35.0); `/tmp/baseline-statamic` (statamic/statamic, cms 6.35.0, framework 13.35.0) |
| Dry run | `/tmp/pixaproof-dryrun` (detached worktree at `8d02b30`, own copy of the SQLite file). Removed after this report |

### Dry-run findings (they change the Phase A script)

1. **Guzzle 8 blocks Statamic.** `statamic/cms` ≤ 6.35 needs `guzzlehttp/guzzle ^6.3 || ^7.0`; the update
   took Guzzle to 8.2.0 (allowed by Laravel 13 and Boost). `composer require statamic/cms -W` downgrades
   Guzzle to 7.15.5 (plus promises 2.5.3 and psr7 2.13.1) and removes `symfony/polyfill-php82`. The app
   doesn't use Guzzle directly. This matches the blank Statamic baseline, which also locks Guzzle 7.15.5.
2. **There is no `please` until `php artisan statamic:install`.** That command creates `please`,
   `config/statamic/*`, `content/`, `resources/users/` and `storage/statamic/`, and publishes two
   migrations (`add_two_factor_columns`, `statamic_webauthn_table`). It sets `users.repository` to
   `eloquent` already.
3. **`install:eloquent-driver` fails silently the first time.** Its internal `composer require
   statamic/eloquent-driver` didn't install the package, yet it reported success, published a
   **0-byte** `config/statamic/eloquent-driver.php`, then failed on
   `statamic:eloquent:import-addon-settings`. Later runs said "Configured" but edited the empty file, so
   every repository stayed on `file`. Fix: `composer require statamic/eloquent-driver` (5.12.2) first,
   then run the installer.
4. **`auth:migration` duplicates `statamic:install`.** It writes `statamic_auth_tables` (which also adds
   the two-factor columns) and a second `statamic_webauthn_table`. Migrating both sets fails part-way on
   SQLite (`duplicate column name`), leaving `users` half altered. Fix: delete the
   `add_two_factor_columns_migration` from `statamic:install` and the second webauthn migration before the
   first `migrate`. With that, all 16 migrations run cleanly on a copy of the real database.
5. **Statamic 6 has no `make:collection`, `make:navigation` or `make:asset-container`.** (`please list make`
   shows only action, addon, dictionary, fieldtype, filter, modifier, scope, tag, user and widget.)
   Collections, navigations and the asset container come from the import command (PHP API) instead.
6. **`seo:install` needs an asset container first** ("Create an asset container first, or pass
   --container"). With the `assets` container on the `media` disk it creates `globals.seo` and the `seo`
   set, filling only `robots_disallow: [/cp/]`. It stores no separator. **Resolved:** in toolkit 0.18.2
   `Settings::titleSiteName()` defaults to off and only turns on when a separator is saved, so titles are
   the title alone by default. The import command still sets `title_site_name: false` explicitly so a
   separator typed in the CP later doesn't change every title.
7. **Toolkit CP assets weren't published.** `/cp/auth/login` returned 500 (`Vite manifest not found at
   public/vendor/statamic-marketing-toolkit/build/manifest.json`) until
   `php artisan vendor:publish --tag=marketing-toolkit --force`. Phase A adds that publish to the
   `post-autoload-dump`/`post-update-cmd` scripts and to `deploy.php`.
8. **Tests need a migrated database.** With Statamic installed, every unknown URL returned **500** in
   tests (`no such table: entries`, from Statamic's catch-all route on the un-migrated `:memory:` DB). The
   snapshot caught it. Adding `RefreshDatabase` to `UrlSnapshotTest` restored the 404s and matched every
   page and all 22 redirects. Phase A adds `RefreshDatabase` to every HTTP test.
9. **Phase A is not snapshot-neutral.** With the toolkit installed, `/sitemap.xml` (empty `urlset`) and
   `/llms.txt` (`# Pixaproof`) answer 200 straight away. Both are planned additions that arrive one phase
   early. `/robots.txt` stays with the static file.
10. **Report-only CSP applies to `/cp`.** That was expected (see Risks). The test suite went from about
    4s to about 13s.
11. **Pint flags Statamic's published files** (`config/statamic/{webauthn,static_caching,eloquent-driver,forms}.php`
    and the two auth migrations). Phase A runs `vendor/bin/pint` on them once, in the same commit.

PHPStan (level 5), `sheath:lint` and `npm run build` all pass in the dry run. Live HTTP checks:
`/`, `/contact` and `/privacy` return 200, `/technology` returns 301, `/nope` returns 404, `/cp` redirects
(302) to the login and `/cp/auth/login` returns 200 once the assets are published.

### Rehearsed Phase A script

```bash
composer require statamic/cms -W --no-interaction             # Guzzle 8 → 7
php artisan statamic:install --no-interaction
composer require statamic/eloquent-driver --no-interaction
php please install:eloquent-driver --no-interaction \
  --repositories=addon_settings,asset_containers,assets,collections,collection_trees,entries,globals,global_variables,navs,nav_trees
test -s config/statamic/eloquent-driver.php                    # guard against the 0-byte publish
php please auth:migration
rm database/migrations/*_add_two_factor_columns_migration.php  # duplicated by statamic_auth_tables
rm "$(ls database/migrations/*_statamic_webauthn_table.php | tail -1)"   # keep one webauthn migration
composer require jotham-lec/statamic-marketing-toolkit --no-interaction
php artisan vendor:publish --tag=marketing-toolkit --force
php artisan migrate --no-interaction
# config/statamic/editions.php: 'addons' => ['jotham-lec/statamic-marketing-toolkit' => 'pro']
# config/filesystems.php: 'media' disk → public_path('media'), url /media
php artisan pixaproof:import-content                           # creates the assets container, collections, navs, entries, redirects
php please seo:install --container=assets
vendor/bin/pint
```

## Phase A results

Done on 2026-10-06. Installed statamic/cms 6.35.0, statamic/eloquent-driver 5.12.2 and
jotham-lec/statamic-marketing-toolkit 0.18.2 (Pro). Guzzle went down to 7.15.5.

- **Install.** Followed the rehearsed script, with one change. `install:eloquent-driver` runs `migrate`
  itself, so it applied `statamic:install`'s two-factor migration before `auth:migration` duplicated
  it. The duplicates (`add_two_factor_columns_migration`, the second `statamic_webauthn_table`) were
  deleted and the local DB restored from the pre-Phase-A copy, so the committed migrations run in one
  pass, as they will in production.
- **Toolkit CP assets (finding 7) fixed the Statamic-default way.** `composer.json` now has Statamic's
  `post-autoload-dump` → `php artisan statamic:install --ansi` hook (from the blank baseline). It
  publishes `public/vendor/statamic` and `public/vendor/statamic-marketing-toolkit`, both git-ignored as
  in the baseline. `deploy.php` needs no extra publish step as long as `composer install` runs scripts.
- **Config:** toolkit Pro in `config/statamic/editions.php`; `['type' => 'seo', 'width' => 100]` widget in
  `config/statamic/cp.php`; `media` disk (`public/media`, URL `/media`, git-ignored; `deploy.php` already
  links it to `shared/data/media`).
- **Content:** `resources/blueprints/collections/pages/page.yaml` (title, markdown `content`, slug,
  template, `seo::seo` tab); `resources/blueprints/globals/seo.yaml` (from `seo:install`);
  `database/seo/redirects.csv` (22 rows, `/#technology` targets already `/#how-it-works`).
- **`php artisan pixaproof:import-content`** creates the `assets` container and copies `og-image.webp`
  and `pixaproof-icon.png` into it. It then creates the `pages` collection, its 3 entries and the tree,
  the `main` and `footer` navigations, runs `seo:install --container=assets`, fills SEO & brand
  (`title_site_name: false`, default description, default image, favicon, `ga4_id`) and imports the
  redirects through `Csv::import()`. It checks the counts at the end. Run locally on a fresh DB; a rerun
  updates in place (same entry IDs, `0 created, 22 updated`). Covered by
  `tests/Feature/ImportStatamicContentTest.php` (6 tests, including idempotency and a rejected redirect
  row).
- **Two Eloquent-driver bugs, worked around in the command:**
  1. `AssetContainer::save()` on the entity skips the repository's Blink reset, so a `null` cached by an
     earlier `findByHandle()` outlives the save. Statamic's search indexer (`EntrySaved`, run in-process
     by the `sync` queue) then hits `null->assets()`. Fix: save through `AssetContainer::save($container)`.
  2. A new nav tree (no model yet) is always stored as `[]`:
     `NavTree::makeModelFromContract()` writes `$source->model ? $source->tree() : []`. Fix: save the new
     tree empty first, as the CP does, then set the items. This only showed in tests, because the local
     DB had been filled by the second (idempotent) run.
  Both are upstream issues worth reporting to statamic/eloquent-driver.
- **Tests:** `RefreshDatabase` is in the base `TestCase` (Statamic's catch-all route needs the tables).
  The array cache serialises in tests (`CACHE_ARRAY_SERIALIZE=true` in `phpunit.xml`, read by
  `config/cache.php`). The suite takes about 24s, up from about 4s.
- **Snapshot:** only `/sitemap.xml` and `/llms.txt` changed (404 → 200), as predicted. Every page, the 404
  and all 22 redirects are unchanged. In tests the sitemap is empty, because the snapshot test doesn't
  import content. **Phase B** runs `pixaproof:import-content` in the snapshot test's `setUp()`, since
  the pages become entries then.
- **Gates:** 73 tests pass; Pint (after formatting Statamic's published files), Sheath, PHPStan level 5
  (`--memory-limit=1G`) and `npm run build` pass. Live: `/`, `/contact`, `/privacy`, `/sitemap.xml`
  (3 URLs), `/llms.txt` and `/media/og-image.webp` return 200; `/cp` → login 200; `/technology` 301
  (still the app route); `/nope` 404.
- **Not done (needs you):** a CP super user (`php please make:user`, interactive, your password).

## Phase B results

Done on 2026-10-06.

- **Templates (Statamic's names, Blade):** `resources/views/layout.blade.php` merges `layouts/base` and
  `layouts/app`, with `<s:seo:head />` in an overridable `seo` section and `<s:seo:body />`.
  `home.blade.php` and `contact.blade.php` were moved with `git mv` (history kept) and now `@extends('layout')`.
  `default.blade.php` renders the privacy entry's markdown (`prose` styling, "Last updated" from
  `lastModified()`). `errors/404.blade.php` uses `<s:seo:head title="Page not found" :canonical="false" status="404" />`.
  Blade templates don't get Statamic's automatic layout (Antlers only), so they extend it as in normal Blade.
- **Navigation:** the navbar (desktop and mobile) and footer link lists render `<s:nav:main>` and
  `<s:nav:footer>`. Sheath's `a11y-list-semantics` is disabled around the footer loop (the tag renders no
  element). Named-route links became paths (`/`, `/contact`, `/privacy`), as the anchor links already were.
- **Routes:** `routes/web.php` keeps only `POST /csp-report`. Statamic's `statamic.site` route serves the
  pages, and the toolkit's `HandleMissing` serves the 22 redirects. `route:list --except-vendor`
  shows only `csp.report`.
- **Toolkit config published** (`config/seo.php`), with two changes:
  - `description.length` 155 → 160, matching the SEO & brand field's limit, so the 157-character home
    description isn't cut.
  - `og.enabled` → false. The Pro generated share cards need Imagick, which production lacks, so pages
    use the SEO & brand default image (served through Glide).
- **`SecurityHeaders`:** no report-only CSP on CP routes (`Statamic::isCpRoute()`); the enforced CSP stays.
  Tested.
- **Tests:** `Tests\Concerns\ImportsSiteContent` (Laravel's `setUp{Trait}` hook) runs the import before
  page tests. `SecurityHeadersTest` exempts `application/ld+json` data blocks from the nonce rule (CSP
  doesn't apply to non-executable scripts). `CspReportRouteTest` checks `statamic.site` instead of `home`.
  The snapshot test turns `noindex_outside_production` off, and normalises Glide `s=` signatures (from
  `APP_KEY`) and the order of llms.txt list lines.
  - **Toolkit issue to fix upstream:** `SiteSeo::llmsTxt()` sorts by `lastModified()` with no tie-break,
    so pages saved in the same second swap places.
  - The suite is 65 tests in about 87s (the import costs about 0.9s per page test).
- **Snapshot diff, all mapped to intended changes:**
  - Titles are the title alone.
  - Robots meta (`max-snippet:-1, …`).
  - JSON-LD (WebSite, Organization, WebPage, BreadcrumbList on inner pages).
  - Share image is now a Glide crop of `og-image.webp`.
  - New `og:locale` and `og:image:alt` / `twitter:image:alt` tags (toolkit additions).
  - The three `/#technology` targets are now `/#how-it-works`.
  - The sitemap lists the 3 pages.
  - robots.txt is served by the toolkit (`Disallow: /cp/`, `Sitemap:`).
  - llms.txt lists the pages.
  - The 404 page has a title, the default description, `noindex, follow` and no canonical.
- **Copy note:** the SEO & brand default description (the old layout's fallback, about 170 characters) is
  over the field's 160-character limit. It now only shows on the 404 page (cut at 160); shorten it in the CP.
- **Deleted:**
  - `routes/web.php` −32 lines (view and redirect routes), `AppServiceProvider` −28
    (`@fingerprintedAsset`), `config/services.php` −4 (GTM).
  - `layouts/app` 17, `pages/privacy` 50, GA/GTM partials 45, `FaviconCacheBustingTest` 81.
  - Navbar −77/+20, footer −25/+16.
  - Public files: `favicon.ico`, `favicon.svg`, `favicon-96x96.png`, `apple-touch-icon.png`,
    `site.webmanifest` (21), `robots.txt` (2), `web-app-manifest-192x192.png`.
  - `web-app-manifest-512x512.png` moved to `database/seo/pixaproof-icon.png` (the import's icon source).
  - `GOOGLE_TAG_MANAGER_ID` → `SEO_GTM_ID` in `.env.tmpl`.
- **Browser check (agent-browser, local):** `/`, `/contact`, `/privacy` and `/nope` render with no console
  errors and no CSP reports from the page loads. Navs come from Statamic, and `/#how-it-works` exists.
  The toolkit serves `favicon.ico`, `apple-touch-icon.png`, `icon-192/512.png`, `site.webmanifest` and
  `robots.txt`. `/technology` → 301 `/#how-it-works`. Locally GA4 is absent and pages are
  `noindex, follow`, as intended outside production.
- **`seo:report`:** 97/100 over 3 pages. Two title-length warnings ("Contact", "Privacy Policy"), which
  follow from the title-only decision.
- **Deploy order (for Phase C):** this branch must not deploy without `migrate` followed by
  `pixaproof:import-content`. Without the entries every page would 404.

## Between B and C (2026-10-06)

- **Imagick on production: not installed.** `apt-get install php8.4-imagick` over `ssh innov8tif` was
  blocked by the session's permission policy (a root write on the live server), and so was the follow-up
  local change that would have turned the share cards on whenever the extension is loaded. Both are left
  to the owner; `config/seo.php` still has `og.enabled => false`. To do it by hand:
  `sudo apt-get install -y php8.4-imagick && sudo systemctl reload php8.4-fpm`, then set
  `og.enabled` to true (or `extension_loaded('imagick')`) and regenerate the snapshot (share images
  become `/og.png` and `/og/{uri}.png`).
- **Default description rewritten** to 156 characters (was about 170 and cut on the 404 page): "PixaProof
  verifies images at the point of capture, stopping fraudulent photos, AI-generated documents and
  tampered evidence before they enter your workflow."
- **Test suite refactor:**
  - `DatabaseSeeder` runs the import, and the base `TestCase` seeds once per run
    (`$seed = true`; Laravel 13 keeps the in-memory DB and wraps each test in a transaction). The
    per-test `ImportsSiteContent` trait is gone. 72s → about 24s.
  - The import command's tests clear the seeded content first.
  - The `csp` log channel is `null` in tests, so fake reports no longer land in `storage/logs`.
  - New tests: GET/HEAD-only redirects; privacy "Last updated" date.
  - Left for approval (rule: don't remove tests without approval): the skeleton
    `tests/Feature/ExampleTest.php` (duplicates the home check) and `tests/Unit/ExampleTest.php`
    (asserts true).

## Phase C results

- **Livewire → Alpine.** Removed `livewire/livewire`, `resources/views/vendor/livewire`, the 419 hook
  and `@livewireStyles`/`@livewireScripts`. Added `alpinejs`, `@alpinejs/intersect` **and
  `@alpinejs/collapse`** (`x-collapse` is used three times; Livewire used to bundle it). `x-cloak` CSS was
  already in `app.css`.
  - Browser check: Alpine 3.17.4, no Livewire, the mobile menu opens, all 12 `x-intersect.once` sections
    reveal.
  - `SecurityHeadersTest`'s nonce test had become assertion-free (no inline scripts left in tests). It
    now turns tracking on and requires the toolkit's GA4/GTM inline scripts to carry the nonce.
- **`leads` dropped** by `2026_10_06_152703_drop_leads_table` (`down()` recreates the final schema;
  tested up/down/up). `app/Models/Lead.php` deleted. `DatabaseSchemaTest` covers it.
- **`pixaproof:import-content --once`** does nothing when the `pages` collection exists. Deploys and
  `composer setup` use it, so the first deploy imports and later deploys keep CP edits. Tested.
- **`deploy.php`:** `content:import` after `migrate:safe`; `statamic:stache:warm` after
  `artisan:cache:refresh`. The CP assets publish through the `post-autoload-dump` → `statamic:install`
  hook. Update scripts can't run on deploy: `composer install` leaves no `composer.lock.bak`, so
  Statamic skips them.
  - **Pre-existing issue, not changed:** Laravel's recipe runs `artisan:config:cache` twice, and the
    repo hangs `npm:install → npm:build → db:ensure-sqlite → db:backup → migrate:safe` off it, so the
    whole chain (now plus `content:import`) runs twice per deploy.
- **Scheduler cron** reference copy: `deploy/server/etc/cron.d/pixaproof-laravel` (Statamic's
  `HandleEntrySchedule`, the toolkit's Search Console import and reports). Install by hand.
- **Docs:** README and AGENTS.md rewritten for Statamic, the toolkit, Alpine and the deploy flow.
  `.claude/docs` `tech-stack`, `architecture`, `site-structure` and `index` were updated with the
  project's auto-documenter skill.
- **Boost:** the `livewire-development` skill was dropped and guidelines regenerated with
  `boost:update`. The upgraded Boost also added its default skills (`infer-conventions`,
  `laravel-best-practices`, `testing-best-practices`) and enabled Laravel Cloud; Cloud was turned off
  (the site deploys with Deployer).
- **Gates:** 71 tests pass (no risky), Pint, PHPStan level 5, Sheath, `npm run build`.
  `route:list --except-vendor` shows only `POST csp-report` (`/up` is the framework's).
- **Stale, not touched:** `nixpacks.toml` (Coolify, no longer the deploy path).

## Phase 7 results

- **Wiki** in `DEV_FILES/wiki`, built the same way as moojing-global.com's: `src/*.html` page bodies,
  `diagrams/*.dot` rendered with Graphviz and inlined as clickable SVG, `[[path]]` source links that fail
  the build when a file is missing, and `{{L}}`/`{{S}}`/`{{A}}`/`{{C}}` provenance badges. 12 pages
  (start here, stock vs custom, request lifecycle, templates, content &amp; SEO, database, control panel,
  security headers &amp; CSP, commands/queue/scheduler, deployment, tests, whiteboard) and 6 diagrams.
  Build: `python3 DEV_FILES/wiki/build.py`.
- **`provenance.py`** classifies every tracked file (305) against a fresh `laravel/laravel` and
  `statamic/statamic` install and the installed packages: stock, edited, published (matched by hash
  inside a package, or by name when Pint reformatted it), generated (named generators: `statamic:install`,
  `seo:install`, `boost:update`) or custom. It writes `provenance.json`, which page 0b renders.
  - **Known limit:** "edited" includes published configs that Pint only reformatted.
- **Found while writing it:** `config/csp.php` refers to a `php artisan csp:summary` command that doesn't
  exist (noted on wiki page 7; not changed).

## Audit findings that shape the plan

- **Storage: production uses SQLite.** `deploy.php` sets
  `sqlite_path = {{deploy_path}}/shared/data/sqlite/database.sqlite` and runs `db:ensure-sqlite`,
  `db:backup` (cp) and `backup:offsite` (`sqlite3 .backup` → B2). `config/database.php` defaults to
  `sqlite`, as do `.env` and `.env.tmpl`. No Postgres or MySQL anywhere. → **Eloquent driver on the
  existing SQLite file.**
- **Compatibility.** statamic/cms 6.35 requires `laravel/framework ^12.40 || ^13.0`; toolkit needs
  `php ^8.3` and `statamic/cms ^6.30`; eloquent-driver 5.x needs `statamic/cms ^6.10`. Livewire,
  blade-heroicons, sheath, boost and larastan don't conflict. **Guzzle 8 does** (see finding 1).
- **The site is tiny.** Three `Route::view` pages (`/`, `/contact`, `/privacy`), 22 `Route::redirect`s
  (all 301) and `POST /csp-report`. Six PHP files in `app/`. No Livewire components (Livewire is loaded
  only for Alpine). **No form**: `43abc6e` removed it and `/contact` is a `mailto:` page. `Lead` and
  `leads` are unused (0 rows locally and in production).
- **SEO and tracking today** (`resources/views/layouts/base.blade.php`): title `X - Pixaproof`;
  description, canonical = current URL, OG and Twitter tags, fingerprinted favicons
  (`@fingerprintedAsset`). GA4 `G-VKS70BYBWN` is hard-coded and prints in all environments; GTM prints
  only when its env var is set. No JSON-LD, robots meta, sitemap or llms.txt. `public/robots.txt` allows
  everything.
- **Custom code that stays:** `SecurityHeaders` (global; CSP report-only with the Vite nonce),
  `CspReportController` + `config/csp.php` + the `csp` log channel, `vite-plugin-sri.js`. The toolkit's
  inline scripts carry the Vite nonce.
- **Known issue:** `/#technology` targets an anchor that doesn't render (section wrapped in `@if(false)`).
  Fixed by the approved retarget to `/#how-it-works`.

## Mapping

| Today | Replacement | Label |
|---|---|---|
| `Route::view` `/`, `/contact`, `/privacy` + `pages/*.blade.php` | Structured `pages` collection (`route: '{parent_uri}/{slug}'`, home root); entries `home`, `contact`, `privacy`, each with a `template` | Statamic default |
| `layouts/base` + `layouts/app` | `resources/views/layout.blade.php` (merged) with `<s:seo:head />` / `<s:seo:body />` | Statamic default + Toolkit |
| `@section('title'/'description')`, canonical, OG, Twitter | Toolkit `seo` fields (`import: seo::seo`) + SEO & brand global | Toolkit |
| `images/og-image.webp` hard-coded | SEO & brand default share image (asset) | Toolkit |
| Favicon links, `@fingerprintedAsset`, `public/favicon*`, `site.webmanifest`, `FaviconCacheBustingTest` | Toolkit favicons from the Icon field (`web-app-manifest-512x512.png`, no Imagick needed). Delete the public files, the directive and its test | Toolkit |
| `partials/google-analytics`, GTM partials, `services.google_tag_manager` | Toolkit Tracking: GA4 ID in the global (or `SEO_GA4_ID`), GTM via `SEO_GTM_ID` | Toolkit |
| `public/robots.txt` | Toolkit robots.txt (delete the file) | Toolkit |
| none | Toolkit sitemap.xml, llms.txt | Toolkit |
| 22 `Route::redirect`s | Toolkit `Redirect` rows from the committed `database/seo/redirects.csv`, loaded with the Pro CSV importer's PHP class `JothamLec\MarketingToolkit\Redirects\Csv::import()` (the CSV import has no CLI; over HTTP it is CP-only) | Toolkit |
| Navbar/footer anchor links | `main` navigation (Home, Solutions, Technology, About, FAQ) and `footer` navigation (Solutions, Technology, About), URL items, rendered with the `nav` tag. "Technology" keeps its label and points at `/#how-it-works`. The "Request Demo" button and the footer's contact/legal links stay in Blade, linking to the entries | Statamic default |
| Homepage copy, industries/videos arrays, components | Stay in Blade | Custom (kept, view code) |
| `privacy.blade.php` prose | `content` markdown field on the entry. "Last updated" today prints `date('F j, Y')` (always today); Phase B prints the entry's `updated_at` instead (intended change) | Statamic default |
| `contact.blade.php` | Template `contact`; offices and email stay in Blade | Statamic default |
| `Lead` model, 2 `leads` migrations | Dropped by a new migration, no export | deleted |
| `User` model, `users` table | Statamic database users (`users.repository = eloquent`, `auth:migration`) | Statamic default |
| `SecurityHeaders`, `CspReportController`, `config/csp.php`, csp log channel | Kept; CSP allowlist rechecked for toolkit output; report-only header skipped for `/cp/*` | Custom (kept) |
| `vite-plugin-sri.js`, `ViteManifestSriTest` | Kept | Custom (kept) |
| Livewire (Alpine only), 419 hook in `app.js`, `vendor/livewire/pagination-links` | `alpinejs` + `@alpinejs/intersect`; delete Livewire | Laravel/npm default |
| `/up`, `bootstrap/app.php` | Unchanged | Laravel default |
| Queue worker (supervisor) | Kept | Laravel default |
| 404 (Laravel default page) | `resources/views/errors/404.blade.php` using the layout with `<s:seo:head status="404" />` | Statamic default |
| `deploy.php` | Add toolkit asset publish, `statamic:stache:warm` and `statamic:install` hooks after `migrate:safe`; keep the SQLite backup before migrations; `schedule:run` cron in deploy notes | Custom (modified) |

## Content model and storage

- **Eloquent driver (SQLite):** entries, collections, collection_trees, navigations, navigation_trees,
  global_sets, global_variables, asset_containers, assets (meta), addon_settings. Users via Statamic
  database users.
- **Flat-file:** blueprints and fieldsets (reviewable in git), taxonomies/terms (none), revisions (Pro
  only; off), sites (config), tokens, forms/form_submissions (no forms).
- **Asset container `assets`** on a `media` disk rooted at `public/media`. Production symlinks that to
  `shared/data/media` (already backed up offsite). The import command copies `og-image.webp` and the icon
  PNG into it.
- **Import command** `app/Console/Commands/ImportStatamicContent` (`php artisan pixaproof:import-content`):
  Statamic PHP API only (`AssetContainer`, `Collection`, `Entry`, `Nav`, `GlobalSet`) plus the toolkit's
  redirect import. Idempotent (find-or-update by handle, slug, source). Asserts 3 entries, 2 navs, 22
  redirects. Run once per environment after `migrate`; not added to `deploy.php` automatically.

## Phases

| Phase | Scope | Deletes | Confidence |
|---|---|---|---|
| 0 | This file, snapshot test, baselines, dry run | — | done |
| A (1+2) | **Done** (see Phase A results). Rehearsed script above; `pages` blueprint (flat-file YAML), collection, navs, asset container, entries, SEO & brand values and redirects via the import command (with tests); `RefreshDatabase` in HTTP tests; serialising test cache. App routes still answer `/`, `/contact`, `/privacy` and the 22 redirects. Expected snapshot changes: `/sitemap.xml` (lists the 3 entries) and `/llms.txt` start answering 200 | — | 92% |
| B (3+5) | **Done** (see Phase B results). Pages become entries with templates and the layout; toolkit head/body; SEO & brand filled; redirects move to the toolkit; robots, sitemap, llms; CSP recheck | `routes/web.php` view and redirect routes, `layouts/*`, GA/GTM partials, `@fingerprintedAsset`, public favicons, manifest, robots.txt, `FaviconCacheBustingTest`, related `PublicRoutesTest` cases (line counts in the report) | 85% |
| C (4+6) | **Done** (see Phase C results). Drop `leads`; Livewire → Alpine; remove `User` factory leftovers; update `deploy.php`, README, AGENTS.md; `route:list --except-vendor` shows only `/csp-report` and `/up` | `Lead.php`, Livewire, `vendor/livewire`, stale docs | 82% |
| 7 | **Done** (see Phase 7 results). Wiki in `DEV_FILES/wiki` (moojing structure), `provenance.py`, badges | — | 75% |

## Intended behaviour changes (the snapshot may change only for these)

- Privacy "Last updated" shows the entry's last edit date, not today's date.
- Titles are the title alone (no ` - Pixaproof`). Home stays "Verify Every Image. Eliminate Fraud."
  (set as the home entry's `seo.title`); "Contact - Pixaproof" becomes "Contact".
- `/#technology` targets (redirects `/technology`, `/how-it-works`, `/product`, nav links) become
  `/#how-it-works`.
- New: JSON-LD (WebSite, Organization, WebPage, breadcrumb), robots meta, sitemap.xml, llms.txt.
  robots.txt gains a `Sitemap:` line and a `/cp` disallow.
- Canonical drops query strings.
- GA4 prints only in production. Everything is noindex outside production (the snapshot test runs with
  `robots.noindex_outside_production` off).
- Redirects fire only for GET/HEAD requests that would otherwise 404 (`HandleMissing`), not for any method.
- Favicons become the toolkit's generated set (`favicon-96x96.png` gone; `icon-192.png`, `icon-512.png` added).
- `/cp` exists.

## Safety net

- Fixtures: `/`, `/contact`, `/privacy`, `/this-does-not-exist`, all 22 redirect sources, `/sitemap.xml`,
  `/robots.txt`, `/llms.txt`, `POST /csp-report`, plus the content of `public/robots.txt` (served by the
  web server, so Laravel records 404 today).
- Recorded: status, `Location` (app URL stripped), whitespace-collapsed `<title>`, description, canonical,
  robots meta, every `og:`/`twitter:` meta and decoded JSON-LD. The app URL becomes `{app_url}`; nonces,
  `?v=` hashes and sitemap `<lastmod>` are normalised.
- Absolute URLs from `config('app.url')`. `UPDATE_SNAPSHOTS=1` regenerates; otherwise equality is asserted.

## Editions (decided: Statamic Core + Toolkit Pro)

- **Statamic Core:** one CP user, no revisions, no roles UI. `config/statamic/editions.php` keeps
  `'pro' => false`.
- **Toolkit Pro** via `'addons' => ['jotham-lec/statamic-marketing-toolkit' => 'pro']` (no licence needed
  locally). Used: CSV redirect import, 404 log and automatic redirects, dashboard widget
  (`['type' => 'seo', 'width' => 100]`), `seo:report` (findings in the Phase B report; `schedule:run`
  cron for scheduled reports and Search Console). Consent Mode v2 off unless asked. Leads unused (no forms).

## Answers and production checks (2026-10-06, read-only `ssh innov8tif`)

- Contact stays `mailto:`; no Statamic form.
- `leads` has 0 rows in production; dropped with no export.
- Livewire replaced by `alpinejs` + `@alpinejs/intersect`.
- Privacy prose moves to a markdown `content` field.
- Titles: title alone, via settings (`title_site_name: false`, home `seo.title`).
- Imagick is absent on production; the Icon field uses the PNG.
- Caddy: `handle_errors` only covers Caddy's own errors, so Statamic's 404 shows; the
  `security_headers` snippet sets only if absent, so the app's CSP wins; `file_server` answers existing
  public files first, so public favicon and robots files must go. Production `.env`: `APP_ENV=production`,
  `DB_CONNECTION=sqlite`, `CACHE_STORE=database` (serialising, OK).
- GA4 and indexing in production only: accepted.
- `/#technology` → `/#how-it-works`: approved.

## Risks

- **Guzzle downgrade (new).** Pinning Guzzle 7 holds Laravel's HTTP client and Boost on 7.x until Statamic
  allows 8. Low impact (no direct use); revisit when Statamic widens the constraint.
- Report-only CSP will log CP (Vue) violations under `/cp`; skip the report-only header for `/cp/*` (small,
  tested change to `SecurityHeaders`).
- Static caching stays off; per-request CSP nonces would break with cached HTML.
- Tests need absolute URLs, a migrated DB (finding 8) and a serialising cache (phpunit uses `array`; set
  `cache.stores.array.serialize` or use `file` in tests).
- PHPStan needs `--memory-limit=1G` on this machine; pass the CLI flag (or wrap it in a Composer script).

## Verification (every phase)

- `php artisan test --compact` (including `UrlSnapshotTest`)
- `vendor/bin/pint --test --cache-file=/tmp/fresh-$RANDOM`
- `php artisan sheath:lint`
- `vendor/bin/phpstan --memory-limit=1G` (level 5)
- `npm run build`
- Phase B+: open `/`, `/contact`, `/privacy` and `/cp` in agent-browser; check head output and console.
- Report: changes, deletions with line counts, which snapshot diffs map to which intended change, next steps.
