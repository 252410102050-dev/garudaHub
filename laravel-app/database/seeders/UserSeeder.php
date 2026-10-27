<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Semua akun dummy: password = "password"
        $users = [
            ['Pak RT Admin', 'admin@example.com', 'admin'],
            ['Agus Kakap', 'agus@example.com', 'klien'],
            ['Mie Ayam Mas Hambali', 'hambali@example.com', 'klien'],
            ['Basnya Mendat Mendut', 'basnya@example.com', 'klien'],
            ['Cak Ngoding Subuh', 'ngoding@example.com', 'developer'],
            ['Mbak Stack Overflow', 'stackoverflow@example.com', 'developer'],
            ['Bang Copas Handal', 'copas@example.com', 'developer'],
            ['Kang Debug Sampai Pagi', 'debug@example.com', 'developer'],
        ];

        foreach ($users as [$name, $email, $role]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => 'password',
                'role' => $role,
            ]);
        }
    }
}
