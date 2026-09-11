# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

BatoDetailing is a Laravel 13 (PHP 8.3+) application for a car detailing business: customers register
vehicles, book appointments for one or more services, earn and redeem loyalty points, leave reviews, and
download PDF invoices. Staff work the jobs through a worker panel; admins manage users, services, pricing,
invoices, reports, settings, and email campaigns.

The app is feature-complete and covered by a passing test suite — it is **not** a scaffold. Server-rendered
Blade + Alpine.js + Tailwind v4; no SPA, no API.

Two companion docs carry context this file deliberately does not duplicate:
- `ProjectInstructions.txt` — the original product spec.
- `PROGRESS.md` — phase-by-phase build log and the reasoning behind past decisions. Note it lags the code
  in places (e.g. its test count); the code is the source of truth.

## Commands

Use `php artisan` (not bare `php`) for framework commands. On Windows this runs under XAMPP.

- **First-time setup**: `composer run setup` (install, `.env`, key, migrate, npm install, build)
- **Dev servers (all-in-one)**: `composer run dev` — runs `php artisan serve`, `queue:listen`, and `npm run dev`
  concurrently with `--kill-others`
- **Frontend**: `npm run dev` / `npm run build`
- **All tests**: `composer run test` (clears config cache, then `php artisan test`)
- **Single test**: `php artisan test --filter=TestName` or `php artisan test tests/Feature/BookingTest.php`
- **Code style**: `vendor/bin/pint` — run before finishing; the repo is kept Pint-clean
- **DB reset**: `php artisan migrate:fresh --seed`
- **Uploads**: `php artisan storage:link` (required once, or vehicle/avatar images 404)

A queue worker must be running for notifications, invoice emails, contact messages, and campaigns —
they all implement `ShouldQueue`. `composer run dev` starts one.

## Database

- **Local dev uses PostgreSQL** (`.env`: `DB_CONNECTION=pgsql`, database `batodetailing`), even though
  `.env.example` still ships Laravel's `sqlite` default. `database/database.sqlite` is a leftover from an
  earlier build and is **not** the dev database.
- **Tests always run on SQLite `:memory:`** (forced in `phpunit.xml`). Anything Postgres-specific in a query
  has to work on SQLite too, or the tests will not catch it.
- Sessions, cache, and queue all use the `database` driver.

## Architecture

### Roles and authorization

`roles` table + `users.role_id` (`admin` / `worker` / `user`). Two gates are defined in
`AppServiceProvider::boot()`: `admin`, and `worker` (which **admins also pass**). Route groups apply them via
`can:admin` / `can:worker`. Per-record checks live in `app/Policies/` (`Vehicle`, `Appointment`, `Invoice`) and
are invoked with `Gate::authorize(...)` inside controllers.

### Route layout (`routes/web.php`)

Public site → guest auth → `auth` (logout + email verification) → `auth,verified` (user panel) →
`worker` prefix (`can:worker`) → `admin` prefix (`can:admin`). Middleware `SetLocale` and
`EnsureUserIsNotBlocked` are appended to the `web` group in `bootstrap/app.php`. Rate limiters `login`
(5/min) and `contact` (3/min) are registered in `AppServiceProvider`.

### Business logic lives in `app/Services/`

Controllers stay thin — they resolve a service, call one method, and redirect. Do not push domain rules back
into controllers.

- **`SettingsService`** — cached key/value store over the `settings` table, merged over a `DEFAULTS` const
  (company info, business hours, per-vehicle-type price modifiers, loyalty rules, default theme). Reads go
  through `get()`/`all()`; writes through `set()`/`setMany()`, which invalidate the `app_settings` cache key.
  Registered as a singleton. Adding a new setting means adding it to `DEFAULTS`.
- **`PriceCalculator`** — `total = sum(service base_price) + vehicle-type modifier − loyalty discount`.
  Caps the redeemed points so a discount can never exceed the gross total.
- **`LoyaltyService`** — awards points on completion, redeems at booking, refunds on cancel/reject. Written
  to be idempotent; `loyalty_points` rows are an append-only ledger and a user's balance is their `SUM(points)`.
- **`BookingService`** — validates business hours and slot availability, then creates the appointment and its
  `appointment_services` pivot rows (each storing a **price snapshot**) in a transaction. Slots are 1-hour
  granularity; a clash on the same `scheduled_at` (ignoring cancelled/rejected) is a double booking. Rule
  violations throw `ValidationException` so they surface as normal form errors.
- **`InvoiceService`** — numbers invoices `BATO-YYYY-MM-XXXXXX` (sequential within the month), renders the PDF
  via dompdf into `storage/app/private/invoices/`, and notifies the customer. `generateFor()` is idempotent —
  it returns the existing invoice if one exists.
- **`ReportService`** — revenue, satisfaction, and service-popularity aggregates for the admin reports page.

### Appointment status flow

Status changes are made by admin/worker controllers, which then fire `AppointmentStatusChanged`.
`HandleAppointmentStatusChange` reacts: approved → notify; rejected → refund points + notify;
cancelled → refund points; completed → award points + notify + generate invoice.

The listener is **auto-discovered** from `app/Listeners`. Do not also register it manually — doing so
previously caused every completed appointment to generate two invoices.

### Model conventions

Models use Laravel 13's PHP attributes — `#[Fillable([...])]` and `#[Hidden([...])]` on the class — rather than
`$fillable`/`$hidden` properties. Follow that style for new models.

Owner and privilege columns are deliberately left out of `#[Fillable]`:
- `user_id` is never fillable; records are created through the relationship (`$user->appointments()->create(...)`,
  `$request->user()->vehicles()->create(...)`).
- `role_id` and `is_blocked` are set with `forceFill([...])->save()` (see `Admin\UserController`).

Status values are class constants with a `STATUSES` array (`Appointment`, `Invoice`); validate against those,
never against string literals. Same for `Vehicle::TYPES` and `Role::ADMIN|WORKER|USER`.

### Frontend

- Blade views in `resources/views/`, grouped by panel (`admin/`, `worker/`, plus user-facing dirs). Shared
  pieces are anonymous components in `resources/views/components/` (`layout`, `card`, `status-badge`,
  `input-error`); `pdf/` holds dompdf templates and `mail/` the mailable templates.
- Tailwind v4 with CSS-first config in `resources/css/app.css` (`@theme`, and a `@custom-variant dark` bound to
  the `.dark` class — not the `prefers-color-scheme` default). Dark mode is toggled by Alpine and persisted in
  `localStorage`, with the initial class set by an inline script in `layout.blade.php` to avoid a flash.
- Alpine is imported in `resources/js/app.js` (npm), which also wires the scroll-reveal and counter
  `IntersectionObserver`s. Chart.js is loaded from a CDN, only in admin views.
- Uploads go to the `public` disk: `vehicles/` and `avatars/`.

### Localization

English strings are the source text, written inline as `__('…')`; `lang/sr.json` holds the Serbian
translations. Supported locales are the `SetLocale::SUPPORTED` const; the switcher stores the choice in the
session. When adding user-facing copy, add the matching `sr.json` key.

## Testing

Feature tests in `tests/Feature/` cover auth, vehicles, booking + pricing + double-booking, the status flow
(invoice + points), reviews, and worker/admin access. 56 tests currently pass. The vehicle image-upload test
skips itself when the GD extension is unavailable.
