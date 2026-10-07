# PixaProof Marketing Site

## Project Overview
Marketing website for PixaProof - an image authenticity verification platform by Innov8tif Solutions Pte. Ltd.

**Product Model:**
| Edition | Target | Entry |
|---------|--------|-------|
| **Community Edition** | Individuals, small teams | App Store / Google Play |
| **Enterprise Solutions** | Organizations | SDK integration |

## Implementation Plan
See **[IMPLEMENTATION.md](.claude/IMPLEMENTATION.md)** for detailed TODO items, component specs, and implementation guidelines.

## Documentation
All project documentation is in `.claude/docs/`:

| Document | Purpose |
|----------|---------|
| [index.md](.claude/docs/index.md) | Documentation navigation and keyword index |
| [tech-stack.md](.claude/docs/tech-stack.md) | Laravel 13, Statamic 6, Marketing Toolkit, Alpine.js, Tailwind 4, devices.css |
| [architecture.md](.claude/docs/architecture.md) | Directory structure, patterns, routes, schema |
| [stylesheet-guidelines.md](.claude/docs/stylesheet-guidelines.md) | Colors (primary/neutral/accent), typography, components |
| [pixaproof-company.md](.claude/docs/pixaproof-company.md) | Innov8tif, brand identity, certifications |
| [pixaproof-product.md](.claude/docs/pixaproof-product.md) | Enterprise SDK, API, PIEA technology, pricing |
| [site-structure.md](.claude/docs/site-structure.md) | Anchor navigation, routes, CTAs |

### Website Content (pages/)
| Page | Document | Status |
|------|----------|--------|
| Homepage | [pages/homepage.md](.claude/docs/pages/homepage.md) | Active — enterprise mega landing |
| Banking | [pages/solutions-banking.md](.claude/docs/pages/solutions-banking.md) | Archived — in `/#solutions` tabs |
| Insurance | [pages/solutions-insurance.md](.claude/docs/pages/solutions-insurance.md) | Archived — in `/#solutions` tabs |
| Government | [pages/solutions-government.md](.claude/docs/pages/solutions-government.md) | Archived — redirects to `/` |
| E-commerce | [pages/solutions-ecommerce.md](.claude/docs/pages/solutions-ecommerce.md) | Archived — redirects to `/` |
| Healthcare | [pages/solutions-healthcare.md](.claude/docs/pages/solutions-healthcare.md) | Archived — redirects to `/` |
| Technology | [pages/technology.md](.claude/docs/pages/technology.md) | Archived — the `/#technology` section is hidden; links go to `/#how-it-works` |
| Developers | [pages/developers.md](.claude/docs/pages/developers.md) | Archived — not yet implemented |

## Skills

### Project Consultant
**Location**: `.claude/skills/project-consultant/SKILL.md`

Use before implementing features to get relevant context from docs. Reads index, identifies relevant documents, returns patterns and references.

### Auto-Documenter
**Location**: `.claude/skills/auto-documenter/SKILL.md`

Use after making architectural changes or learning new information about the company/product. Updates relevant docs and index.

## Quick Start

```bash
composer setup     # Install dependencies, migrate, import the Statamic content, build assets
composer dev       # Run dev server, queue, logs, vite concurrently
php please make:user   # A control panel login (/cp)
```

## Stack

Laravel 13 + **Statamic 6** (Core) with the **Marketing Toolkit** (Pro), Tailwind 4, Alpine.js, SQLite.
Conversion plan and per-phase results: `DEV_FILES/statamic-conversion/00-plan.md`.

## Key Patterns

### Content (Statamic, Eloquent driver)
- Entries, collections, trees, navigations, globals, assets and addon settings live in the SQLite
  database (`config/statamic/eloquent-driver.php`). Blueprints stay flat-file in `resources/blueprints/`.
- `pages` collection (structured, `{parent_uri}/{slug}`): `home`, `contact`, `privacy`. Navigations
  `main` and `footer`. Asset container `assets` on the `media` disk (`public/media`).
- `php artisan pixaproof:import-content` creates all of it, including the SEO & brand values and the 22
  legacy redirects (`database/seo/redirects.csv`). It updates in place; `--once` does nothing if the
  content exists (the deploy uses that, so control panel edits are kept). `DatabaseSeeder` runs it.

### Templates
Blade, Statamic's names: `layout`, `home`, `contact`, `default` (renders an entry's markdown `content`),
`errors/404`. Templates `@extends('layout')` (Statamic applies layouts automatically only to Antlers).
Navigation renders with `<s:nav:main>` / `<s:nav:footer>`. Homepage copy stays in `home.blade.php`.

### SEO, tracking, favicons, redirects
All from the Marketing Toolkit: `<s:seo:head />` and `<s:seo:body />` in the layout; values in
Globals → SEO & brand and each entry's SEO tab; `config/seo.php`. Don't hand-write meta tags, GA/GTM
snippets, favicons, robots.txt or redirects. Tracking prints in production only; other environments
are noindex.

### Security headers
`App\Http\Middleware\SecurityHeaders`: enforced CSP plus a report-only CSP with a per-request Vite nonce
(skipped on `/cp`). Reports go to `POST /csp-report` → `csp` log channel.

### Alpine.js
Bundled in `resources/js/app.js` with the `collapse` and `intersect` plugins; `window.Motion` holds the
animation helpers used in Alpine expressions.

## Routes

`routes/web.php` has only `POST /csp-report`. Pages are Statamic entries; `/sitemap.xml`, `/robots.txt`,
`/llms.txt`, favicons and the manifest come from the toolkit; `/cp` is the control panel; `/up` is
Laravel's health check.

### Anchor Links (on homepage)
`/#challenge` `/#solution` `/#how-it-works` `/#demos` `/#solutions` `/#about` `/#faq`

### 301 Redirects
The toolkit's redirects (CP: Tools → SEO → Redirects), imported from `database/seo/redirects.csv`. They
answer GET/HEAD requests that would otherwise 404.

## Tests

- `php artisan test --compact`. The base `TestCase` migrates and seeds the content once per run; each
  test runs in a transaction.
- `tests/Feature/Guard/UrlSnapshotTest.php` records every public URL's status, redirect and head
  metadata in `tests/__snapshots__/urls.json`. Regenerate with `UPDATE_SNAPSHOTS=1` only for intended
  changes.
- PHPStan needs `vendor/bin/phpstan --memory-limit=1G`.

## Deployment

**Platform:** Deployer (`deploy.php`). Deploy via `dep deploy prod`. SSH via `dep ssh prod`.
Each deploy backs up SQLite, migrates, runs `pixaproof:import-content --once` and warms the Stache.
The scheduler cron is in `deploy/server/etc/cron.d/pixaproof-laravel` (installed by hand).

### Backups

Nightly off-site backups go to the Backblaze B2 bucket `cothinking-client-backups`, under
`pixaproof/`, via spatie/laravel-backup (`config/backup.php`, scheduled in `routes/console.php`:
`backup:clean` 19:50, `backup:run` 19:55, `backup:monitor` 20:55 UTC). Each
`YYYY-MM-DD-HH-MM-SS.zip` holds a consistent `sqlite3 .dump` of the database
(`db-dumps/*.sql.gz`) plus `shared/` (`.env`, `data/media`, all of `storage/app`), AES-256
encrypted. Left out: `data/sqlite` (the dump replaces it), `data/backups`, `cache/`,
`storage/framework`, `storage/logs` and `storage/statamic`. Kept: every backup for 7 days, then
one a day to 30 days, then one a month for 12 months. The bucket has Object Lock (30 days).
Failures are mailed to `BACKUP_NOTIFY_EMAIL`, but production has `MAIL_MAILER=log`, so that mail
goes nowhere; the Uptime Kuma push (`BACKUP_HEARTBEAT_URL`) is the real alarm.

The B2 key, archive password, bucket name and restore notes are in 1Password item
`dig73jz7gqe6svm4llq7naj4fy` ("B2 CoThinking Client Backups Keys"); the Kuma push URL goes in its
`heartbeat_pixaproof` field. Recipe: `deploy/backup.php`.

- **Set up / rotate keys:** `dep backup:env prod` writes a marked block into `shared/.env` from
  1Password and re-caches config. Don't add a second scheduler line; it already runs from
  `/etc/cron.d/pixaproof-laravel`.
- **Back up now / list:** `dep backup:run prod`, `dep backup:list prod`.
- **Verify:** `dep backup:verify prod` downloads the newest backup, decrypts it and test-restores
  the dump locally (needs `op`, `7z`, `jq`, `sqlite3`). Do this quarterly.
- **Restore:** download the zip (see the 1Password notes), `7z x <file>.zip` with the archive
  password (stock `unzip` can't open AES-256), then
  `gunzip db-dumps/*.sql.gz && sqlite3 database.sqlite < db-dumps/<name>.sql`. Put it at
  `shared/data/sqlite/database.sqlite` with `dep` locked, and copy the files back into `shared/`.

`db:backup` still copies the SQLite file to `shared/data/backups` before each migration
(`migrate:safe`); those copies stay on the box.

**Git LFS:** Video assets in `public/videos/` are tracked via git-lfs. After pushing, always verify LFS objects are uploaded: `git lfs push origin main --all`.

## Environment Variables (Production)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pixaproof.com
CACHE_STORE=database        # the toolkit needs a serialising cache (not array)
SEO_GTM_ID=                 # optional; the GA4 ID is in Globals → SEO & brand

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_FROM_ADDRESS=noreply@pixaproof.com
```

## Source Files
Documentation in `.claude/` is copied (not symlinked) from `doom_emacs_roam_files/pixaproof/`. Original Roam source files:
- `20251217T141900--pixaproof.org`
- `pixaproof-product-documentation`

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.4. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/Pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>
