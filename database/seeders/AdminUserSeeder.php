<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'adminagi@sttcipasung.ac.id'],
            [
                'name' => 'Admin Kecamatan Singaparna',
                'password' => Hash::make('password123'), // Ganti dengan password yang diinginkan
            ]
        );
    }
}