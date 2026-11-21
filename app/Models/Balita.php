<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balita extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database
     */
    protected $table = 'balitas';

    /**
     * Izinkan seluruh kolom diisi kecuali ID
     */
    protected $guarded = ['id'];

    /**
     * Casts tipe data atribut
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Relasi ke seluruh riwayat penimbangan balita
     */
    public function penimbangans()
    {
        return $this->hasMany(Penimbangan::class);
    }

    /**
     * Relasi ke data penimbangan terbaru (latest penimbangan)
     */
    public function penimbanganTerakhir()
    {
        return $this->hasOne(Penimbangan::class)->latestOfMany();
    }

    /**
     * Relasi ke data Intervensi PMT (Pemberian Makanan Tambahan)
     */
    public function intervensis()
    {
        return $this->hasMany(Intervensi::class);
    }
}