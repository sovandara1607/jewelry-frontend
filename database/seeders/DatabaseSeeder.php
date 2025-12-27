<?php

namespace Database\Seeders; // <-- 1. Check this namespace

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder // <-- 2. Check this class name
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Example of how to call another seeder
        // $this->call([
        //     ProductSeeder::class,
        // ]);

        // Or you can create a user directly
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}