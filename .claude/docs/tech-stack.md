# PixaProof Marketing Site - Tech Stack

## Core Framework
| Component | Version | Notes |
|-----------|---------|-------|
| Laravel | 13.x | PHP framework |
| PHP | 8.4+ | Runtime version |
| Statamic | 6.x (Core) | CMS: pages, navigation, globals, assets, control panel at `/cp` |
| statamic/eloquent-driver | 5.x | Stores Statamic content in the SQLite database |
| Marketing Toolkit (`jotham-lec/statamic-marketing-toolkit`) | 0.18 (Pro) | SEO meta, JSON-LD, sitemap, robots.txt, llms.txt, favicons, GA4/GTM, redirects, 404 log, reports |
| Alpine.js | 3.x | From npm, with the `@alpinejs/collapse` and `@alpinejs/intersect` plugins |

## Frontend
| Component | Version | Notes |
|-----------|---------|-------|
| Tailwind CSS | 4.x | CSS-first configuration via `@theme` directive |
| Vite | 7.x | Build tool |
| devices.css | — | Device mockup CSS framework |
| @tailwindcss/forms | — | Form styling plugin |
| @tailwindcss/typography | — | Prose styling plugin |
| blade-ui-kit/blade-heroicons | 2.6 | Heroicon SVG components |

## Database
| Component | Status | Notes |
|-----------|--------|-------|
| SQLite | Active | Development and production; holds Statamic content (Eloquent driver), users, redirects, the 404 log and reports |

## Development Tools
| Tool | Purpose |
|------|---------|
| Laravel Pint | Code formatting |
| Laravel Pail | Log viewer |
| Laravel Sail | Docker development |
| PHPUnit | Testing (v12) |

## Deployment
| Platform | Config |
|----------|--------|
| Deployer | `deploy.php`: SQLite backup, migrations, `pixaproof:import-content --once`, Stache warm, health check |
| Scheduler | `deploy/server/etc/cron.d/pixaproof-laravel` (installed by hand) |

## Key Dependencies
```json
{
  "laravel/framework": "^13.0",
  "laravel/tinker": "^3.0",
  "statamic/cms": "^6.35",
  "statamic/eloquent-driver": "^5.12",
  "jotham-lec/statamic-marketing-toolkit": "^0.18.2",
  "blade-ui-kit/blade-heroicons": "^2.6"
}
```

Guzzle is held at 7.x because Statamic doesn't allow Guzzle 8 yet.

```json
{
  "alpinejs": "^3.17",
  "@alpinejs/collapse": "^3.17",
  "@alpinejs/intersect": "^3.17",
  "motion": "^12.23"
}
```

## NPM Scripts
```bash
composer dev      # Run all dev services concurrently
composer setup    # Initial project setup
composer test     # Run tests
npm run dev       # Vite dev server
npm run build     # Production build
```

---
*Updated: 2026-08-20 - Upgraded to Laravel 13 / PHPUnit 12 / Tinker 3; dropped the laravel-frontend-presets/tall scaffolding preset (no Laravel 13 release, unused at runtime)*
*Updated: 2026-10-06 - Statamic 6 + Eloquent driver + Marketing Toolkit Pro; Livewire replaced by Alpine.js from npm; deployment is Deployer (Coolify notes removed)*
