<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Balita;
use App\Models\Penimbangan;
use App\Models\User;

class BalitaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan minimal ada 1 User Admin aktif di sistem (tanpa kolom role)
        User::firstOrCreate(
            ['email' => 'admin@agisttcipasung.go.id'],
            [
                'name' => 'Admin Sukaharja',
                'password' => bcrypt('password123'),
            ]
        );

        // 2. Data Balita 1 (Normal)
        $b1 = Balita::updateOrCreate(
            ['nik' => '3206011203220001'],
            [
                'nama' => 'Ahmad Fauzi',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2022-03-12',
                'nama_orang_tua' => 'Siti Aminah',
                'rt_rw' => '001/002',
                'posyandu' => 'Posyandu Mawar 1',
                'status_gizi' => 'Normal',
            ]
        );

        Penimbangan::updateOrCreate(
            ['balita_id' => $b1->id, 'umur_bulan' => 24],
            [
                'tanggal_timbang' => now(),
                'berat_badan' => 12.2,
                'tinggi_badan' => 87.5,
                'zscore_tb_u' => 0.12,
                'status_gizi' => 'Normal',
            ]
        );

        // 3. Data Balita 2 (Terindikasi Stunting)
        $b2 = Balita::updateOrCreate(
            ['nik' => '3206011505230002'],
            [
                'nama' => 'Siti Nurhaliza',
                'jenis_kelamin' => 'P',
                'tanggal_lahir' => '2023-05-15',
                'nama_orang_tua' => 'Dewi Rahayu',
                'rt_rw' => '003/001',
                'posyandu' => 'Posyandu Melati 2',
                'status_gizi' => 'Sangat Pendek (Stunting)',
            ]
        );

        Penimbangan::updateOrCreate(
            ['balita_id' => $b2->id, 'umur_bulan' => 12],
            [
                'tanggal_timbang' => now(),
                'berat_badan' => 7.1,
                'tinggi_badan' => 68.0,
                'zscore_tb_u' => -2.45,
                'status_gizi' => 'Sangat Pendek (Stunting)',
            ]
        );

        // 4. Data Balita 3 (Normal)
        $b3 = Balita::updateOrCreate(
            ['nik' => '3206012008230003'],
            [
                'nama' => 'Bagas Pratama',
                'jenis_kelamin' => 'L',
                'tanggal_lahir' => '2023-08-20',
                'nama_orang_tua' => 'Rina Amalia',
                'rt_rw' => '002/002',
                'posyandu' => 'Posyandu Mawar 1',
                'status_gizi' => 'Normal',
            ]
        );

        Penimbangan::updateOrCreate(
            ['balita_id' => $b3->id, 'umur_bulan' => 10],
            [
                'tanggal_timbang' => now(),
                'berat_badan' => 9.5,
                'tinggi_badan' => 73.0,
                'zscore_tb_u' => 0.05,
                'status_gizi' => 'Normal',
            ]
        );
    }
}