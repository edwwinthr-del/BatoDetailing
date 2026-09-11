# BatoDetailing — Build Progress (v2: real spec)

> Tracks build progress so work survives context/token loss. UPDATE AFTER EVERY PHASE.
> `ProjectInstructions.txt` was EMPTY at first build; it now contains the full SaaS spec.
> v1 build (cars/bookings/payments/service_packages) is being REPLACED by the spec domain
> (vehicles/appointments/invoices/reviews/loyalty/settings).

## Environment facts

- PHP 8.5.5 (XAMPP), Laravel 13 (spec says Laravel 12/PHP 8.4 — using installed versions, noted in README)
- .env: pgsql on localhost:5432 db=batodetailing user=postgres pass=edo123; Mailtrap SMTP configured; queue/session/cache = database
- Tests run on sqlite :memory: (phpunit.xml)
- Models use `#[Fillable]` attribute style
- DB note: user's postgres DB may contain v1 tables → README tells them `migrate:fresh --seed`

## Phases

- [x] A: Packages — barryvdh/laravel-dompdf (composer), alpinejs (npm) installed OK
- [x] B: Migrations done (+ v1 files deleted: old migrations/models/factories/controllers/views/tests, PSQLInstructions.txt, postgres_schema.sql)
- [x] B2: Models done (Role, User w/ MustVerifyEmail+name accessor+loyaltyBalance, Vehicle, Service, Appointment, Invoice, Review, LoyaltyPoint, Setting)
- [x] D: Services done — SettingsService (cached k/v + DEFAULTS), PriceCalculator, LoyaltyService (award/redeem/refund, idempotent), BookingService (book + business-hours + double-booking + redeem validation), InvoiceService (BATO-YYYY-MM-XXXXXX, dompdf → storage, queued email)
- [x] G(partial): Event AppointmentStatusChanged + listener HandleAppointmentStatusChange (approved→notify; rejected→refund+notify; cancelled→refund; completed→award points+notify+invoice); notifications BookingCreated/Approved/Rejected, AppointmentCompleted, InvoiceSent (PDF attached) — all ShouldQueue
- [ ] B (old line, superseded) — rewrite users (first/last/username/phone/promo_emails/is_blocked/avatar/role_id),
      roles, vehicles, services (base_price), appointments, appointment_services, invoices, reviews,
      loyalty_points, settings. DELETE v1 migrations (cars, service_packages, bookings, payments, add_is_admin).
- [x] C: Factories (Role, User w/ admin/blocked/optedIn states, Vehicle, Service, Appointment w/ completed state, Invoice, Review, LoyaltyPoint) + seeders (RoleSeeder, ServiceSeeder 6 spec services, SettingSeeder, DatabaseSeeder: admin@batodetailing.com + test@example.com, password "password")
- [x] E: Auth done — Auth\AuthController (register w/ spec fields + promo checkbox + email verification redirect; login w/ blocked check + admin redirect), PasswordResetController, EmailVerificationController, RegisterRequest; middleware SetLocale + EnsureUserIsNotBlocked (appended to web group); policies Vehicle/Appointment/Invoice; AppServiceProvider (admin gate via role, event listener wiring, login+contact rate limiters, SettingsService singleton)
- [x] H: User controllers — Dashboard, Vehicle (CRUD + image upload to storage/public), Appointment (index/create/store/show/cancel via BookingService), Review, Invoice (index/show/download w/ PDF regen), Home (services+testimonials from reviews+stats), Contact (queued mail + rate limit), Locale switch, Profile (avatar + promo toggle); ContactMessage + CampaignMail mailables, SendCampaign job
- [ ] D: Settings service (typed access to settings table: company info, business_hours, vehicle_type_modifiers, loyalty rules, default theme)
- [ ] E: Auth — register (new fields + promo checkbox), login (blocked check), logout, password reset, email verification
- [ ] F: Domain services — PriceCalculator (base+modifier), LoyaltyService (earn/redeem), InvoiceService (number BATO-YYYY-MM-XXXXXX, PDF via dompdf, storage, DB)
- [ ] G: Events/listeners/notifications — AppointmentStatusChanged; on Completed: generate invoice + award points;
      queued notifications: BookingCreated/Approved/Rejected/Completed, InvoiceSent (PDF attached)
- [ ] H: User panel — dashboard (vehicles/appointments/upcoming/points), vehicles CRUD (image upload),
      appointments (book w/ price calc + double-booking & business-hours prevention, cancel), reviews (1 per completed appt),
      invoices (list/download PDF), loyalty redemption at booking
- [x] I(controllers): Admin controllers done (Dashboard w/ 12-month chart data, User block/role, Vehicle, Appointment +calendar +status events, Service CRUD+toggle, Pricing, Settings, Invoice list/status/resend/regenerate/CSV, Report + ReportService + CSV/PDF export, Campaign) + routes/web.php complete
- [x] J(partial): app.css (dark variant + reveal animations), app.js (Alpine + IntersectionObserver reveal + counters), layout (dark toggle + locale switcher + mobile menu + footer w/ support form), components (card, status-badge, input-error), home (hero/about/services/gallery/testimonials/contact/counters), privacy, terms, welcome deleted
- [x] Views: auth (login/register/forgot/reset/verify), dashboard, profile, vehicles (index/create/edit/_form), appointments (index/create w/ Alpine live price calc/show w/ review form), invoices (index/show), pdf (invoice/report), mail (contact-message/campaign)
- [x] I(views): Admin views done (dashboard w/ Chart.js CDN, users, vehicles, appointments index/calendar/show, services CRUD, pricing, invoices, reports, campaigns, settings)
- [x] K: lang/sr.json (~250 strings), locale switcher in nav
- [x] M: 45 tests, 44 pass + 1 skipped (image upload needs GD ext, auto-skips). Fixed: listener double-registration (auto-discovery + manual Event::listen caused duplicate invoices), is_blocked/role_id not fillable (use forceFill), faker usernames break alpha_dash
- [x] N: README.txt written; pint clean; npm build OK; migrate:fresh --seed OK on PostgreSQL; storage:link done — dashboard (totals + Chart.js monthly charts + satisfaction), users (block/role/edit),
      vehicles, appointments (calendar + filters + status), services CRUD, pricing settings (modifiers + hours),
      reports (revenue/satisfaction/popularity, date range, CSV + PDF export), email campaigns (queued, opted-in only), settings
- [ ] J: Public site — premium home (hero/about/services/gallery/testimonials-from-reviews/contact form),
      footer, privacy, terms; scroll animations (fade-in/slide-up/counters); dark/light mode (Alpine + Tailwind dark variant)
- [ ] K: Localization — EN + SR lang JSON files, locale switcher in navbar, SetLocale middleware
- [ ] L: Security — Form Requests everywhere, policies (Vehicle, Appointment, Invoice, Review), rate limiting (login/contact), blocked-user middleware
- [ ] M: Tests — replace v1 tests; cover auth, vehicles, booking+pricing+double-booking, status flow→invoice+points, reviews, admin access
- [ ] N: README.txt (mandatory, per spec) + cleanup (delete v1 leftovers incl. PSQLInstructions/postgres_schema or update), pint, npm build, full test run, migrate:fresh --seed on postgres

## Decisions

- Roles: `roles` table + `users.role_id` (spec lists roles migration). Gate 'admin' checks role name.
- Appointments: multi-service select (pivot `appointment_services` stores price snapshot); total = sum(base) + vehicle-type modifier − loyalty discount
- Slots: 1-hour granularity; double-booking = same scheduled_at hour; business hours from settings (default Mon–Sat 08–18)
- Loyalty: settings `loyalty.points_per_euro` (default 1) earned on completion; redeem at booking: `loyalty.redeem_value` €/point (default 0.05)
- Invoice statuses: draft/issued/paid/unpaid/cancelled; auto-created as 'issued' on appointment completion
- Charts: Chart.js via CDN; Alpine via npm import in app.js
- Vehicle types: coupe, hatchback, sedan, suv, minivan, pickup
- Appointment statuses: pending, approved, in_progress, completed, cancelled, rejected
- Locale: session-based, `SetLocale` middleware, switcher posts to /locale/{en|sr}

## Log

- 2026-06-11: v1 complete (see git-less history in this file's previous version): basic cars/bookings/payments app, 29 tests passing.
- 2026-06-11: ProjectInstructions.txt now populated with full spec. Started v2 rebuild. Phase A started (composer dompdf + npm alpinejs in background).
