# PixaProof Marketing Site

Marketing website for **PixaProof** — an image authenticity verification platform by Innov8tif Solutions Pte. Ltd.

🌐 **Production:** [https://pixaproof.com](https://pixaproof.com)

## What This Is

A public-facing marketing site that presents PixaProof's two product editions:

| Edition | Audience | Entry |
|---------|----------|-------|
| **Community Edition** | Individuals, small teams | App Store / Google Play |
| **Enterprise Solutions** | Organizations | SDK / API integration |

The site is a single-page enterprise landing experience (homepage with anchor navigation to Challenge → Solution → How It Works → Demos → Solutions → Technology → About → FAQ), plus `/contact` (Livewire demo request form) and `/privacy`. Legacy routes 301-redirect to homepage anchors. See [`.claude/docs/site-structure.md`](.claude/docs/site-structure.md) for the full map.

## Tech Stack

- **PHP 8.4** / **Laravel 12** (streamlined `bootstrap/app.php` structure)
- **Livewire 3** for interactive components (contact form, etc.)
- **Tailwind CSS v4** (CSS-first config via `@theme`)
- **SQLite** for persistence (lightweight, file-based)
- **Vite** for asset bundling
- **devices.css** for device mockups in demo sections
- **Git LFS** for video assets in `public/videos/`

Project-specific documentation lives in `.claude/docs/` — see [`.claude/docs/index.md`](.claude/docs/index.md) for the navigation index.

## Quick Start

```bash
composer setup     # Install dependencies, run migrations, build assets
composer dev       # Run dev server, queue, logs, and vite concurrently
```

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
4. Ensure SQLite file exists → backup current DB → run pending migrations safely
5. Maintenance mode ON → swap symlink → restart PHP-FPM → maintenance OFF → restart queue → refresh caches
6. HTTP health check (5 retries) → auto-rollback if it fails

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

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_FROM_ADDRESS=noreply@pixaproof.com
```

The `.env` file lives on the server under `/home/deployer/pixaproof-laravel/shared/.env` and is symlinked into each release.

## License

Proprietary — © Innov8tif Solutions Pte. Ltd.
