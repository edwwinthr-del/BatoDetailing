<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => Role::ADMIN]);
        Role::firstOrCreate(['name' => Role::WORKER]);
        Role::firstOrCreate(['name' => Role::USER]);
    }
}
