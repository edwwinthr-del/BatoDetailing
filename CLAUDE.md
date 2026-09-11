# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

BatoDetailing is a Laravel 13 (PHP 8.3) application for a car detailing business — bookings, cars, services, service packages, and payments. It is currently a freshly scaffolded project: most models and migrations are stubs (id + timestamps only) and routes only render the default `welcome` view. The frontend uses Vite + Tailwind CSS v4, with no JS framework yet (`resources/js/app.js`, `resources/css/app.css`).

## Commands

Use `php artisan` (not bare `php`) for all framework commands. On Windows, run via the project's PHP/Composer (XAMPP).

- **Install deps**: `composer install` and `npm install`
- **Dev servers (all-in-one)**: `composer run dev` — runs `php artisan serve`, queue listener, `pail` log tailer, and `npm run dev` (Vite) concurrently with `--kill-others`
- **Frontend dev**: `npm run dev` (Vite dev server)
- **Frontend build**: `npm run build`
- **Run all tests**: `composer run test` (clears config cache, then `php artisan test`)
- **Run a single test**: `php artisan test --filter=TestName` or `php artisan test path/to/TestFile.php`
- **Migrations**: `php artisan migrate` (SQLite DB at `database/database.sqlite`)
- **Database seeding**: `php artisan db:seed`
- **Tinker REPL**: `php artisan tinker`
- **Code style (Pint)**: `vendor/bin/pint`

## Architecture

- **Database**: SQLite (`database/database.sqlite`), configured via `.env` (`DB_CONNECTION=sqlite`). Sessions, cache, and queue all use the `database` driver.
- **Domain models** (`app/Models/`): `User`, `Car`, `Booking`, `Service`, `ServicePackage`, `Payment`. Relationships defined so far:
  - `User hasMany Car` and `User hasMany Booking`
  - `Service hasMany ServicePackage`
  - Other models (`Car`, `Booking`, `ServicePackage`, `Payment`) are currently empty stubs — relationships and fillable/cast attributes still need to be added as features are built.
- **Migrations** (`database/migrations/`): domain tables (`cars`, `services`, `service_packages`, `bookings`, `payments`) currently only define `id` and `timestamps`; columns need to be added alongside corresponding model logic.
- **Routes**: `routes/web.php` only defines `/` returning the `welcome` view. No controllers exist yet beyond the empty base `app/Http/Controllers/Controller.php`.
- **Frontend build**: Vite config (`vite.config.js`) wires `laravel-vite-plugin` and `@tailwindcss/vite`, with entry points `resources/css/app.css` and `resources/js/app.js`.
- **Tests**: PHPUnit, with `tests/Feature` and `tests/Unit` directories (currently only example tests).
