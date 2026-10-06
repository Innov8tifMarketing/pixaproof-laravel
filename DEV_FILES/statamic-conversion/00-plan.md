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
| A (1+2) | Rehearsed script above; `pages` blueprint (flat-file YAML), collection, navs, asset container, entries, SEO & brand values and redirects via the import command (with tests); `RefreshDatabase` in HTTP tests; serialising test cache. App routes still answer `/`, `/contact`, `/privacy` and the 22 redirects. Expected snapshot changes: `/sitemap.xml` (lists the 3 entries) and `/llms.txt` start answering 200 | — | 92% |
| B (3+5) | Pages become entries with templates and the layout; toolkit head/body; SEO & brand filled; redirects move to the toolkit; robots, sitemap, llms; CSP recheck | `routes/web.php` view and redirect routes, `layouts/*`, GA/GTM partials, `@fingerprintedAsset`, public favicons, manifest, robots.txt, `FaviconCacheBustingTest`, related `PublicRoutesTest` cases (line counts in the report) | 85% |
| C (4+6) | Drop `leads`; Livewire → Alpine; remove `User` factory leftovers; update `deploy.php`, README, AGENTS.md; `route:list --except-vendor` shows only `/csp-report` and `/up` | `Lead.php`, Livewire, `vendor/livewire`, stale docs | 82% |
| 7 | Wiki in `DEV_FILES/wiki` (moojing structure), `provenance.py`, badges | — | 75% |

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
