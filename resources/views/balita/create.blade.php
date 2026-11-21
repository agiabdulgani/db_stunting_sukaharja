<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tambah Data Balita - Kecamatan Singaparna</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">
    <div class="container mx-auto px-4 py-8 max-w-3xl">

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Form Input Balita</h1>
                <p class="text-gray-500 text-sm">Pendataan Stunting Kecamatan Singaparna</p>
            </div>
            <a href="{{ route('balita.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium text-sm bg-white px-3 py-2 border border-gray-200 rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>

        <!-- Ringkasan error -->
        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                Data belum tersimpan. Periksa kolom yang ditandai merah.
            </div>
        @endif

        <!-- Form Card: Data Balita -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <form action="{{ route('balita.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label for="nik_balita" class="block text-sm font-medium text-gray-700 mb-1">NIK Balita</label>
                        <input type="text" name="nik_balita" id="nik_balita" value="{{ old('nik_balita') }}"
                            required maxlength="16" minlength="16" inputmode="numeric" pattern="[0-9]{16}"
                            title="NIK harus 16 digit angka"
                            class="w-full px-4 py-2 border @error('nik_balita') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                            placeholder="Masukkan 16 digit NIK">
                        @error('nik_balita') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nama_balita" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Balita</label>
                        <input type="text" name="nama_balita" id="nama_balita" value="{{ old('nama_balita') }}" required
                            class="w-full px-4 py-2 border @error('nama_balita') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                            placeholder="Nama lengkap anak">
                        @error('nama_balita') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="jenis_kelamin" required
                            class="w-full px-4 py-2 border @error('jenis_kelamin') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm bg-white">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>
                        @error('jenis_kelamin') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                            required max="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2 border @error('tanggal_lahir') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                        @error('tanggal_lahir') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nama_orang_tua" class="block text-sm font-medium text-gray-700 mb-1">Nama Orang Tua / Wali</label>
                        <input type="text" name="nama_orang_tua" id="nama_orang_tua" value="{{ old('nama_orang_tua') }}" required
                            class="w-full px-4 py-2 border @error('nama_orang_tua') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                            placeholder="Nama Ayah / Ibu">
                        @error('nama_orang_tua') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="posyandu" class="block text-sm font-medium text-gray-700 mb-1">Nama Posyandu</label>
                        <input type="text" name="posyandu" id="posyandu" value="{{ old('posyandu') }}" required
                            class="w-full px-4 py-2 border @error('posyandu') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                            placeholder="Contoh: Posyandu Mawar 1">
                        @error('posyandu') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="rt_rw" class="block text-sm font-medium text-gray-700 mb-1">RT / RW</label>
                        <input type="text" name="rt_rw" id="rt_rw" value="{{ old('rt_rw') }}" required
                            class="w-full px-4 py-2 border @error('rt_rw') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                            placeholder="Contoh: 002/005">
                        @error('rt_rw') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="berat_badan" class="block text-sm font-medium text-gray-700 mb-1">Berat Badan (kg)</label>
                        <input type="number" step="0.1" min="1" max="30" name="berat_badan" id="berat_badan" value="{{ old('berat_badan') }}" required
                            class="w-full px-4 py-2 border @error('berat_badan') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                            placeholder="Contoh: 8.5">
                        @error('berat_badan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="tinggi_badan" class="block text-sm font-medium text-gray-700 mb-1">Tinggi / Panjang Badan (cm)</label>
                        <input type="number" step="0.1" min="30" max="130" name="tinggi_badan" id="tinggi_badan" value="{{ old('tinggi_badan') }}" required
                            class="w-full px-4 py-2 border @error('tinggi_badan') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm"
                            placeholder="Contoh: 72.0">
                        @error('tinggi_badan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('balita.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg text-sm transition duration-200">Batal</a>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow text-sm transition duration-200">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!--
        Contoh tombol pemicu modal (taruh di halaman index/show balita):
        <button type="button"
            onclick="openModal({{ $balita->id }}, '{{ addslashes($balita->nama_balita) }}', '{{ $balita->tanggal_lahir }}')"
            class="px-3 py-1.5 bg-green-600 text-white rounded text-sm">Timbang</button>
    -->

    <!-- Modal Form Penimbangan Bulanan -->
    <div id="modalPenimbangan" class="hidden fixed inset-0 z-50 bg-black/50 justify-center items-center p-4 overflow-y-auto"
         role="dialog" aria-modal="true" aria-labelledby="judulModal">
        <div class="bg-white rounded-xl p-6 w-full max-w-lg shadow-xl">
            <h3 id="judulModal" class="text-xl font-bold mb-1">Input Penimbangan Bulanan</h3>
            <p id="namaBalitaModal" class="text-sm text-gray-500 mb-4"></p>

            <form id="formPenimbangan" method="POST" action="">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="tanggal_timbang" class="text-sm font-medium">Tanggal Timbang</label>
                        <input type="date" id="tanggal_timbang" name="tanggal_timbang" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required
                            class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="umur_bulan" class="text-sm font-medium">Umur (Bulan)</label>
                            <input type="number" id="umur_bulan" name="umur_bulan" min="0" max="60" required readonly
                                class="w-full border border-gray-300 p-2 rounded bg-gray-50 text-sm">
                            <p class="text-xs text-gray-400 mt-1">Dihitung otomatis dari tanggal lahir.</p>
                        </div>
                        <div>
                            <label for="status_gizi" class="text-sm font-medium">Status Gizi (TB/U)</label>
                            <select id="status_gizi" name="status_gizi" required
                                class="w-full border border-gray-300 p-2 rounded bg-white text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="Normal">Normal</option>
                                <option value="Pendek">Pendek</option>
                                <option value="Sangat Pendek (Stunting)">Sangat Pendek (Stunting)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="pb_berat" class="text-sm font-medium">Berat Badan (kg)</label>
                            <input type="number" id="pb_berat" step="0.1" min="1" max="30" name="berat_badan" required
                                class="w-full border border-gray-300 p-2 rounded text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: 10.5">
                        </div>
                        <div>
                            <label for="pb_tinggi" class="text-sm font-medium">Tinggi Badan (cm)</label>
                            <input type="number" id="pb_tinggi" step="0.1" min="30" max="130" name="tinggi_badan" required
                                class="w-full border border-gray-300 p-2 rounded text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Contoh: 82.0">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">Simpan Penimbangan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modalPenimbangan');
        const form = document.getElementById('formPenimbangan');
        const tglTimbang = document.getElementById('tanggal_timbang');
        const umurInput = document.getElementById('umur_bulan');
        let tglLahirBalita = null;

        // Hitung umur dalam bulan penuh
        function hitungUmurBulan() {
            if (!tglLahirBalita || !tglTimbang.value) return;
            const lahir = new Date(tglLahirBalita);
            const timbang = new Date(tglTimbang.value);
            let bulan = (timbang.getFullYear() - lahir.getFullYear()) * 12 + (timbang.getMonth() - lahir.getMonth());
            if (timbang.getDate() < lahir.getDate()) bulan--;
            umurInput.value = Math.max(0, bulan);
        }

        function openModal(balitaId, nama, tanggalLahir) {
            // Sesuaikan nama route dengan routes/web.php Anda
            form.action = "{{ url('balita') }}/" + balitaId + "/penimbangan";
            document.getElementById('namaBalitaModal').textContent = nama || '';
            tglLahirBalita = tanggalLahir || null;
            hitungUmurBulan();
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('pb_berat').focus();
        }

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            form.reset();
        }

        tglTimbang.addEventListener('change', hitungUmurBulan);
        modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal(); });
    </script>
</body>
</html>