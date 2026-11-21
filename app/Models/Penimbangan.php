<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penimbangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'balita_id',
        'tanggal_timbang',
        'umur_bulan',
        'berat_badan',
        'tinggi_badan',
        'zscore_tb_u',
        'status_gizi',
    ];

    public function balita()
    {
        return $this->belongsTo(Balita::class);
    }
}