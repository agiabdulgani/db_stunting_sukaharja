@extends('layouts.app')

@section('title', 'Data Posyandu - Kecamatan Singaparna')

@php
    $inputClass = 'w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none';
@endphp

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8"
     x-data="{
        open: {{ $errors->has('nama_posyandu') || $errors->has('desa') || $errors->has('ketua_kader') ? 'true' : 'false' }},
        mode: '{{ old('_mode', 'tambah') }}',
        id: '{{ old('_id') }}',
        nama: {{ \Illuminate\Support\Js::from(old('nama_posyandu', '')) }},
        desa: {{ \Illuminate\Support\Js::from(old('desa', '')) }},
        ketua: {{ \Illuminate\Support\Js::from(old('ketua_kader', '')) }},
        urlUpdate: '{{ route('posyandu.update', ['posyandu' => '__ID__']) }}',
        get action() { return this.mode === 'edit' ? this.urlUpdate.replace('__ID__', this.id) : '{{ route('posyandu.store') }}'; },
        tambah() { this.mode = 'tambah'; this.id = ''; this.nama = ''; this.desa = ''; this.ketua = ''; this.open = true; },
        edit(p) { this.mode = 'edit'; this.id = p.id; this.nama = p.nama_posyandu; this.desa = p.desa ?? ''; this.ketua = p.ketua_kader ?? ''; this.open = true; }
     }"
     @keydown.escape.window="open = false">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Posyandu</h1>
            <p class="text-sm text-gray-500">Master posyandu Kecamatan Singaparna</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('balita.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg shadow-sm hover:bg-gray-50">&larr; Dashboard</a>
            <button type="button" @click="tambah()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow">+ Tambah Posyandu</button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->has('hapus'))
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r text-sm">{{ $errors->first('hapus') }}</div>
    @endif

    <!-- Pencarian -->
    <form method="GET" action="{{ route('posyandu.index') }}" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama posyandu atau desa..." class="{{ $inputClass }} sm:max-w-sm">
        <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg">Cari</button>
        @if(request('search'))
            <a href="{{ route('posyandu.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg">Reset</a>
        @endif
    </form>

    <!-- Tabel -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100 text-gray-600 text-xs">
                    <tr>
                        <th class="py-3 px-4 font-semibold">No</th>
                        <th class="py-3 px-4 font-semibold">Nama Posyandu</th>
                        <th class="py-3 px-4 font-semibold">Desa</th>
                        <th class="py-3 px-4 font-semibold">Ketua Kader</th>
                        <th class="py-3 px-4 font-semibold text-center">Jumlah Balita</th>
                        <th class="py-3 px-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($posyandus as $i => $p)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">{{ $i + 1 }}</td>
                            <td class="py-3 px-4 font-semibold text-gray-900">{{ $p->nama_posyandu }}</td>
                            <td class="py-3 px-4">{{ $p->desa ?? '-' }}</td>
                            <td class="py-3 px-4">{{ $p->ketua_kader ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('balita.index', ['posyandu' => $p->nama_posyandu]) }}" class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold hover:bg-blue-100">{{ $p->balitas_count }} balita</a>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" @click="edit({{ \Illuminate\Support\Js::from($p->only(['id', 'nama_posyandu', 'desa', 'ketua_kader'])) }})" class="px-2.5 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded">Edit</button>
                                    <form action="{{ route('posyandu.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus posyandu {{ addslashes($p->nama_posyandu) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-8 text-gray-400">Belum ada data posyandu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal tambah / edit -->
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6 overflow-y-auto"
         @click.self="open = false" role="dialog" aria-modal="true">
        <div class="bg-white rounded-xl shadow-lg max-w-lg w-full p-6">
            <div class="flex justify-between items-center pb-3 border-b mb-4">
                <h2 class="font-bold text-gray-800 text-lg" x-text="mode === 'edit' ? 'Edit Posyandu' : 'Tambah Posyandu'"></h2>
                <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none" aria-label="Tutup">&times;</button>
            </div>

            <form method="POST" :action="action" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" :value="mode === 'edit' ? 'PUT' : 'POST'">
                <input type="hidden" name="_mode" :value="mode">
                <input type="hidden" name="_id" :value="id">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama posyandu</label>
                    <input type="text" name="nama_posyandu" x-model="nama" class="{{ $inputClass }}" placeholder="Contoh: Posyandu Mawar 1" required>
                    @error('nama_posyandu') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Desa / kelurahan</label>
                    <input type="text" name="desa" x-model="desa" class="{{ $inputClass }}" placeholder="Nama desa">
                    @error('desa') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Ketua kader</label>
                    <input type="text" name="ketua_kader" x-model="ketua" class="{{ $inputClass }}" placeholder="Nama ketua kader">
                    @error('ketua_kader') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="open = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection