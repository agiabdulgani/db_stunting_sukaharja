<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Balita;
use App\Models\Penimbangan;
use App\Services\WhoGrowthService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PenimbanganController extends Controller
{
    protected $whoGrowthService;

    public function __construct(WhoGrowthService $whoGrowthService)
    {
        $this->whoGrowthService = $whoGrowthService;
    }

    /**
     * Menyimpan transaksi penimbangan bulanan balita dan mengalkulasi Z-Score WHO.
     */
    public function store(Request $request)
    {
        $request->validate([
            'balita_id' => 'required|exists:balitas,id',
            'tanggal_timbang' => 'required|date',
            'berat_badan' => 'required|numeric|min:0.5|max:50',
            'tinggi_badan' => 'required|numeric|min:30|max:150',
        ]);

        $balita = Balita::findOrFail($request->balita_id);

        
        $tglLahir = Carbon::parse($balita->tanggal_lahir);
        $tglTimbang = Carbon::parse($request->tanggal_timbang);
        $umurBulan = (int) $tglLahir->diffInMonths($tglTimbang);

    
        $zscoreResult = $this->whoGrowthService->calculateZScore(
            $balita->jenis_kelamin,
            $umurBulan,
            (float) $request->berat_badan,
            (float) $request->tinggi_badan
        );

        DB::transaction(function () use ($balita, $request, $umurBulan, $zscoreResult) {
            
            Penimbangan::create([
                'balita_id' => $balita->id,
                'tanggal_timbang' => $request->tanggal_timbang,
                'umur_bulan' => $umurBulan,
                'berat_badan' => $request->berat_badan,
                'tinggi_badan' => $request->tinggi_badan,
                'zscore_tb_u' => $zscoreResult['zscore_tb_u'],
                'status_gizi' => $zscoreResult['status_gizi'],
            ]);

            // 2. Sinkronkan status gizi terbaru ke tabel master balitas
            if (Schema::hasColumn('balitas', 'status_gizi')) {
                $balita->update([
                    'status_gizi' => $zscoreResult['status_gizi'],
                ]);
            }
        });

        return redirect()->back()->with('success', 'Catatan penimbangan & Z-score berhasil dihitung!');
    }
}