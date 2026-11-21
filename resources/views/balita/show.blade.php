@extends('layouts.app')

@section('title', 'Detail Balita - ' . ($balita->nama_balita ?? $balita->nama))

@php
    $nama = $balita->nama_balita ?? $balita->nama;
    $nik = $balita->nik_balita ?? $balita->nik;
    $namaPosyandu = is_object($balita->posyandu) ? $balita->posyandu->nama_posyandu : ($balita->posyandu ?? '-');

    $tglLahir = \Carbon\Carbon::parse($balita->tanggal_lahir)->locale('id');
    $umurBulan = (int) floor($tglLahir->diffInMonths(now()));

    // Riwayat dari controller: terbaru di atas. Untuk grafik dibalik jadi kronologis.
    $riwayat = $balita->penimbangans;
    $kronologis = $riwayat->sortBy('tanggal_timbang')->values();
    $labels = $kronologis->map(fn ($p) => \Carbon\Carbon::parse($p->tanggal_timbang)->format('d/m/Y'))->all();
    $dataTB = $kronologis->map(fn ($p) => (float) $p->tinggi_badan)->all();
    $dataBB = $kronologis->map(fn ($p) => (float) $p->berat_badan)->all();

    $terakhir = $riwayat->first();
    $statusTerakhir = $terakhir->status_gizi ?? 'Belum ditimbang';

    // Warna badge status gizi: merah = stunting/sangat pendek, kuning = pendek, hijau = normal
    $warnaStatus = function (?string $s) {
        $s = strtolower($s ?? '');
        if (str_contains($s, 'stunting') || str_contains($s, 'sangat pendek')) return 'bg-red-100 text-red-700';
        if (str_contains($s, 'pendek')) return 'bg-yellow-100 text-yellow-800';
        if ($s === '' || str_contains($s, 'belum')) return 'bg-gray-100 text-gray-600';
        return 'bg-green-100 text-green-700';
    };

    $inputClass = 'w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none';
    $adaIntervensi = $adaIntervensi ?? false;
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Balita: {{ $nama }}</h1>
            <p class="text-sm text-gray-500 mt-1">NIK: {{ $nik }} &middot; Posyandu: {{ $namaPosyandu }}</p>
        </div>
        <a href="{{ route('balita.index') }}" class="inline-flex items-center justify-center text-gray-600 hover:text-gray-900 font-medium text-sm bg-white px-4 py-2 border border-gray-200 rounded-lg shadow-sm transition">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r shadow-sm text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r shadow-sm text-sm">
            <p class="font-semibold mb-1">Data belum tersimpan:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom kiri: profil, penimbangan, PMT -->
        <div class="space-y-6">

            <!-- Profil -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-blue-600 text-white font-semibold px-5 py-3">Profil &amp; Identitas</div>
                <dl class="divide-y divide-gray-100 text-sm">
                    <div class="flex justify-between px-5 py-3"><dt class="text-gray-500">Jenis kelamin</dt><dd class="font-semibold text-gray-800">{{ $balita->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</dd></div>
                    <div class="flex justify-between px-5 py-3"><dt class="text-gray-500">Tanggal lahir</dt><dd class="font-semibold text-gray-800">{{ $tglLahir->translatedFormat('d F Y') }}</dd></div>
                    <div class="flex justify-between px-5 py-3"><dt class="text-gray-500">Umur sekarang</dt><dd class="font-semibold text-gray-800">{{ $umurBulan }} bulan</dd></div>
                    <div class="flex justify-between px-5 py-3"><dt class="text-gray-500">Orang tua / wali</dt><dd class="font-semibold text-gray-800">{{ $balita->nama_orang_tua }}</dd></div>
                    <div class="flex justify-between px-5 py-3"><dt class="text-gray-500">RT / RW</dt><dd class="font-semibold text-gray-800">{{ $balita->rt_rw }}</dd></div>
                    <div class="flex justify-between items-center px-5 py-3">
                        <dt class="text-gray-500">Status gizi terakhir</dt>
                        <dd><span class="px-3 py-1 rounded-full text-xs font-semibold {{ $warnaStatus($statusTerakhir) }}">{{ $statusTerakhir }}</span></dd>
                    </div>
                </dl>
            </div>

            <!-- Input penimbangan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-green-600 text-white font-semibold px-5 py-3">Catat Penimbangan</div>
                <form action="{{ route('penimbangan.store', $balita->id) }}" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal timbang</label>
                        <input type="date" name="tanggal_timbang" value="{{ old('tanggal_timbang', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="{{ $inputClass }}" required>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Berat (kg)</label>
                            <input type="number" step="0.01" min="1" max="30" name="berat_badan" value="{{ old('berat_badan') }}" class="{{ $inputClass }}" placeholder="12.5" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tinggi (cm)</label>
                            <input type="number" step="0.1" min="30" max="130" name="tinggi_badan" value="{{ old('tinggi_badan') }}" class="{{ $inputClass }}" placeholder="85.0" required>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400">Umur dan status gizi dihitung otomatis.</p>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 rounded-lg transition">Simpan &amp; hitung status gizi</button>
                </form>
            </div>

            <!-- Intervensi PMT -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-amber-500 text-white font-semibold px-5 py-3">Intervensi PMT</div>

                @if($adaIntervensi)
                    <form action="{{ route('balita.intervensi.store', $balita->id) }}" method="POST" class="p-5 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal pemberian</label>
                            <input type="date" name="tanggal_pemberian" value="{{ old('tanggal_pemberian', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="{{ $inputClass }}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis bantuan / PMT</label>
                            <input type="text" name="jenis_pmt" value="{{ old('jenis_pmt') }}" class="{{ $inputClass }}" placeholder="Contoh: Telur 1 kg, susu, paket PMT lokal" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan / evaluasi</label>
                            <textarea name="keterangan" rows="2" class="{{ $inputClass }}" placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
                        </div>
                        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium py-2 rounded-lg transition">+ Simpan intervensi</button>
                    </form>

                    <div class="border-t border-gray-100 px-5 py-4">
                        <h3 class="text-xs font-semibold text-gray-500 mb-2">Riwayat PMT</h3>
                        @forelse($balita->intervensis as $i)
                            <div class="py-2 border-b border-gray-50 last:border-0 text-sm">
                                <div class="font-semibold text-gray-800">{{ $i->jenis_pmt }}</div>
                                <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($i->tanggal_pemberian)->format('d/m/Y') }} &middot; {{ $i->petugas ?? '-' }}</div>
                                @if($i->keterangan)
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $i->keterangan }}</div>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-400">Belum ada pemberian PMT.</p>
                        @endforelse
                    </div>
                @else
                    <p class="p-5 text-sm text-gray-500">Fitur pencatatan PMT belum diaktifkan (tabel intervensi belum dibuat).</p>
                @endif
            </div>
        </div>

        <!-- Kolom kanan: grafik & riwayat -->
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">Grafik Pertumbuhan (Tinggi &amp; Berat Badan)</div>
                <div class="p-5">
                    @if($kronologis->count() > 0)
                        <div class="relative h-72"><canvas id="grafikTumbuh"></canvas></div>
                    @else
                        <p class="text-sm text-gray-400 text-center py-10">Grafik muncul setelah ada penimbangan pertama.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">Riwayat Penimbangan Bulanan</div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-100 text-gray-600 text-xs">
                            <tr>
                                <th class="py-3 px-4 font-semibold">Tanggal</th>
                                <th class="py-3 px-4 font-semibold">Umur</th>
                                <th class="py-3 px-4 font-semibold">Berat</th>
                                <th class="py-3 px-4 font-semibold">Tinggi</th>
                                <th class="py-3 px-4 font-semibold">Z-Score TB/U</th>
                                <th class="py-3 px-4 font-semibold">Status Gizi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($riwayat as $p)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($p->tanggal_timbang)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4">{{ $p->umur_bulan ?? '-' }} bln</td>
                                    <td class="py-3 px-4">{{ number_format((float) $p->berat_badan, 2) }} kg</td>
                                    <td class="py-3 px-4">{{ number_format((float) $p->tinggi_badan, 1) }} cm</td>
                                    <td class="py-3 px-4">
                                        @if(!is_null($p->zscore_tb_u))
                                            <span class="px-2 py-0.5 rounded border border-gray-200 bg-gray-50 text-xs">{{ number_format((float) $p->zscore_tb_u, 2) }} SD</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $warnaStatus($p->status_gizi) }}">{{ $p->status_gizi }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-8 text-gray-400">Belum ada riwayat penimbangan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($kronologis->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Chart(document.getElementById('grafikTumbuh'), {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [
                {
                    label: 'Tinggi badan (cm)',
                    data: @json($dataTB),
                    yAxisID: 'yTB',
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2, fill: true, tension: 0.3, pointRadius: 4
                },
                {
                    label: 'Berat badan (kg)',
                    data: @json($dataBB),
                    yAxisID: 'yBB',
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.1)',
                    borderWidth: 2, fill: true, tension: 0.3, pointRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top' } },
            scales: {
                // Dua sumbu: cm dan kg punya skala berbeda, jadi tidak dipaksa satu sumbu
                yTB: { type: 'linear', position: 'left',  title: { display: true, text: 'cm' } },
                yBB: { type: 'linear', position: 'right', title: { display: true, text: 'kg' }, grid: { drawOnChartArea: false } }
            }
        }
    });
});
</script>
@endif
@endpush