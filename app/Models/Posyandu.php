<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    protected $fillable = ['nama_posyandu', 'desa', 'ketua_kader'];

    public function balitas()
    {
        return $this->hasMany(Balita::class, 'posyandu_id');
    }
}