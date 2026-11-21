<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom Z-Score & Status di tabel penimbangans jika belum ada
        Schema::table('penimbangans', function (Blueprint $table) {
            if (!Schema::hasColumn('penimbangans', 'zscore_bb_u')) {
                $table->decimal('zscore_bb_u', 5, 2)->nullable()->after('tinggi_badan');
            }
            if (!Schema::hasColumn('penimbangans', 'zscore_tb_u')) {
                $table->decimal('zscore_tb_u', 5, 2)->nullable()->after('zscore_bb_u');
            }
            if (!Schema::hasColumn('penimbangans', 'zscore_bb_tb')) {
                $table->decimal('zscore_bb_tb', 5, 2)->nullable()->after('zscore_tb_u');
            }
            if (!Schema::hasColumn('penimbangans', 'status_gizi')) {
                $table->enum('status_gizi', ['Sangat Pendek', 'Pendek', 'Normal', 'Tinggi'])->default('Normal')->after('zscore_bb_tb');
            }
        });

        // 2. Tabel Modul Intervensi & PMT
        if (!Schema::hasTable('intervensis')) {
            Schema::create('intervensis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('balita_id')->constrained('balitas')->onDelete('cascade');
                $table->date('tanggal_pemberian');
                $table->string('jenis_pmt');
                $table->text('keterangan')->nullable();
                $table->string('petugas')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::table('penimbangans', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('penimbangans', 'zscore_bb_u')) $columnsToDrop[] = 'zscore_bb_u';
            if (Schema::hasColumn('penimbangans', 'zscore_tb_u')) $columnsToDrop[] = 'zscore_tb_u';
            if (Schema::hasColumn('penimbangans', 'zscore_bb_tb')) $columnsToDrop[] = 'zscore_bb_tb';
            if (Schema::hasColumn('penimbangans', 'status_gizi')) $columnsToDrop[] = 'status_gizi';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        Schema::dropIfExists('intervensis');
    }
};