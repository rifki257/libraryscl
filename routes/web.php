<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PeminjamandataController;
use App\Http\Controllers\UserdashboardController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\UserpengembalianController;
use Illuminate\Support\Facades\Route;

// --- PUBLIC ROUTE ---
Route::get('/', function () {
    return view('katalog');
});

Route::get('/', [KatalogController::class, 'index'])->name('katalog');
require __DIR__ . '/auth.php';

// --- SEMUA HARUS LOGIN ---
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. BLOK KHUSUS ADMIN (Kepper & Petugas)
    Route::group([], function () {

        // Dashboard Admin
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'Index'])
            ->middleware(['auth'])
            ->name('dashboard');

        Route::get('/userdashboard', [App\Http\Controllers\UserdashboardController::class, 'Index'])
            ->middleware(['auth', 'verified'])
            ->name('userdashboard');

        // Manajemen Admin
        Route::get('/register-petugas', [UserController::class, 'create'])->name('register.petugas');
        Route::post('/register-petugas', [UserController::class, 'store'])->name('register.petugas.store');
        Route::resource('users', UserController::class);
        Route::put('/user/bulk-update-kelas', [UserController::class, 'bulkUpdateKelas'])->name('user.bulkUpdateKelas');
        Route::delete('/user/bulk-delete', [UserController::class, 'bulkDestroy'])->name('user.bulkDestroy');

        // Manajemen Buku
        Route::get('/buku', [BukuController::class, 'index'])->name('buku');
        Route::get('/buku/create', [BukuController::class, 'create'])->name('buku.create');
        Route::post('/buku/simpan', [BukuController::class, 'store'])->name('buku.store');
        Route::get('/buku/detail/{id}', [BukuController::class, 'show'])->name('buku.detail');
        Route::get('/buku/edit/{id}', [BukuController::class, 'edit'])->name('buku.edit');
        Route::put('/buku/update/{id}', [BukuController::class, 'update'])->name('buku.update');
        Route::delete('/buku/delete/{id}', [BukuController::class, 'destroy'])->name('buku.destroy');
        Route::get('/buku-search', [BukuController::class, 'search'])->name('buku.search');
    });

    // 2. PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 3.KARALOG


    // 4. PEMINJAMAN
    Route::get('/pinjam/{id}', [App\Http\Controllers\PeminjamanController::class, 'create'])->name('peminjaman');
    Route::post('/peminjaman/store', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::get('/my-peminjaman', [PeminjamanController::class, 'history'])->name('mypinjaman');


    // 5. PEMINJAMANDATA
    Route::get('/peminjamandata', [PeminjamandataController::class, 'index'])->name('peminjamandata');

    // 6. PENGEMBALIAN
    Route::put('/peminjaman/ajukan/{id}', [PeminjamanController::class, 'ajukan_kembali'])->name('peminjaman.ajukan_kembali');
    Route::put('/peminjamandata/konfirmasi/{id}', [PeminjamandataController::class, 'konfirmasi_kembali'])->name('admin.konfirmasi_kembali');

    // 7. ADMIN PENGEMBALIAN
    Route::put('/admin/konfirmasi-kembali/{id}', [PeminjamanController::class, 'konfirmasi_kembali'])->name('admin.konfirmasi_kembali');

    // 8. AKUN ADMIN DAN USER
    Route::middleware(['auth'])->group(function () {
        Route::get('/akun-admin', [UserController::class, 'index'])->name('akun_admin');
        Route::delete('/akun-admin/{id}', [UserController::class, 'destroy'])->name('admin.destroy');
        Route::put('/akun-admin/update-password/{id}', [UserController::class, 'updatePassword'])->name('admin.updatePassword');
    });

    // 9. AKUN USER
    Route::middleware(['auth'])->group(function () {
        Route::get('/users', [UserController::class, 'indexAnggota'])->name('akun_user');
        Route::put('/users/{id}/password', [UserController::class, 'updatePassword'])->name('user.updatePassword');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    });

    // 10. PENGEMBALIAN
    Route::middleware(['auth'])->group(function () {
        Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian');
    });

    // 11. BALIK
    Route::get('/mybalik', [UserpengembalianController::class, 'history'])->name('mybalik');
    Route::post('/mybalik/store/{id}', [UserpengembalianController::class, 'store'])->name('mybalik.store');
});
