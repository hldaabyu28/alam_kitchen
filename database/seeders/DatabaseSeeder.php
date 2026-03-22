<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\RoleAndPermissionSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil seeder lain
        $this->call([
            CategorySeeder::class ,
            RoleAndPermissionSeeder::class ,
        ]);

        // Seeder user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}