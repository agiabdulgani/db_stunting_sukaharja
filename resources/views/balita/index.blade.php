<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Balita - Kecamatan Singaparna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important;}</style>
</head>

@php
    // Memastikan admin terdeteksi berdasarkan role 'admin' ATAU email utama Anda
    $userLogin = auth()->user();
    $isAdmin = $userLogin && ($userLogin->role === 'admin' || $userLogin->email === 'adminagi@sttcipasung.ac.id');
    $bisaInput = auth()->check();

    $pilihanBalita = $semua_balita ?? ($balitas ?? []);
    $mulai = (isset($balitas) && method_exists($balitas, 'firstItem')) ? ($balitas->firstItem() ?? 1) : 1;
    $itemsPilihan = $pilihanBalita instanceof \Illuminate\Pagination\AbstractPaginator
        ? $pilihanBalita->getCollection()
        : collect($pilihanBalita);
    $petaBalita = $itemsPilihan->mapWithKeys(fn ($b) => [$b->id => $b->nama_balita ?? $b->nama])->all();
@endphp

<body class="bg-gray-100 min-h-screen font-sans"
      x-data="{
          modalTambah: {{ (old('nik') || old('nama')) ? 'true' : 'false' }},
          modalPenimbangan: {{ old('balita_id') ? 'true' : 'false' }},
          dropdownExport: false,
          balitaId: '{{ old('balita_id') }}',
          penimbanganUrl: '{{ route('penimbangan.store', ['balita' => '__ID__']) }}',
          get actionPenimbangan() { return this.balitaId ? this.penimbanganUrl.replace('__ID__', this.balitaId) : '#'; },
          daftarBalita: {{ \Illuminate\Support\Js::from($petaBalita) }},
          get namaBalita() { return this.daftarBalita[this.balitaId] ?? ''; },
          bukaPenimbangan(id = '') { this.balitaId = id; this.modalPenimbangan = true; }
      }"
      @keydown.escape.window="modalTambah = false; modalPenimbangan = false">

    <div class="container mx-auto px-4 py-8 max-w-7xl">

        <!-- Header & Navigasi User -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Sistem Pencatatan Stunting</h1>
                <p class="text-gray-500 text-sm">Pemerintah Kecamatan Singaparna - Kelola data balita dan riwayat penimbangan bulanan</p>
            </div>

            <div class="mt-4 md:mt-0 flex flex-wrap items-center gap-3">
                <div class="text-right mr-2 hidden sm:block">
                    <div class="text-sm font-semibold text-gray-800">{{ $userLogin->name ?? 'Admin' }}</div>
                    <div class="text-xs text-gray-500">{{ $userLogin->email ?? '' }}</div>
                </div>

                @if($isAdmin)
                    <button type="button" @click="modalTambah = true" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow transition duration-200 text-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Balita
                    </button>
                @endif

                @if($bisaInput)
                    <button type="button" @click="bukaPenimbangan()" class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow transition duration-200 text-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Catat Penimbangan
                    </button>
                @endif

                @if($isAdmin)
                    <a href="{{ route('posyandu.index') }}" class="inline-flex items-center justify-center bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-3 rounded-lg border border-gray-300 transition duration-200 text-sm">Kelola Posyandu</a>
                @endif

                <!-- Dropdown Export -->
                <div class="relative" @click.outside="dropdownExport = false">
                    <button type="button" @click="dropdownExport = !dropdownExport" class="inline-flex items-center justify-center bg-white hover:bg-gray-50 text-gray-700 font-medium py-2 px-3 rounded-lg border border-gray-300 transition duration-200 text-sm">
                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Export
                        <svg class="w-4 h-4 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="dropdownExport" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-40">
                        <a href="{{ route('balita.export.excel') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">📊 Export Excel (.csv)</a>
                        <a href="{{ route('balita.export.pdf') }}" target="_blank" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">📄 Cetak / Simpan PDF</a>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 font-medium py-2 px-3 rounded-lg border border-red-200 transition duration-200 text-sm" title="Keluar">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Statistik Ringkas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <p class="text-xs font-semibold text-gray-500">Total Balita</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $total_balita ?? 0 }}</h3>
                <p class="text-xs text-gray-400 mt-1">Terdaftar di sistem</p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <p class="text-xs font-semibold text-gray-500">Status Normal</p>
                <h3 class="text-2xl font-bold text-green-600 mt-1">{{ $total_normal ?? 0 }}</h3>
                <p class="text-xs text-green-500 mt-1">Gizi baik / normal</p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <p class="text-xs font-semibold text-gray-500">Terindikasi Stunting</p>
                <h3 class="text-2xl font-bold text-red-600 mt-1">{{ $total_stunting ?? 0 }}</h3>
                <p class="text-xs text-red-500 mt-1">Perlu perhatian</p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <p class="text-xs font-semibold text-gray-500">Posyandu Aktif</p>
                <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ $total_posyandu ?? 0 }}</h3>
                <p class="text-xs text-indigo-400 mt-1">Wilayah terdata</p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-amber-200">
                <p class="text-xs font-semibold text-gray-500">Belum Timbang Bulan Ini</p>
                <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $total_belum_timbang ?? 0 }}</h3>
                <a href="{{ route('balita.index', ['timbang' => 'belum']) }}" class="text-xs text-amber-700 underline mt-1 inline-block">Lihat daftar sweeping</a>
            </div>
        </div>

        <!-- Alert Sukses -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Alert Error Validasi -->
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

        <!-- Filter & Pencarian -->
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6">
            <form method="GET" action="{{ route('balita.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label for="search" class="block text-xs font-semibold text-gray-600 mb-1.5">Cari nama / NIK</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ketik nama atau NIK..." class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>

                <div>
                    <label for="posyandu" class="block text-xs font-semibold text-gray-600 mb-1.5">Posyandu</label>
                    <select name="posyandu" id="posyandu" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">Semua posyandu</option>
                        @foreach($daftar_posyandu ?? [] as $p)
                            <option value="{{ $p }}" {{ request('posyandu') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status_gizi" class="block text-xs font-semibold text-gray-600 mb-1.5">Status gizi</label>
                    <select name="status_gizi" id="status_gizi" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">Semua status</option>
                        @foreach(['Normal', 'Pendek', 'Sangat Pendek', 'Stunting'] as $s)
                            <option value="{{ $s }}" {{ request('status_gizi') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="timbang" class="block text-xs font-semibold text-gray-600 mb-1.5">Timbang bulan ini</label>
                    <select name="timbang" id="timbang" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">Semua</option>
                        <option value="belum" {{ request('timbang') == 'belum' ? 'selected' : '' }}>Belum ditimbang</option>
                        <option value="sudah" {{ request('timbang') == 'sudah' ? 'selected' : '' }}>Sudah ditimbang</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg text-sm transition duration-200">Terapkan filter</button>
                    @if(request('search') || request('posyandu') || request('status_gizi') || request('timbang'))
                        <a href="{{ route('balita.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium py-2 px-3 rounded-lg text-sm transition duration-200">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h2 class="font-semibold text-gray-700">Daftar Penimbangan Balita</h2>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold bg-blue-100 text-blue-800 py-1 px-3 rounded-full">@if(isset($balitas) && method_exists($balitas, 'total') && $balitas->total() > 0){{ $balitas->firstItem() }}–{{ $balitas->lastItem() }} dari {{ $balitas->total() }} balita @else 0 balita @endif</span>
                    @if($isAdmin)
                        <button type="button" @click="modalTambah = true" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition">+ Tambah Balita</button>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 text-xs">
                            <th class="py-3 px-4 font-semibold">No</th>
                            <th class="py-3 px-4 font-semibold">NIK & Nama Balita</th>
                            <th class="py-3 px-4 font-semibold">L/P</th>
                            <th class="py-3 px-4 font-semibold">Orang Tua</th>
                            <th class="py-3 px-4 font-semibold">Posyandu / RT</th>
                            <th class="py-3 px-4 font-semibold">BB / TB</th>
                            <th class="py-3 px-4 font-semibold">Status Gizi</th>
                            <th class="py-3 px-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @forelse($balitas ?? [] as $index => $item)
                            @php
                                $status = $item->penimbanganTerakhir->status_gizi ?? $item->status_gizi ?? 'Belum ditimbang';
                                $statusLower = strtolower($status);
                                $isMerah = str_contains($statusLower, 'stunting') || str_contains($statusLower, 'sangat pendek');
                                $isKuning = !$isMerah && str_contains($statusLower, 'pendek');
                                $tglTerakhir = optional($item->penimbanganTerakhir)->tanggal_timbang;
                                $sudahBulanIni = $tglTerakhir && \Carbon\Carbon::parse($tglTerakhir)->format('Y-m') === now()->format('Y-m');
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-4 font-medium">{{ $mulai + $index }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-gray-900">{{ $item->nama_balita ?? $item->nama }}</div>
                                    <div class="text-xs text-gray-400">NIK: {{ $item->nik_balita ?? $item->nik }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 text-xs rounded font-semibold {{ $item->jenis_kelamin == 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">{{ $item->jenis_kelamin }}</span>
                                </td>
                                <td class="py-3 px-4">{{ $item->nama_orang_tua }}</td>
                                <td class="py-3 px-4">
                                    <div>{{ is_object($item->posyandu) ? $item->posyandu->nama_posyandu : ($item->posyandu ?? '-') }}</div>
                                    <div class="text-xs text-gray-400">RT/RW: {{ $item->rt_rw }}</div>
                                </td>
                                <td class="py-3 px-4 font-medium">
                                    {{ $item->penimbanganTerakhir->berat_badan ?? $item->berat_badan ?? '-' }} kg /
                                    {{ $item->penimbanganTerakhir->tinggi_badan ?? $item->tinggi_badan ?? '-' }} cm
                                    @if(optional($item->penimbanganTerakhir)->tanggal_timbang)
                                        <div class="text-xs text-gray-400 font-normal">Timbang: {{ \Carbon\Carbon::parse($item->penimbanganTerakhir->tanggal_timbang)->format('d/m/Y') }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $isMerah ? 'bg-red-100 text-red-700' : ($isKuning ? 'bg-yellow-100 text-yellow-800' : ($status === 'Belum ditimbang' ? 'bg-gray-100 text-gray-600' : 'bg-green-100 text-green-700')) }}">
                                        {{ $status }}</span>
                                    <span class="block mt-1.5 text-[11px] font-semibold {{ $sudahBulanIni ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $sudahBulanIni ? '✔ Sudah ditimbang bulan ini' : '⚠ Belum timbang bulan ini' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Tombol Detail -->
                                        <a href="{{ route('balita.show', $item->id) }}" class="px-2.5 py-1.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-medium rounded transition" title="Detail & riwayat">Detail</a>
                                        
                                        <!-- Tombol Timbang -->
                                        <button type="button" @click="bukaPenimbangan('{{ $item->id }}')" class="px-2.5 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded transition" title="Catat penimbangan">Timbang</button>

                                        <!-- TOMBOL EDIT & HAPUS HANYA MUNCUL JIKA USER ADALAH ADMIN -->
                                        @if($isAdmin)
                                            <!-- Tombol Edit -->
                                            <a href="{{ route('balita.edit', $item->id) }}" class="px-2.5 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition" title="Edit">Edit</a>
                                            
                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('balita.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data {{ addslashes($item->nama_balita ?? $item->nama) }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition" title="Hapus">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8 text-gray-400">Data balita tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($balitas) && method_exists($balitas, 'links'))
                <div class="px-6 py-4 border-t border-gray-100">{{ $balitas->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>

    @if($bisaInput)
    <!-- MODAL 1: TAMBAH BALITA -->
    <div x-show="modalTambah" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6 overflow-y-auto"
         @click.self="modalTambah = false" role="dialog" aria-modal="true" aria-labelledby="judulTambah">
        <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full p-6">
            <div class="flex justify-between items-center pb-3 border-b mb-4">
                <h5 id="judulTambah" class="font-bold text-gray-800 text-lg">Tambah Data Balita Baru</h5>
                <button type="button" @click="modalTambah = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none" aria-label="Tutup">×</button>
            </div>
            <form action="{{ route('balita.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">NIK Balita</label>
                        <input type="text" name="nik" value="{{ old('nik') }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="16 digit NIK" required maxlength="16" minlength="16" inputmode="numeric" pattern="[0-9]{16}" title="NIK harus 16 digit angka">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap Balita</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Nama balita" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                            <option value="">Pilih jenis kelamin</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" max="{{ date('Y-m-d') }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Orang Tua / Wali</label>
                        <input type="text" name="nama_orang_tua" value="{{ old('nama_orang_tua') }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Nama ibu / ayah" required>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">RT / RW</label>
                            <input type="text" name="rt_rw" value="{{ old('rt_rw') }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="001/002" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Posyandu</label>
                            @if(count($posyandus ?? []) > 0)
                            <select name="posyandu_id" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none bg-white" required>
                                <option value="">Pilih</option>
                                @foreach($posyandus ?? [] as $p)
                                    <option value="{{ $p->id }}" {{ old('posyandu_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_posyandu }}</option>
                                @endforeach
                            </select>
                            @else
                            <input type="text" name="posyandu" value="{{ old('posyandu') }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Nama posyandu" required>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="modalTambah = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">Simpan data balita</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: CATAT PENIMBANGAN BULANAN -->
    <div x-show="modalPenimbangan" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6 overflow-y-auto"
         @click.self="modalPenimbangan = false" role="dialog" aria-modal="true" aria-labelledby="judulTimbang">
        <div class="bg-white rounded-xl shadow-lg max-w-lg w-full p-6">
            <div class="flex justify-between items-center pb-3 border-b mb-4">
                <h5 id="judulTimbang" class="font-bold text-gray-800 text-lg">Catat Penimbangan <span x-show="namaBalita" x-cloak><span class="text-green-700" x-text="namaBalita"></span></span></h5>
                <button type="button" @click="modalPenimbangan = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none" aria-label="Tutup">×</button>
            </div>

            <form method="POST" :action="actionPenimbangan">
                @csrf
                <div class="space-y-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih balita</label>
                        <select name="balita_id" x-model="balitaId" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 outline-none bg-white" required>
                            <option value="">Pilih balita (NIK - nama)</option>
                            @foreach($pilihanBalita as $b)
                                <option value="{{ $b->id }}">{{ $b->nik_balita ?? $b->nik }} - {{ $b->nama_balita ?? $b->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal timbang</label>
                            <input type="date" name="tanggal_timbang" value="{{ old('tanggal_timbang', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 outline-none" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Berat badan (kg)</label>
                            <input type="number" step="0.01" min="1" max="30" name="berat_badan" value="{{ old('berat_badan') }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 outline-none" placeholder="12.5" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tinggi / panjang badan (cm)</label>
                        <input type="number" step="0.1" min="30" max="130" name="tinggi_badan" value="{{ old('tinggi_badan') }}" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 outline-none" placeholder="85.0" required>
                    </div>
                    <p class="text-xs text-gray-400">Umur dan status gizi dihitung otomatis saat data disimpan.</p>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="modalPenimbangan = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg">Batal</button>
                    <button type="submit" :disabled="!balitaId" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">Simpan & hitung status gizi</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</body>
</html>