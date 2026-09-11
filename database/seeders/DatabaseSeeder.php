<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ServiceSeeder::class,
            SettingSeeder::class,
        ]);

        User::factory()->admin()->create([
            'first_name' => 'Bato',
            'last_name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@batodetailing.com',
            'phone' => '+381 60 123 4567',
        ]);

        User::factory()->worker()->create([
            'first_name' => 'Bato',
            'last_name' => 'Worker',
            'username' => 'worker',
            'email' => 'worker@batodetailing.com',
            'phone' => '+381 60 111 2222',
        ]);

        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone' => '+381 60 765 4321',
        ]);
    }
}
