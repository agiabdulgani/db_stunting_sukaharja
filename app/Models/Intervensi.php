<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intervensi extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database
     */
    protected $table = 'intervensis';

    /**
     * Kolom yang diperbolehkan diisi massal
     */
    protected $guarded = ['id'];

    /**
     * Format tipe data kolom
     */
    protected $casts = [
        'tanggal_intervensi' => 'date',
    ];

    /**
     * Relasi balik ke Model Balita (BelongsTo)
     */
    public function balita()
    {
        return $this->belongsTo(Balita::class);
    }
}