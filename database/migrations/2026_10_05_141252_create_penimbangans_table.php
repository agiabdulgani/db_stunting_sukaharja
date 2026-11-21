<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penimbangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balita_id')->constrained('balitas')->cascadeOnDelete();
            $table->date('tanggal_timbang');
            $table->integer('umur_bulan');
            $table->decimal('berat_badan', 5, 2);
            $table->decimal('tinggi_badan', 5, 2);
            $table->float('zscore_tb_u')->nullable();
            $table->string('status_gizi')->default('Normal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penimbangans');
    }
};