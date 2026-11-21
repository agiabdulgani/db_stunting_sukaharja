<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- 1. Header Informasi & Tombol Utama -->
            <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Sistem Pencatatan Stunting</h1>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                            Pemerintah Kecamatan Singaparna - Kelola data balita dan riwayat penimbangan bulanan
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('balita.create') ?? url('#') }}" class="flex-1 sm:flex-none text-center px-3.5 py-2 bg-blue-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            + Tambah Balita
                        </a>
                        <a href="{{ route('penimbangan.create') ?? url('#') }}" class="flex-1 sm:flex-none text-center px-3.5 py-2 bg-green-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-green-700 transition">
                            + Catat Penimbangan
                        </a>
                        <a href="#" class="px-3 py-2 border border-gray-300 text-gray-700 text-xs sm:text-sm rounded-lg hover:bg-gray-50 transition">
                            Export ▾
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Kartu Statistik (1 Kolom di HP, 2 di Tablet, 5 di Laptop) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-500 font-medium">Total Balita</p>
                    <h3 class="text-2xl font-bold text-gray-800 my-1">{{ $totalBalita ?? count($balitas ?? []) }}</h3>
                    <p class="text-xs text-gray-400">Terdaftar di sistem</p>
                </div>

                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-500 font-medium">Status Normal</p>
                    <h3 class="text-2xl font-bold text-green-600 my-1">{{ $totalNormal ?? 0 }}</h3>
                    <p class="text-xs text-green-600">Gizi baik / normal</p>
                </div>

                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-500 font-medium">Terindikasi Stunting</p>
                    <h3 class="text-2xl font-bold text-red-600 my-1">{{ $totalStunting ?? 0 }}</h3>
                    <p class="text-xs text-red-500">Perlu perhatian</p>
                </div>

                <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-500 font-medium">Posyandu Aktif</p>
                    <h3 class="text-2xl font-bold text-blue-600 my-1">{{ $posyanduAktif ?? 0 }}</h3>
                    <p class="text-xs text-blue-500">Wilayah terdata</p>
                </div>

                <div class="bg-white p-4 rounded-xl border border-amber-200 bg-amber-50/20 shadow-sm">
                    <p class="text-xs text-gray-500 font-medium">Belum Timbang Bulan Ini</p>
                    <h3 class="text-2xl font-bold text-amber-600 my-1">{{ $belumTimbang ?? 0 }}</h3>
                    <a href="#" class="text-xs text-amber-600 underline hover:text-amber-700">Lihat daftar sweeping</a>
                </div>
            </div>

            <!-- 3. Form Filter Pencarian (Stacking di HP, Grid di Desktop) -->
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Cari nama / NIK</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama atau NIK..." class="w-full text-xs border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Posyandu</label>
                        <select name="posyandu" class="w-full text-xs border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua posyandu</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Status gizi</label>
                        <select name="status" class="w-full text-xs border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua status</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600 block mb-1">Timbang bulan ini</label>
                        <select name="timbang" class="w-full text-xs border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500">
                            <option value="belum">Belum ditimbang</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white text-xs font-semibold p-2.5 rounded-lg hover:bg-blue-700 transition">
                            Terapkan filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- 4. Daftar Penimbangan Balita -->
            <div class="bg-white p-4 sm:p-6 rounded-xl border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-bold text-gray-800 text-base sm:text-lg">Daftar Penimbangan Balita</h2>
                    <span class="text-xs bg-blue-50 text-blue-600 font-medium px-2.5 py-1 rounded-full">
                        {{ count($balitas ?? []) }} balita ditampilkan
                    </span>
                </div>

                <!-- TAMPILAN HP / MOBILE (Card View) -->
                <div class="block md:hidden space-y-3">
                    @forelse($balitas ?? [] as $index => $balita)
                        <div class="border border-gray-200 p-4 rounded-xl bg-white space-y-2 shadow-sm">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">{{ $balita->nama ?? $balita->nama_balita }}</p>
                                    <p class="text-xs text-gray-400">NIK: {{ $balita->nik }}</p>
                                </div>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full 
                                    @if(($balita->status_gizi ?? '') == 'Sangat Pendek (Stunting)') bg-red-100 text-red-700
                                    @elseif(($balita->status_gizi ?? '') == 'Tinggi') bg-blue-100 text-blue-700
                                    @else bg-green-100 text-green-700 @endif">
                                    {{ $balita->status_gizi ?? 'Normal' }}
                                </span>
                            </div>

                            <div class="text-xs text-gray-600 space-y-1 py-1">
                                <p><span class="text-gray-400">Orang Tua:</span> {{ $balita->nama_orang_tua ?? $balita->orang_tua ?? '-' }}</p>
                                <p><span class="text-gray-400">Posyandu:</span> {{ $balita->posyandu ?? '-' }}</p>
                                <p><span class="text-gray-400">BB / TB:</span> <span class="font-semibold text-gray-800">{{ $balita->bb ?? '-' }} kg / {{ $balita->tb ?? '-' }} cm</span></p>
                            </div>

                            <div class="flex gap-2 pt-2 border-t border-gray-100">
                                <a href="{{ route('balita.show', $balita->id) }}" class="flex-1 text-center bg-blue-600 text-white text-xs py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                                    Detail
                                </a>
                                <a href="{{ route('penimbangan.create', ['balita_id' => $balita->id]) ?? url('#') }}" class="flex-1 text-center bg-green-600 text-white text-xs py-2 rounded-lg font-medium hover:bg-green-700 transition">
                                    Timbang
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-xs text-gray-500">
                            Belum ada data balita yang dapat ditampilkan.
                        </div>
                    @endforelse
                </div>

                <!-- TAMPILAN DESKTOP / LAPTOP (Table View) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="p-3">No</th>
                                <th class="p-3">NIK & Nama Balita</th>
                                <th class="p-3">L/P</th>
                                <th class="p-3">Orang Tua</th>
                                <th class="p-3">Posyandu / RT</th>
                                <th class="p-3">BB / TB</th>
                                <th class="p-3">Status Gizi</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($balitas ?? [] as $index => $balita)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-3 font-medium">{{ $index + 1 }}</td>
                                    <td class="p-3">
                                        <p class="font-bold text-gray-800">{{ $balita->nama ?? $balita->nama_balita }}</p>
                                        <p class="text-xs text-gray-400">NIK: {{ $balita->nik }}</p>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 text-xs font-bold rounded {{ ($balita->jenis_kelamin ?? 'L') == 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                            {{ $balita->jenis_kelamin ?? 'L' }}
                                        </span>
                                    </td>
                                    <td class="p-3">{{ $balita->nama_orang_tua ?? $balita->orang_tua ?? '-' }}</td>
                                    <td class="p-3">{{ $balita->posyandu ?? '-' }}</td>
                                    <td class="p-3 font-semibold text-gray-800">
                                        {{ $balita->bb ?? '-' }} kg / {{ $balita->tb ?? '-' }} cm
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-1 text-xs rounded-full font-semibold 
                                            @if(($balita->status_gizi ?? '') == 'Sangat Pendek (Stunting)') bg-red-100 text-red-700
                                            @elseif(($balita->status_gizi ?? '') == 'Tinggi') bg-blue-100 text-blue-700
                                            @else bg-green-100 text-green-700 @endif">
                                            {{ $balita->status_gizi ?? 'Normal' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('balita.show', $balita->id) }}" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition">
                                                Detail
                                            </a>
                                            <a href="{{ route('penimbangan.create', ['balita_id' => $balita->id]) ?? url('#') }}" class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition">
                                                Timbang
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-6 text-gray-500">
                                        Belum ada data balita yang dapat ditampilkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>