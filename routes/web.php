<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\Worker\WorkerAppointmentController;
use Illuminate\Support\Facades\Route;

// Public site
Route::get('/', HomeController::class)->name('home');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');
Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle:contact')->name('contact.send');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Guest auth
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// Email verification
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/verify-email', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')->name('verification.verify');
    Route::post('/verify-email/resend', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')->name('verification.send');
});

// User panel (verified users)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('vehicles', VehicleController::class)->except(['show']);

    Route::resource('appointments', AppointmentController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{appointment}/review', [ReviewController::class, 'store'])->name('appointments.review');

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
});

// Worker panel
Route::middleware(['auth', 'verified', 'can:worker'])->prefix('worker')->name('worker.')->group(function () {
    Route::get('/', [WorkerAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [WorkerAppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('/appointments/{appointment}/status', [WorkerAppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::post('/appointments/{appointment}/comments', [WorkerAppointmentController::class, 'storeComment'])->name('appointments.comments.store');
});

// Admin panel
Route::middleware(['auth', 'verified', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle-block', [Admin\UserController::class, 'toggleBlock'])->name('users.toggle-block');

    Route::get('/vehicles', [Admin\VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/vehicles/{vehicle}/edit', [Admin\VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('/vehicles/{vehicle}', [Admin\VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicles/{vehicle}', [Admin\VehicleController::class, 'destroy'])->name('vehicles.destroy');

    Route::get('/appointments', [Admin\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/calendar', [Admin\AppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::get('/appointments/{appointment}', [Admin\AppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('/appointments/{appointment}/status', [Admin\AppointmentController::class, 'updateStatus'])->name('appointments.status');

    Route::get('/services', [Admin\ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [Admin\ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [Admin\ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}/edit', [Admin\ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [Admin\ServiceController::class, 'update'])->name('services.update');
    Route::patch('/services/{service}/toggle', [Admin\ServiceController::class, 'toggle'])->name('services.toggle');

    Route::get('/pricing', [Admin\PricingController::class, 'edit'])->name('pricing.edit');
    Route::put('/pricing', [Admin\PricingController::class, 'update'])->name('pricing.update');

    Route::get('/invoices', [Admin\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/export', [Admin\InvoiceController::class, 'export'])->name('invoices.export');
    Route::patch('/invoices/{invoice}/status', [Admin\InvoiceController::class, 'updateStatus'])->name('invoices.status');
    Route::post('/invoices/{invoice}/resend', [Admin\InvoiceController::class, 'resend'])->name('invoices.resend');
    Route::post('/invoices/{invoice}/regenerate', [Admin\InvoiceController::class, 'regenerate'])->name('invoices.regenerate');

    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/csv', [Admin\ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('/reports/export/pdf', [Admin\ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    Route::get('/campaigns', [Admin\CampaignController::class, 'create'])->name('campaigns.create');
    Route::post('/campaigns', [Admin\CampaignController::class, 'send'])->name('campaigns.send');

    Route::get('/settings', [Admin\SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
});
