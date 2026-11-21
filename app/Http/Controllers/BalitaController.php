<?php

namespace App\Http\Controllers;

use App\Helpers\ZScoreHelper;
use App\Models\Balita;
use App\Models\Intervensi;
use App\Models\Penimbangan;
use App\Models\Posyandu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BalitaController extends Controller
{
    /* ---------------------------------------------------------------
     |  HELPER PRIVAT
     * ------------------------------------------------------------- */

    /**
     * Batasi query Balita berdasarkan status gizi TERBARU (penimbangan terakhir).
     * Balita yang belum pernah ditimbang memakai kolom balitas.status_gizi (jika ada).
     */
    private function filterStatusGizi($query, string $status)
    {
        $cocok = function ($q) use ($status) {
            match ($status) {
                'Normal'        => $q->where('status_gizi', 'Normal'),
                'Pendek'        => $q->where('status_gizi', 'like', '%Pendek%')
                                     ->where('status_gizi', 'not like', '%Sangat%'),
                'Sangat Pendek' => $q->where('status_gizi', 'like', '%Sangat Pendek%'),
                'Stunting'      => $q->where(fn ($w) => $w->where('status_gizi', 'like', '%Stunting%')
                                                         ->orWhere('status_gizi', 'like', '%Pendek%')),
                default         => $q->where('status_gizi', $status),
            };
        };

        $adaKolom = Schema::hasColumn('balitas', 'status_gizi');

        return $query->where(function ($q) use ($cocok, $adaKolom) {
            $q->whereHas('penimbanganTerakhir', $cocok);

            if ($adaKolom) {
                $q->orWhere(function ($b) use ($cocok) {
                    $b->doesntHave('penimbangans');
                    $cocok($b);
                });
            }
        });
    }

    /**
     * Form modal mengirim posyandu_id, sedangkan tabel/filter/export memakai nama posyandu.
     * Isi kolom `posyandu` dari posyandu_id agar semuanya konsisten.
     */
    private function normalisasiPosyandu(array $data): array
    {
        if (!empty($data['posyandu_id']) && ($p = Posyandu::find($data['posyandu_id']))) {
            $data['posyandu'] = $p->nama_posyandu;
        }

        if (!Schema::hasColumn('balitas', 'posyandu_id')) {
            unset($data['posyandu_id']);
        }

        return $data;
    }

    /**
     * Fitur PMT/intervensi dianggap tersedia jika model, relasi, dan tabelnya ada.
     */
    private function fiturIntervensiTersedia(): bool
    {
        return class_exists(Intervensi::class)
            && method_exists(Balita::class, 'intervensis')
            && Schema::hasTable('intervensis');
    }

    private function aturanBalita(?int $ignoreId = null): array
    {
        return [
            'nik'            => 'required|numeric|digits:16|unique:balitas,nik' . ($ignoreId ? ',' . $ignoreId : ''),
            'nama'           => 'required|string|max:255',
            'jenis_kelamin'  => 'required|in:L,P',
            'tanggal_lahir'  => 'required|date|before_or_equal:today',
            'nama_orang_tua' => 'required|string|max:255',
            'rt_rw'          => 'required|string|max:50',
            'posyandu'       => 'nullable|string|max:255',
            'posyandu_id'    => 'nullable|integer|exists:posyandus,id',
        ];
    }

    /* ---------------------------------------------------------------
     |  DAFTAR BALITA
     * ------------------------------------------------------------- */

    public function index(Request $request)
    {
        $search     = $request->input('search');
        $posyandu   = $request->input('posyandu');
        $statusGizi = $request->input('status_gizi');

        // --- Statistik (dihitung per balita, bukan per baris riwayat) ---
        $total_balita   = Balita::count();
        $total_normal   = $this->filterStatusGizi(Balita::query(), 'Normal')->count();
        $total_stunting = $this->filterStatusGizi(Balita::query(), 'Stunting')->count();

        // Sweeping posyandu: balita yang belum punya catatan timbang di bulan berjalan
        $awalBulan  = Carbon::now()->startOfMonth()->toDateString();
        $akhirBulan = Carbon::now()->endOfMonth()->toDateString();
        $bulanIni   = fn ($q) => $q->whereBetween('tanggal_timbang', [$awalBulan, $akhirBulan]);

        $total_belum_timbang = Balita::whereDoesntHave('penimbangans', $bulanIni)->count();

        $total_posyandu = Schema::hasTable('posyandus') && Posyandu::count() > 0
            ? Posyandu::count()
            : Balita::whereNotNull('posyandu')->distinct()->count('posyandu');

        // --- Daftar dengan filter ---
        $query = Balita::with('penimbanganTerakhir');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($posyandu) {
            $query->where('posyandu', $posyandu);
        }

        if ($statusGizi) {
            $this->filterStatusGizi($query, $statusGizi);
        }

        // Filter pengingat penimbangan bulan ini: ?timbang=belum | sudah
        $timbang = $request->input('timbang');
        if ($timbang === 'belum') {
            $query->whereDoesntHave('penimbangans', $bulanIni);
        } elseif ($timbang === 'sudah') {
            $query->whereHas('penimbangans', $bulanIni);
        }

        $balitas = $query->latest()->paginate(15)->withQueryString();

        $posyandus = Schema::hasTable('posyandus') ? Posyandu::orderBy('nama_posyandu')->get() : collect();

        // Opsi filter: utamakan master Posyandu (semua posyandu tampil walau belum punya balita);
        // jika master kosong, ambil nama unik dari data balita.
        $daftar_posyandu = $posyandus->isNotEmpty()
            ? $posyandus->pluck('nama_posyandu')
            : Balita::whereNotNull('posyandu')->distinct()->orderBy('posyandu')->pluck('posyandu');

        // Dropdown modal penimbangan: semua balita, bukan hanya halaman terfilter
        $semua_balita = Balita::orderBy('nama')->get(['id', 'nik', 'nama']);

        return view('balita.index', compact(
            'balitas',
            'semua_balita',
            'daftar_posyandu',
            'posyandus',
            'total_balita',
            'total_normal',
            'total_stunting',
            'total_belum_timbang',
            'total_posyandu'
        ));
    }

    /* ---------------------------------------------------------------
     |  CRUD BALITA (KHUSUS ADMIN)
     * ------------------------------------------------------------- */

    public function create()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Admin yang dapat menambah data balita.');
        }

        $posyandus = Schema::hasTable('posyandus') ? Posyandu::all() : collect();
        return view('balita.create', compact('posyandus'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Admin yang dapat menambah data balita.');
        }

        $validated = $this->normalisasiPosyandu($request->validate($this->aturanBalita()));

        Balita::create($validated);

        return redirect()->route('balita.index')->with('success', 'Data balita berhasil ditambahkan!');
    }

    public function show($id)
    {
        $relasi = [
            'penimbangans' => fn ($q) => $q->orderBy('tanggal_timbang', 'desc'),
        ];

        $adaIntervensi = $this->fiturIntervensiTersedia();
        if ($adaIntervensi) {
            $relasi['intervensis'] = fn ($q) => $q->orderBy('tanggal_pemberian', 'desc');
        }

        $balita = Balita::with($relasi)->findOrFail($id);

        return view('balita.show', compact('balita', 'adaIntervensi'));
    }

    public function edit($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Admin yang dapat mengedit data balita.');
        }

        $balita    = Balita::findOrFail($id);
        $posyandus = Schema::hasTable('posyandus') ? Posyandu::all() : collect();

        return view('balita.edit', compact('balita', 'posyandus'));
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Admin yang dapat memperbarui data balita.');
        }

        $balita    = Balita::findOrFail($id);
        $validated = $this->normalisasiPosyandu($request->validate($this->aturanBalita((int)$id)));

        $balita->update($validated);

        return redirect()->route('balita.index')->with('success', 'Data balita berhasil diperbarui!');
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Admin yang dapat menghapus data balita.');
        }

        DB::transaction(function () use ($id) {
            $balita = Balita::findOrFail($id);
            $balita->penimbangans()->delete();

            if ($this->fiturIntervensiTersedia()) {
                $balita->intervensis()->delete();
            }

            $balita->delete();
        });

        return redirect()->route('balita.index')->with('success', 'Data balita berhasil dihapus!');
    }

    /* ---------------------------------------------------------------
     |  PENIMBANGAN BULANAN (BISA DIAKSES ADMIN & USER/KADER)
     * ------------------------------------------------------------- */

    public function storePenimbangan(Request $request, $id)
    {
        $balita   = Balita::findOrFail($id);
        $tglLahir = Carbon::parse($balita->tanggal_lahir)->startOfDay();

        $request->validate([
            'tanggal_timbang' => [
                'required', 'date', 'before_or_equal:today',
                'after_or_equal:' . $tglLahir->toDateString(),
            ],
            'berat_badan'  => 'required|numeric|min:0.5|max:50',
            'tinggi_badan' => 'required|numeric|min:30|max:150',
        ]);

        $tglTimbang = Carbon::parse($request->tanggal_timbang)->startOfDay();
        $umurBulan  = (int) floor($tglLahir->diffInMonths($tglTimbang));

        if ($umurBulan > 60) {
            return back()->withInput()->withErrors([
                'tanggal_timbang' => 'Umur balita pada tanggal timbang melebihi 60 bulan (di luar rentang standar balita).',
            ]);
        }

        try {
            $hasilGizi = ZScoreHelper::hitungStunting($balita->jenis_kelamin, $umurBulan, (float)$request->tinggi_badan);
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'tinggi_badan' => 'Status gizi gagal dihitung. Periksa umur, jenis kelamin, dan tinggi badan.',
            ]);
        }

        DB::transaction(function () use ($balita, $request, $umurBulan, $hasilGizi) {
            Penimbangan::create([
                'balita_id'       => $balita->id,
                'tanggal_timbang' => $request->tanggal_timbang,
                'umur_bulan'      => $umurBulan,
                'berat_badan'     => $request->berat_badan,
                'tinggi_badan'    => $request->tinggi_badan,
                'zscore_tb_u'     => $hasilGizi['zscore_tb_u'],
                'status_gizi'     => $hasilGizi['status_gizi'],
            ]);

            if (Schema::hasColumn('balitas', 'status_gizi')) {
                $balita->update(['status_gizi' => $hasilGizi['status_gizi']]);
            }
        });

        return redirect()->back()->with(
            'success',
            'Data penimbangan berhasil disimpan! Status: ' . $hasilGizi['status_gizi']
        );
    }

    /* ---------------------------------------------------------------
     |  INTERVENSI / PMT
     * ------------------------------------------------------------- */

    public function storeIntervensi(Request $request, $id)
    {
        if (!$this->fiturIntervensiTersedia()) {
            return back()->withErrors([
                'jenis_pmt' => 'Fitur pencatatan PMT belum diaktifkan (tabel/model intervensi belum dibuat).',
            ]);
        }

        $balita = Balita::findOrFail($id);

        $request->validate([
            'tanggal_pemberian' => 'required|date|before_or_equal:today',
            'jenis_pmt'         => 'required|string|max:255',
            'keterangan'        => 'nullable|string|max:1000',
        ]);

        Intervensi::create([
            'balita_id'         => $balita->id,
            'tanggal_pemberian' => $request->tanggal_pemberian,
            'jenis_pmt'         => $request->jenis_pmt,
            'keterangan'        => $request->keterangan,
            'petugas'           => auth()->user()->name ?? 'Kader Posyandu',
        ]);

        return redirect()->back()->with('success', 'Pemberian Makanan Tambahan (PMT) berhasil dicatat!');
    }

    /* ---------------------------------------------------------------
     |  EXPORT
     * ------------------------------------------------------------- */

    public function exportExcel()
    {
        $filename = 'data_stunting_balita_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['NIK', 'Nama Balita', 'JK', 'Nama Orang Tua', 'Posyandu', 'BB (kg)', 'TB (cm)', 'Status Gizi']);

            Balita::with('penimbanganTerakhir')->orderBy('nama')->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $b) {
                    $t = $b->penimbanganTerakhir;

                    fputcsv($out, [
                        '="' . $b->nik . '"',
                        $b->nama,
                        $b->jenis_kelamin,
                        $b->nama_orang_tua ?? '-',
                        $b->posyandu ?? '-',
                        $t->berat_badan ?? '-',
                        $t->tinggi_badan ?? '-',
                        $t->status_gizi ?? $b->status_gizi ?? 'Belum ditimbang',
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportPdf()
    {
        $balitas = Balita::with('penimbanganTerakhir')->orderBy('nama', 'asc')->get();
        return view('balita.pdf', compact('balitas'));
    }
}