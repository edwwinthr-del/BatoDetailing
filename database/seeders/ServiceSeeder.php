<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'Interior Wash', 'description' => 'Thorough vacuum, dashboard wipe-down, window cleaning and air freshening.', 'base_price' => 25.00, 'duration_minutes' => 60],
            ['name' => 'Exterior Wash', 'description' => 'Hand wash, dry, wheel cleaning and tire shine for a spotless exterior.', 'base_price' => 20.00, 'duration_minutes' => 45],
            ['name' => 'Full Wash', 'description' => 'Complete interior and exterior wash — the best of both in one visit.', 'base_price' => 40.00, 'duration_minutes' => 90],
            ['name' => 'Deep Interior Cleaning', 'description' => 'Seat and carpet shampooing, steam cleaning, leather care and odor removal.', 'base_price' => 90.00, 'duration_minutes' => 180],
            ['name' => 'Detailed Exterior Cleaning', 'description' => 'Clay bar decontamination, machine polish and protective wax coating.', 'base_price' => 120.00, 'duration_minutes' => 240],
            ['name' => 'Full Premium Detailing', 'description' => 'Our flagship package: complete deep interior and exterior detailing with premium products.', 'base_price' => 220.00, 'duration_minutes' => 420],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['name' => $service['name']], $service + ['is_active' => true]);
        }
    }
}
