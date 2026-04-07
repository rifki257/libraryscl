<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PeminjamandataController;
use App\Http\Controllers\UserdashboardController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\AdminPengembalianController;
use App\Http\Controllers\AdminPeminjamanController;
use App\Http\Controllers\PeminjamanBedaController;
use App\Http\Controllers\UserpengembalianController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\KategoriController;
use App\Models\Buku;
use Illuminate\Http\Request;

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
    // Tambahkan ini untuk memproses simpan banyak buku sekaligus
    Route::post('/peminjaman/store-masal', [PeminjamanController::class, 'storeMasal'])->name('peminjaman.store.masal');
    Route::get('/my-peminjaman', [PeminjamanController::class, 'history'])->name('mypinjaman');
    Route::put('/peminjaman/ajukan-kembali/{id}', [PeminjamanController::class, 'ajukanKembali'])
        ->name('peminjaman.ajukan_kembali');
    Route::delete('/peminjaman/{id}/batal', [PeminjamanController::class, 'destroy'])->name('peminjaman.cancel');
    Route::get('/my-history', [PeminjamanController::class, 'history'])->name('peminjaman.history');
    // Contoh rute yang benar
    Route::get('/admin/persetujuan', [PeminjamanController::class, 'persetujuan'])->name('admin.persetujuan');
    // Pastikan tujuannya ke PeminjamanController
    Route::put('/admin/setujui/{id}', [PeminjamanController::class, 'setujuiPinjam'])->name('admin.setujui');
    Route::get('/pinjam-masal', [PeminjamanController::class, 'createMasal'])->name('peminjaman.masal');
    Route::get('/peminjaman-beda', [WishlistController::class, 'peminjamanBeda'])->name('peminjaman.beda');
    // Pastikan ada ->name('peminjaman') di ujungnya
    Route::get('/pinjam/{id}', [PeminjamanController::class, 'pinjam'])->name('peminjaman');

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

        Route::put('/admin/konfirmasi-pengembalian/{id_pinjam}', [PengembalianController::class, 'konfirmasi'])
            ->name('admin.konfirmasi_kembali');
    });
    // 11. BALIK
    Route::get('/mybalik', [UserpengembalianController::class, 'history'])->name('mybalik');
    Route::post('/mybalik/store/{id}', [UserpengembalianController::class, 'store'])->name('mybalik.store');

    Route::post('/wishlist/store', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    Route::patch('/admin/persetujuan/{id}/tolak', [AdminPeminjamanController::class, 'tolak'])->name('admin.tolak');
    Route::get('/admin/pengembalian', [AdminPengembalianController::class, 'index'])->name('admin.pengembalian.index');
    Route::put('/admin/pengembalian/{id}/konfirmasi', [AdminPengembalianController::class, 'konfirmasi'])->name('admin.pengembalian.konfirmasi');

    Route::get('/kategori-buku', [KategoriController::class, 'index'])->name('kategori.buku');

    Route::get('/kategori-buku/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('/kategori-buku/store', [KategoriController::class, 'store'])->name('kategori.store');
    Route::post('/kategori-buku', [KategoriController::class, 'store'])->name('kategori.store');
    Route::put('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
});
