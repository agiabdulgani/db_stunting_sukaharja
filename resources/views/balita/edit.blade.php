<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Balita - Desa Sukaharja</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8 font-sans">
    <div class="container mx-auto px-4 max-w-2xl">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            
            <div class="flex items-center justify-between border-b pb-4 mb-6">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Edit Data Balita</h1>
                    <p class="text-xs text-gray-500">Perbarui informasi kesehatan balita Desa Sukaharja</p>
                </div>
                <a href="{{ route('balita.index') }}" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-3 rounded-lg font-medium transition">
                    Kembali
                </a>
            </div>

            <form action="{{ route('balita.update', $balita->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">NIK Balita</label>
                    <input type="text" name="nik_balita" value="{{ old('nik_balita', $balita->nik_balita) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    @error('nik_balita') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Balita</label>
                    <input type="text" name="nama_balita" value="{{ old('nama_balita', $balita->nama_balita) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    @error('nama_balita') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="L" {{ old('jenis_kelamin', $balita->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                            <option value="P" {{ old('jenis_kelamin', $balita->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Orang Tua</label>
                        <input type="text" name="nama_orang_tua" value="{{ old('nama_orang_tua', $balita->nama_orang_tua) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Posyandu</label>
                        <input type="text" name="posyandu" value="{{ old('posyandu', $balita->posyandu) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">RT / RW</label>
                        <input type="text" name="rt_rw" value="{{ old('rt_rw', $balita->rt_rw) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Berat Badan (kg)</label>
                        <input type="number" step="0.1" name="berat_badan" value="{{ old('berat_badan', $balita->berat_badan) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tinggi Badan (cm)</label>
                        <input type="number" step="0.1" name="tinggi_badan" value="{{ old('tinggi_badan', $balita->tinggi_badan) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status Gizi</label>
                    <select name="status_gizi" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="Normal" {{ old('status_gizi', $balita->status_gizi) == 'Normal' ? 'selected' : '' }}>Normal</option>
                        <option value="Stunting" {{ old('status_gizi', $balita->status_gizi) == 'Stunting' ? 'selected' : '' }}>Stunting</option>
                        <option value="Pendek" {{ old('status_gizi', $balita->status_gizi) == 'Pendek' ? 'selected' : '' }}>Pendek</option>
                        <option value="Gizi Kurang" {{ old('status_gizi', $balita->status_gizi) == 'Gizi Kurang' ? 'selected' : '' }}>Gizi Kurang</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-3 border-t">
                    <a href="{{ route('balita.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg text-sm hover:bg-gray-300 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg text-sm hover:bg-blue-700 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</body>
</html>