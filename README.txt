==============================================================
 BatoDetailing — Car Detailing SaaS Platform
==============================================================

A full Laravel application for a car detailing business:
public marketing site, user panel (vehicles, bookings, loyalty,
invoices, reviews), and a complete admin panel (users, services,
pricing, calendar, reports, email campaigns, settings) with
automatic PDF invoicing, queued email notifications, EN/SR
localization and dark/light mode.

--------------------------------------------------------------
 1. REQUIREMENTS
--------------------------------------------------------------
- PHP 8.3+ (project currently runs on PHP 8.5 / Laravel 13)
- Composer
- Node.js 20+ and npm
- PostgreSQL 14+
- PHP extensions: pdo_pgsql, pgsql, mbstring, openssl, fileinfo
  (enable in php.ini), and gd (optional — needed for vehicle
  image upload validation; uncomment "extension=gd" in php.ini)

--------------------------------------------------------------
 2. INSTALLATION
--------------------------------------------------------------
    cd batodetailing
    composer install
    npm install
    copy .env.example .env        (skip if .env already exists)
    php artisan key:generate      (skip if APP_KEY already set)

--------------------------------------------------------------
 3. POSTGRESQL SETUP
--------------------------------------------------------------
Install PostgreSQL (https://www.postgresql.org/download/windows/),
then create the database (in psql or pgAdmin):

    CREATE DATABASE batodetailing;

Enable the PHP driver in C:\xampp\php\php.ini (remove ";"):

    extension=pdo_pgsql
    extension=pgsql

--------------------------------------------------------------
 4. ENVIRONMENT VARIABLES (.env)
--------------------------------------------------------------
Database:

    DB_CONNECTION=pgsql
    DB_HOST=localhost
    DB_PORT=5432
    DB_DATABASE=batodetailing
    DB_USERNAME=postgres
    DB_PASSWORD=<your password>

Mail (Mailtrap example — any SMTP works):

    MAIL_MAILER=smtp
    MAIL_HOST=sandbox.smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=<mailtrap username>
    MAIL_PASSWORD=<mailtrap password>
    MAIL_FROM_ADDRESS="info@batodetailing.com"
    MAIL_FROM_NAME="${APP_NAME}"

Queues / sessions / cache all use the "database" driver
(no Redis needed):

    QUEUE_CONNECTION=database
    SESSION_DRIVER=database
    CACHE_STORE=database

--------------------------------------------------------------
 5. MIGRATIONS & SEEDERS
--------------------------------------------------------------
    php artisan migrate:fresh --seed

This creates all tables (roles, users, vehicles, services,
appointments, appointment_services, invoices, reviews,
loyalty_points, settings + framework tables) and seeds:
- roles "admin" and "user"
- the 6 detailing services with base prices
- default settings (company info, business hours Mon–Sat 8–18,
  vehicle type price modifiers, loyalty rules, default theme)
- two accounts (see DEFAULT CREDENTIALS below)

--------------------------------------------------------------
 6. STORAGE SETUP
--------------------------------------------------------------
    php artisan storage:link

Vehicle images and avatars are stored on the "public" disk
(storage/app/public → public/storage). Invoice PDFs are stored
privately in storage/app/private/invoices and are only served
through authorized download routes.

--------------------------------------------------------------
 7. QUEUE WORKER (MANDATORY for emails & invoices)
--------------------------------------------------------------
All notifications, invoice emails and campaigns are queued.
Run a worker:

    php artisan queue:work

(`composer run dev` starts one automatically — see step 9.)

--------------------------------------------------------------
 8. FRONTEND BUILD
--------------------------------------------------------------
    npm run build        (production build)
    npm run dev          (Vite dev server with hot reload)

Stack: Tailwind CSS v4 + Alpine.js + Chart.js (CDN).

--------------------------------------------------------------
 9. RUNNING THE PROJECT
--------------------------------------------------------------
Easiest (server + queue worker + log tail + Vite in one command):

    composer run dev

Or manually:

    php artisan serve          (http://localhost:8000)
    php artisan queue:work     (in a second terminal)
    npm run dev                (in a third terminal)

--------------------------------------------------------------
 10. DEFAULT ADMIN CREDENTIALS
--------------------------------------------------------------
    Admin:     admin@batodetailing.com   / password
    Customer:  test@example.com          / password

The admin panel lives at /admin.

--------------------------------------------------------------
 11. LOCALIZATION (EN + SR)
--------------------------------------------------------------
The language switcher (EN | SR) is in the navbar. Translations
live in lang/sr.json (English strings are the keys). The chosen
locale is stored in the session via the SetLocale middleware.
To add a language: create lang/<code>.json and add the code to
App\Http\Middleware\SetLocale::SUPPORTED.

--------------------------------------------------------------
 12. INVOICE SYSTEM
--------------------------------------------------------------
When an admin sets an appointment to "Completed":
1. An invoice is generated automatically with a unique number
   in the format BATO-YYYY-MM-XXXXXX.
2. A PDF (barryvdh/laravel-dompdf) is rendered and saved to
   storage (invoices/<number>.pdf); the path is stored in the DB.
3. The invoice row is stored with status "issued".
4. Loyalty points are awarded (floor(total × points_per_euro)).
5. A queued email "Your BatoDetailing Invoice" with the PDF
   attached is sent to the customer.

Admins can change invoice status (draft/issued/paid/unpaid/
cancelled), resend the email, regenerate the PDF and export all
invoices to CSV from /admin/invoices. Users can view and
download their invoices from /invoices.

--------------------------------------------------------------
 13. PRICING & BOOKING RULES
--------------------------------------------------------------
Final price = sum of selected service base prices
            + vehicle type modifier (admin-configurable)
            − loyalty discount (optional points redemption)

Bookings are 1-hour slots. The system rejects double bookings
on the same slot and bookings outside the configured business
hours/days (/admin/pricing).

--------------------------------------------------------------
 14. TESTS
--------------------------------------------------------------
    composer run test

45 feature tests cover auth (incl. blocking & verification),
vehicles, booking + price calculation + double-booking +
business hours + loyalty redemption, the status flow (invoice
generation, PDF, points, notifications), reviews, and the
admin panel (roles, blocking, services, pricing, campaigns,
report exports). Tests run on an in-memory SQLite database.

--------------------------------------------------------------
 15. FOLDER STRUCTURE (key paths)
--------------------------------------------------------------
app/
  Events/            AppointmentStatusChanged
  Listeners/         HandleAppointmentStatusChange (auto-discovered;
                     invoice + points + notifications on status change)
  Http/Controllers/  Public + user panel controllers
  Http/Controllers/Admin/   Admin panel controllers
  Http/Middleware/   SetLocale, EnsureUserIsNotBlocked
  Http/Requests/     Form Request validation classes
  Jobs/              SendCampaign (queued promo emails)
  Mail/              ContactMessage, CampaignMail
  Models/            Role, User, Vehicle, Service, Appointment,
                     Invoice, Review, LoyaltyPoint, Setting
  Notifications/     BookingCreated/Approved/Rejected,
                     AppointmentCompleted, InvoiceSent (PDF attached)
  Policies/          Vehicle, Appointment, Invoice policies
  Services/          SettingsService, PriceCalculator,
                     LoyaltyService, BookingService,
                     InvoiceService, ReportService
database/
  migrations/        All schema (PostgreSQL-compatible)
  seeders/           Role/Service/Setting/Database seeders
  factories/         Model factories for tests & seeding
lang/
  sr.json            Serbian translations
resources/
  views/             Blade templates (home, auth, user panel,
                     admin panel, pdf/, mail/, components/)
  css/app.css        Tailwind v4 + dark variant + animations
  js/app.js          Alpine + scroll reveal + counters
routes/web.php       All routes (public / auth / user / admin)
PROGRESS.md          Build log (development notes)

==============================================================
 The project runs immediately after steps 2–9 above.
==============================================================
