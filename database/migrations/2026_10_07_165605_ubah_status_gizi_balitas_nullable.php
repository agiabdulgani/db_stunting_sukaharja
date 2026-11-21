<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        
        Schema::table('balitas', function (Blueprint $table) {
            $table->string('status_gizi')->nullable()->default(null)->change();
        });

        
        DB::table('balitas')
            ->whereNotIn('id', function ($query) {
                $query->select('balita_id')
                      ->from('penimbangans')
                      ->whereNotNull('balita_id');
            })
            ->update(['status_gizi' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    
        DB::table('balitas')
            ->whereNull('status_gizi')
            ->update(['status_gizi' => 'Normal']);

        
        Schema::table('balitas', function (Blueprint $table) {
            $table->string('status_gizi')->default('Normal')->change();
        });
    }
};