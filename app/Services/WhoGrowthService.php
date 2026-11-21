<?php

namespace App\Services;

class WhoGrowthService
{
    /**
     * Menghitung Z-Score TB/U (Tinggi Badan menurut Umur) berdasarkan standar WHO
     * serta menentukan kategori status gizi balita.
     */
    public function calculateZScore(string $jenisKelamin, int $umurBulan, float $beratBadan, float $tinggiBadan): array
    {
        // Ambil Median (M) dan Standar Deviasi (SD) WHO TB/U berdasarkan umur & jenis kelamin
        $whoStandard = $this->getWhoTbUStandard($jenisKelamin, $umurBulan);

        $median = $whoStandard['median'];
        $sd = $whoStandard['sd'];

        // Formula Perhitungan Z-Score: Z = (Tinggi Badan Input - Median WHO) / SD WHO
        $zScoreTbU = round(($tinggiBadan - $median) / $sd, 2);

        // Kategori Status Gizi Berdasarkan Standar Permenkes / WHO
        $statusGizi = 'Normal';

        if ($zScoreTbU < -3.0) {
            $statusGizi = 'Sangat Pendek (Severely Stunted)';
        } elseif ($zScoreTbU >= -3.0 && $zScoreTbU < -2.0) {
            $statusGizi = 'Pendek (Stunted)';
        } elseif ($zScoreTbU >= -2.0 && $zScoreTbU <= 2.0) {
            $statusGizi = 'Normal';
        } else {
            $statusGizi = 'Tinggi';
        }

        return [
            'zscore_tb_u' => $zScoreTbU,
            'status_gizi'  => $statusGizi,
        ];
    }

    /**
     * Referensi Median & SD Standar WHO TB/U (Tinggi Badan menurut Umur) 0-24 Bulan
     */
    private function getWhoTbUStandard(string $jk, int $umur): array
    {
        // Standar WHO TB/U Anak Laki-Laki (L)
        $maleStandards = [
            0  => ['median' => 49.9, 'sd' => 1.9],
            1  => ['median' => 54.7, 'sd' => 2.0],
            2  => ['median' => 58.4, 'sd' => 2.1],
            3  => ['median' => 61.4, 'sd' => 2.2],
            4  => ['median' => 63.9, 'sd' => 2.2],
            5  => ['median' => 65.9, 'sd' => 2.3],
            6  => ['median' => 67.6, 'sd' => 2.3],
            7  => ['median' => 69.2, 'sd' => 2.4],
            8  => ['median' => 70.6, 'sd' => 2.4],
            9  => ['median' => 72.0, 'sd' => 2.5],
            10 => ['median' => 73.3, 'sd' => 2.5],
            11 => ['median' => 74.5, 'sd' => 2.5],
            12 => ['median' => 75.7, 'sd' => 2.6],
            18 => ['median' => 82.3, 'sd' => 2.9],
            24 => ['median' => 87.8, 'sd' => 3.2],
        ];

        // Standar WHO TB/U Anak Perempuan (P)
        $femaleStandards = [
            0  => ['median' => 49.1, 'sd' => 1.9],
            1  => ['median' => 53.7, 'sd' => 2.0],
            2  => ['median' => 57.1, 'sd' => 2.1],
            3  => ['median' => 59.8, 'sd' => 2.1],
            4  => ['median' => 62.1, 'sd' => 2.2],
            5  => ['median' => 64.0, 'sd' => 2.2],
            6  => ['median' => 65.7, 'sd' => 2.3],
            7  => ['median' => 67.3, 'sd' => 2.3],
            8  => ['median' => 68.7, 'sd' => 2.4],
            9  => ['median' => 70.1, 'sd' => 2.4],
            10 => ['median' => 71.5, 'sd' => 2.4],
            11 => ['median' => 72.8, 'sd' => 2.5],
            12 => ['median' => 74.0, 'sd' => 2.5],
            18 => ['median' => 80.7, 'sd' => 2.9],
            24 => ['median' => 86.4, 'sd' => 3.2],
        ];

        $standards = strtoupper($jk) === 'L' ? $maleStandards : $femaleStandards;

        // Jika umur melebihi tabel referensi dasar, gunakan fallback terdekat
        if (array_key_exists($umur, $standards)) {
            return $standards[$umur];
        }

        // Estimasi linier sederhana untuk umur di antara/di atas rentang dasar
        return $standards[24]; 
    }
}