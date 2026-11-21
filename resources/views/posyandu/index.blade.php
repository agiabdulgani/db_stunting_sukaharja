<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Posyandu - Kecamatan Singaparna</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <div class="flex justify-between items-center mb-6 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Kelola Data Posyandu</h1>
                <p class="text-gray-500 text-sm">Daftar posyandu wilayah Kecamatan Singaparna</p>
            </div>
            <a href="{{ route('balita.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg transition">Kembali ke Data Balita</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-gray-600">Halaman manajemen posyandu siap dikonfigurasi.</p>
        </div>
    </div>
</body>
</html>