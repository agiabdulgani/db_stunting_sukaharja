<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posyandus', function (Blueprint $table) {
            $table->id();
            $table->string('nama_posyandu')->unique();
            $table->string('desa')->nullable();
            $table->string('ketua_kader')->nullable();
            $table->timestamps();
        });

        Schema::table('balitas', function (Blueprint $table) {
            $table->foreignId('posyandu_id')->nullable()->after('posyandu')
                  ->constrained('posyandus')->nullOnDelete();
        });

        // 3. Pindahkan nama posyandu yang sudah ada di data balita ke master
        $namaList = DB::table('balitas')
            ->whereNotNull('posyandu')
            ->where('posyandu', '!=', '')
            ->distinct()
            ->pluck('posyandu');

        foreach ($namaList as $asli) {
            $nama = trim($asli);

            if ($nama === '' || ctype_digit($nama)) {
                continue;
            }

            $id = DB::table('posyandus')->where('nama_posyandu', $nama)->value('id')
                ?? DB::table('posyandus')->insertGetId([
                    'nama_posyandu' => $nama,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

            DB::table('balitas')->where('posyandu', $asli)->update([
                'posyandu_id' => $id,
                'posyandu'    => $nama,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('balitas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('posyandu_id');
        });

        Schema::dropIfExists('posyandus');
    }
};