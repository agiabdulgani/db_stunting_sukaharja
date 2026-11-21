<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\Posyandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PosyanduController extends Controller
{
    public function index(Request $request)
    {
        $posyandus = Posyandu::withCount('balitas')
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('nama_posyandu', 'like', "%{$s}%")
                ->orWhere('desa', 'like', "%{$s}%")))
            ->orderBy('desa')
            ->orderBy('nama_posyandu')
            ->get();

        return view('posyandu.index', compact('posyandus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_posyandu' => ['required', 'string', 'max:255', 'unique:posyandus,nama_posyandu'],
            'desa'          => ['nullable', 'string', 'max:255'],
            'ketua_kader'   => ['nullable', 'string', 'max:255'],
        ]);

        Posyandu::create($data);

        return redirect()->route('posyandu.index')->with('success', 'Posyandu berhasil ditambahkan.');
    }

    public function update(Request $request, Posyandu $posyandu)
    {
        $data = $request->validate([
            'nama_posyandu' => ['required', 'string', 'max:255',
                                Rule::unique('posyandus', 'nama_posyandu')->ignore($posyandu->id)],
            'desa'          => ['nullable', 'string', 'max:255'],
            'ketua_kader'   => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($posyandu, $data) {
            $posyandu->update($data);

            // Samakan nama teks di data balita agar tabel, filter, dan export ikut berubah
            Balita::where('posyandu_id', $posyandu->id)->update(['posyandu' => $data['nama_posyandu']]);
        });

        return redirect()->route('posyandu.index')->with('success', 'Posyandu berhasil diperbarui.');
    }

    public function destroy(Posyandu $posyandu)
    {
        if ($posyandu->balitas()->exists()) {
            return back()->withErrors([
                'hapus' => "Posyandu \"{$posyandu->nama_posyandu}\" masih memiliki balita. Pindahkan balitanya dulu sebelum menghapus.",
            ]);
        }

        $posyandu->delete();

        return redirect()->route('posyandu.index')->with('success', 'Posyandu berhasil dihapus.');
    }
}