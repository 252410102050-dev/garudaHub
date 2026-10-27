<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan penting: users dulu, baru projects, baru bids (foreign key)
        $this->call([
            UserSeeder::class,
            ProjectSeeder::class,
            BidSeeder::class,
        ]);
    }
}
