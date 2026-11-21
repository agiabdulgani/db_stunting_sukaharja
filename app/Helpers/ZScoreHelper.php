<?php

namespace App\Helpers;

class ZScoreHelper
{
    // Median dan SD Standar WHO/Kemenkes untuk TB/U (Contoh Sampel Umur 0-24 Bulan)
    protected static $standardTBU = [
        'L' => [ // Laki-laki (Umur bulan => [SD3neg, SD2neg, SD1neg, SD0, SD1, SD2, SD3])
            0  => [44.2, 46.1, 48.0, 49.9, 51.8, 53.7, 55.6],
            6  => [61.2, 63.3, 65.5, 67.6, 69.8, 71.9, 74.0],
            12 => [71.0, 73.4, 75.7, 78.1, 80.5, 82.9, 85.3],
            24 => [81.0, 83.8, 86.5, 89.3, 92.0, 94.8, 97.5],
        ],
        'P' => [ // Perempuan
            0  => [43.6, 45.4, 47.3, 49.1, 51.0, 52.9, 54.7],
            6  => [58.9, 61.2, 63.5, 65.7, 68.0, 70.3, 72.5],
            12 => [68.9, 71.4, 73.8, 76.2, 78.7, 81.1, 83.6],
            24 => [79.3, 82.1, 84.8, 87.6, 90.4, 93.2, 95.9],
        ]
    ];

    public static function hitungStunting($jenisKelamin, $umurBulan, $tinggiBadan)
    {
        $jk = strtoupper($jenisKelamin);
        $data = self::$standardTBU[$jk] ?? self::$standardTBU['L'];
        
        // Ambil data acuan umur terdekat
        $availableMonths = array_keys($data);
        sort($availableMonths);
        $closestMonth = $availableMonths[0];
        foreach ($availableMonths as $m) {
            if ($umurBulan >= $m) $closestMonth = $m;
        }

        $sd = $data[$closestMonth];
        $median = $sd[3]; // SD0

        // Perhitungan nilai Z-Score
        if ($tinggiBadan < $median) {
            $zScore = ($tinggiBadan - $median) / ($median - $sd[1]); // SD-2
        } else {
            $zScore = ($tinggiBadan - $median) / ($sd[5] - $median); // SD+2
        }

        $zScore = round($zScore, 2);

        // Klasifikasi Status Gizi Berdasarkan TB/U (Kemenkes RI)
        if ($zScore < -3.0) {
            $status = 'Sangat Pendek';
        } elseif ($zScore >= -3.0 && $zScore < -2.0) {
            $status = 'Pendek';
        } elseif ($zScore >= -2.0 && $zScore <= 3.0) {
            $status = 'Normal';
        } else {
            $status = 'Tinggi';
        }

        return [
            'zscore_tb_u' => $zScore,
            'status_gizi'  => $status
        ];
    }
}