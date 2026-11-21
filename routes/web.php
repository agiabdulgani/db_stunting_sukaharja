<?php

use App\Http\Controllers\BalitaController;
use App\Http\Controllers\PosyanduController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Pencatatan Stunting Kecamatan Singaparna
|--------------------------------------------------------------------------
| Catatan: Route::redirect dipakai (bukan closure) agar aman untuk
| `php artisan route:cache`.
*/

// Halaman utama & dashboard -> daftar balita
// (tamu otomatis diarahkan ke halaman login oleh middleware auth)
Route::redirect('/', '/balita');

Route::middleware(['auth'])->group(function () {

    Route::redirect('/dashboard', '/balita')->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Data Balita - semua user login (admin & kader)
    |----------------------------------------------------------------------
    | Urutan penting: route statis (create, export) HARUS di atas /balita/{balita}.
    */
    Route::get('/balita', [BalitaController::class, 'index'])->name('balita.index');
    Route::get('/balita/create', [BalitaController::class, 'create'])->name('balita.create');
    Route::get('/balita/export/excel', [BalitaController::class, 'exportExcel'])->name('balita.export.excel');
    Route::get('/balita/export/pdf', [BalitaController::class, 'exportPdf'])->name('balita.export.pdf');
    Route::post('/balita', [BalitaController::class, 'store'])->name('balita.store');
    Route::get('/balita/{balita}', [BalitaController::class, 'show'])->whereNumber('balita')->name('balita.show');

    // Input penimbangan bulanan & pencatatan PMT (kader boleh)
    Route::post('/balita/{balita}/penimbangan', [BalitaController::class, 'storePenimbangan'])
        ->whereNumber('balita')->name('penimbangan.store');

    Route::post('/balita/{balita}/intervensi', [BalitaController::class, 'storeIntervensi'])
        ->whereNumber('balita')->name('balita.intervensi.store');

    /*
    |----------------------------------------------------------------------
    | Ubah & hapus data balita serta Master Posyandu - KHUSUS ADMIN
    |----------------------------------------------------------------------
    | Wajib mendefinisikan Gate 'admin' di AppServiceProvider::boot():
    |     Gate::define('admin', fn ($user) => $user->role === 'admin' || $user->email === 'adminagi@sttcipasung.ac.id');
    | Tanpa Gate itu, route di bawah menolak SEMUA user (403).
    */
    Route::middleware('can:admin')->group(function () {
        // Kelola Balita (Admin)
        Route::get('/balita/{balita}/edit', [BalitaController::class, 'edit'])->whereNumber('balita')->name('balita.edit');
        Route::put('/balita/{balita}', [BalitaController::class, 'update'])->whereNumber('balita')->name('balita.update');
        Route::patch('/balita/{balita}', [BalitaController::class, 'update'])->whereNumber('balita');
        Route::delete('/balita/{balita}', [BalitaController::class, 'destroy'])->whereNumber('balita')->name('balita.destroy');

        // Master Posyandu (Admin)
        Route::get('/posyandu', [PosyanduController::class, 'index'])->name('posyandu.index');
        Route::post('/posyandu', [PosyanduController::class, 'store'])->name('posyandu.store');
        Route::put('/posyandu/{posyandu}', [PosyanduController::class, 'update'])->whereNumber('posyandu')->name('posyandu.update');
        Route::delete('/posyandu/{posyandu}', [PosyanduController::class, 'destroy'])->whereNumber('posyandu')->name('posyandu.destroy');
    });

    /*
    |----------------------------------------------------------------------
    | Profil pengguna
    |----------------------------------------------------------------------
    | Beberapa layout (mis. Breeze) memanggil route('profile.edit').
    | Jika ProfileController ada -> route asli. Jika tidak -> pengalihan
    | sementara agar tidak muncul error "Route [profile.edit] not defined".
    */
    if (class_exists(ProfileController::class)) {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    } else {
        Route::redirect('/profile', '/balita')->name('profile.edit');
    }
});

// Route autentikasi bawaan Laravel (login, logout, dsb.)
require __DIR__ . '/auth.php';