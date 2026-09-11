<?php

namespace App\Providers;

use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // HandleAppointmentStatusChange is auto-discovered from app/Listeners.
        Gate::define('admin', fn (User $user): bool => $user->isAdmin());

        // Admins can also use the worker panel.
        Gate::define('worker', fn (User $user): bool => $user->isWorker() || $user->isAdmin());

        RateLimiter::for('login', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->input('email').'|'.$request->ip());
        });

        RateLimiter::for('contact', function (Request $request): Limit {
            return Limit::perMinute(3)->by($request->ip());
        });
    }
}
