# PixaProof Marketing Site

Marketing website for **PixaProof** — an image authenticity verification platform by Innov8tif Solutions Pte. Ltd.

🌐 **Production:** [https://pixaproof.com](https://pixaproof.com)

## What This Is

A public-facing marketing site that presents PixaProof's two product editions:

| Edition | Audience | Entry |
|---------|----------|-------|
| **Community Edition** | Individuals, small teams | App Store / Google Play |
| **Enterprise Solutions** | Organizations | SDK / API integration |

The site is a single-page enterprise landing experience (homepage with anchor navigation to Challenge → Solution → How It Works → Demos → Solutions → Technology → About → FAQ), plus `/contact` (email the sales team) and `/privacy`. Legacy URLs 301-redirect to homepage anchors. See [`.claude/docs/site-structure.md`](.claude/docs/site-structure.md) for the full map.

## Tech Stack

- **PHP 8.4** / **Laravel 13** (streamlined `bootstrap/app.php` structure)
- **Statamic 6** (Core) for content, with the **Eloquent driver** (content in the database) and the control panel at `/cp`
- **Marketing Toolkit** (Pro) for SEO meta, JSON-LD, sitemap, robots.txt, llms.txt, favicons, GA4/GTM and redirects
- **Alpine.js** (with the `collapse` and `intersect` plugins) and **Motion** for interactions
- **Tailwind CSS v4** (CSS-first config via `@theme`)
- **SQLite** for persistence (lightweight, file-based)
- **Vite** for asset bundling
- **devices.css** for device mockups in demo sections
- **Git LFS** for video assets in `public/videos/`

Project-specific documentation lives in `.claude/docs/` — see [`.claude/docs/index.md`](.claude/docs/index.md) for the navigation index.

## Quick Start

```bash
composer setup     # Install dependencies, migrate, import the Statamic content, build assets
composer dev       # Run dev server, queue, logs, and vite concurrently
php please make:user   # Create a control panel login for /cp
```

## Content

Pages, navigation, SEO settings and redirects are edited in the control panel (`/cp`):

- **Collections → Pages:** Home, Contact and Privacy Policy. The homepage and contact copy live in their
  Blade templates (`resources/views/home.blade.php`, `contact.blade.php`); the privacy policy is the
  entry's markdown. Each page has an SEO tab.
- **Navigation:** `Main` (header) and `Footer`.
- **Globals → SEO & brand:** default description and share image, icon, GA4 ID, robots.txt lines.
- **Tools → SEO:** redirects, the 404 log and site reports.

`php artisan pixaproof:import-content` creates all of this from the code (used for new installs and
tests). With `--once` it does nothing if the content already exists, which is how deploys run it.

Tests:

```bash
php artisan test --compact
```

Format:

```bash
vendor/bin/pint --dirty
```

## Deployment

Deployment is driven by **[Deployer](https://deployer.org/)** via [`deploy.php`](deploy.php). The recipe builds on `recipe/laravel.php` and adds project-specific tasks for SQLite backups, safe migrations, Git LFS pulls, supervisor-managed queue workers, PHP-FPM restarts, and post-deploy HTTP health checks with automatic rollback on failure.

### Server

| Detail | Value |
|--------|-------|
| **Host** | `prod` (`47.237.191.213`) |
| **Cloud Provider** | Alibaba Cloud (Singapore region) |
| **Deploy User** | `deployer` |
| **Deploy Path** | `/home/deployer/pixaproof-laravel` |
| **Branch** | `main` |
| **Releases Kept** | 5 (auto-rotated) |

> **SSH access:** Reach out to **Jin Xuan** or **Nathan** to be added to the `deployer` user's authorized keys.

### Common Deployer Commands

Run these from the project root on your local machine:

```bash
# Deploy current `main` to production
dep deploy prod

# SSH into the production server (drops you into the current release)
dep ssh prod

# Roll back to the previous release
dep rollback prod

# Tail the last 50 lines of the Laravel log
dep artisan:log prod

# Show pending Supervisor queue worker status
dep queue:status prod

# Restart queue workers
dep queue:restart prod

# Refresh config/route/view caches
dep artisan:cache:refresh prod

# List database backups
dep db:backups prod

# Restore a database backup (interactive — prompts for which backup to restore)
dep db:restore prod

# Put the app into maintenance mode (prints a bypass secret)
dep artisan:down prod

# Bring the app back up
dep artisan:up prod

# Verify deployment health via HTTP check
dep deploy:verify prod

# List all available tasks
dep list
```

### Deploy Flow

`dep deploy prod` runs roughly:

1. Clone repo (LFS smudge skipped) → `lfs:pull` pulls video assets from GitHub
2. `composer install` → cache config
3. `npm ci` + `npm run build`
4. Ensure SQLite file exists → backup current DB → run pending migrations safely → import the Statamic content if the site has none (`pixaproof:import-content --once`)
5. Maintenance mode ON → swap symlink → restart PHP-FPM → maintenance OFF → restart queue → refresh caches → warm Statamic's Stache
6. HTTP health check (5 retries) → auto-rollback if it fails

### Scheduler

Statamic's scheduled entries and the Marketing Toolkit's reports and Search Console import need Laravel's
scheduler. The cron line is in [`deploy/server/etc/cron.d/pixaproof-laravel`](deploy/server/etc/cron.d/pixaproof-laravel);
install it by hand as root (`/etc/cron.d/pixaproof-laravel`).

### Share images and Imagick

Contact and Privacy share a generated card (the Marketing Toolkit, in the SEO & brand card colours); the
homepage shares the designed `og-image.webp` (its entry's SEO image). Cards need PHP's `imagick`
extension, installed on the server (`php8.4-imagick`). `config/seo.php` turns them on only when the
extension is loaded, so a machine without it falls back to the default share image.

### Git LFS Note

Video assets in `public/videos/` are tracked via Git LFS. After pushing changes that touch LFS-tracked files, always verify objects are uploaded:

```bash
git lfs push origin main --all
```

Otherwise the production deploy will fail to fetch the videos.

## Environment Variables (Production)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pixaproof.com

CACHE_STORE=database   # the Marketing Toolkit needs a serialising cache (not array)
SEO_GTM_ID=            # optional Google Tag Manager container; the GA4 ID is set in SEO & brand

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_FROM_ADDRESS=noreply@pixaproof.com
```

Tracking tags print only when `APP_ENV=production`; every other environment is `noindex`.

The `.env` file lives on the server under `/home/deployer/pixaproof-laravel/shared/.env` and is symlinked into each release.

## License

Proprietary — © Innov8tif Solutions Pte. Ltd.
