<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat User Admin Utama (tanpa kolom role)
        User::firstOrCreate(
            ['email' => 'admin@agisttcipasung.go.id'],
            [
                'name' => 'Admin Kec.singaparna',
                'password' => bcrypt('password123'),
            ]
        );

        // 2. Jalankan Seeder Balita & Penimbangan
        $this->call([
            BalitaSeeder::class,
        ]);
    }
}