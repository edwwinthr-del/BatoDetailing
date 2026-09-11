<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected const CACHE_KEY = 'app_settings';

    /**
     * @var array<string, mixed>
     */
    public const DEFAULTS = [
        'company.name' => 'BatoDetailing',
        'company.email' => 'info@batodetailing.com',
        'company.phone' => '+381 60 123 4567',
        'company.address' => 'Bulevar Oslobodjenja 1, Novi Sad, Serbia',
        'business_hours.open' => 8,
        'business_hours.close' => 18,
        'business_hours.days' => [1, 2, 3, 4, 5, 6], // ISO weekdays, Mon–Sat
        'modifiers.coupe' => 5.00,
        'modifiers.hatchback' => 0.00,
        'modifiers.sedan' => 5.00,
        'modifiers.suv' => 15.00,
        'modifiers.minivan' => 20.00,
        'modifiers.pickup' => 15.00,
        'loyalty.points_per_euro' => 1,
        'loyalty.redeem_value' => 0.05, // € discount per point
        'loyalty.min_redeem' => 100,    // minimum points required to redeem
        'theme.default' => 'light',
    ];

    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        return $settings[$key] ?? self::DEFAULTS[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => json_encode($value)]);

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => json_encode($value)]);
        }

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $stored = Cache::rememberForever(self::CACHE_KEY, function (): array {
            return Setting::pluck('value', 'key')
                ->map(fn (string $value): mixed => json_decode($value, true))
                ->all();
        });

        return array_merge(self::DEFAULTS, $stored);
    }

    public function modifierFor(string $vehicleType): float
    {
        return (float) $this->get('modifiers.'.$vehicleType, 0);
    }

    /**
     * @return array{open: int, close: int, days: array<int>}
     */
    public function businessHours(): array
    {
        return [
            'open' => (int) $this->get('business_hours.open'),
            'close' => (int) $this->get('business_hours.close'),
            'days' => (array) $this->get('business_hours.days'),
        ];
    }
}
