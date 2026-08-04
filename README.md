# Travel & Tour Management System

A single-vendor (multi-vendor-ready) travel & tour booking platform: a public
website, a customer self-service panel, and an admin dashboard, built on
Laravel 13 + Tailwind CSS v4, running entirely in Docker.

See `requirement_doc/` for the full client requirement spec (`-v2.md`) and
database schema (`-v3.md`).

---

## 1. Prerequisites

- **Docker Desktop** (running, with Compose v2 — bundled with recent Docker
  Desktop releases)
- **Node.js** (v18+) on the host — used only for the Tailwind/Vite asset
  build, not containerized
- PHP, Composer, and MySQL do **not** need to be installed on the host —
  they run inside Docker

---

## 2. First-time setup

All `composer`/`artisan` commands run **inside the `app` container**, since
PHP isn't installed on the host. `npm` commands run on the **host**.

```bash
# 1. Build and start the containers (app, mysql, phpmyadmin, mailpit)
docker compose up -d --build

# 2. Install PHP dependencies (skip if vendor/ is already present)
docker compose exec app composer install

# 3. Copy the environment file and generate an app key (skip if .env already exists)
cp .env.example .env
docker compose exec app php artisan key:generate

# 4. Run migrations and seed demo data (destinations, categories, a sample
#    tour, an admin user, and a customer user)
docker compose exec app php artisan migrate --seed

# 5. Link storage so uploaded images (tour thumbnails, gallery, receipts) are servable
docker compose exec app php artisan storage:link

# 6. Install JS dependencies and build Tailwind CSS (host machine)
npm install
npm run build
```

Then visit **http://localhost:8080**.

For active frontend development, use `npm run dev` instead of `npm run
build` — it starts Vite's dev server with hot reload (leave it running in
its own terminal).

### Seeded accounts

| Role | Email | Password |
|---|---|---|
| Admin | `admin@travel-tour.test` | `password` |
| Customer | `customer@travel-tour.test` | `password` |

---

## 3. Everyday commands

Once set up, day-to-day commands (run from the repo root):

```bash
# Start / stop the environment
docker compose up -d
docker compose down

# Run artisan commands
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker
docker compose exec app php artisan route:list

# Run composer commands
docker compose exec app composer require some/package

# Rebuild assets after pulling changes with new CSS/JS
npm run build            # one-off production build
npm run dev               # dev server with hot reload

# Reset the database back to seeded demo data
docker compose exec app php artisan migrate:fresh --seed
```

If you ever see permission errors writing to `storage/` or
`bootstrap/cache/` from inside the container:

```bash
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

---

## 4. Service URLs & ports

| Service | URL | Notes |
|---|---|---|
| Website | http://localhost:8080 | Public site |
| Admin Dashboard | http://localhost:8080/admin/login | Branded admin login |
| Customer Panel | http://localhost:8080/login → redirects to `/account` | Shared login form |
| phpMyAdmin | http://localhost:8081 | DB user/pass from `.env` (`DB_USERNAME`/`DB_PASSWORD`), or `root` / `DB_ROOT_PASSWORD` |
| Mailpit | http://localhost:8025 | Catches all outgoing mail in dev — nothing is ever really emailed |
| MySQL (external) | `localhost:3307` | For connecting a GUI client (TablePlus, DBeaver, ...) from the host |

Ports are overridable via `.env` (`APP_PORT`, `FORWARD_DB_PORT`,
`FORWARD_PHPMYADMIN_PORT`, `FORWARD_MAILPIT_PORT`,
`FORWARD_MAILPIT_SMTP_PORT`) if `8080`/`3307`/`8081`/`8025` are already in
use on your machine.

---

## 5. Project structure notes

- **Three panels, one app**: Admin Dashboard, public Website, and Customer
  Panel are one Laravel app with separate route files (`routes/admin.php`,
  `routes/web.php`, `routes/customer.php`) and layouts
  (`resources/views/components/{admin,website,customer}-layout.blade.php`),
  sharing the same `users` table, guard, and database.
- **Dynamic theme system**: each panel's colors are stored in the `settings`
  table and edited from **Admin → Settings → Theme**
  (`app/Services/ThemeService.php`, `<x-theme-vars>`). No rebuild needed to
  change branding colors.
- **Reusable UI kit**: `resources/views/components/ui/*` (buttons, cards,
  inputs, tables, ...) used across all three panels.
- **CRUD scaffold generator**: for straightforward "lookup" admin modules
  (name/slug/description/parent/status), run:

  ```bash
  docker compose exec app php artisan module:make GalleryCategory --parent
  ```

  See `stubs/module/` for the templates and `app/Console/Commands/MakeModuleCommand.php`
  for available flags (`--parent`, `--no-slug`, `--no-description`,
  `--no-status`, `--no-sort`). It generates the migration (if the table
  doesn't already exist), model, form requests, controller, and views —
  then prints the one route line to add.
- **Roles & permissions**: [Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission),
  seeded in `database/seeders/RoleSeeder.php`.

---

## 6. Troubleshooting

- **Port already in use**: change the relevant `FORWARD_*_PORT` / `APP_PORT`
  in `.env`, then `docker compose up -d` again.
- **"vendor/autoload.php not found"**: run
  `docker compose exec app composer install`.
- **Blank/500 page with no obvious cause**: check
  `docker compose logs app` and `storage/logs/laravel.log` inside the
  container (`docker compose exec app tail -f storage/logs/laravel.log`).
- **Styles look unstyled**: run `npm run build` (or keep `npm run dev`
  running) — Blade views reference compiled assets via Vite.
