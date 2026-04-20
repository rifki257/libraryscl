<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\AdminPeminjamanController;
use App\Http\Controllers\AdminPengembalianController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\KategoriController;
use Illuminate\Support\Facades\Auth;

// --- PUBLIC ROUTE ---

Route::get('/', [KategoriController::class, 'katalog'])->name('katalog');
require __DIR__ . '/auth.php';

Route::get('/mark-read', function () {
    Auth::user()->unreadNotifications->markAsRead();
    return back();
})->name('markNotificationsRead');

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
        Route::post('/update-kelas-massal', [UserController::class, 'bulkUpdateKelas'])->name('user.bulk_update_kelas');
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

    // UserController for user
    Route::middleware(['auth'])->group(function () {
        Route::get('/akun-admin', [UserController::class, 'index'])->name('akun_admin');
        Route::delete('/akun-admin/{id}', [UserController::class, 'destroy'])->name('admin.destroy');
        Route::put('/akun-admin/update-password/{id}', [UserController::class, 'updatePassword'])->name('admin.updatePassword');
        Route::post('/admin/reset-password/{id}', [UserController::class, 'resetPassword'])->name('admin.resetPassword');
        Route::get('/users', [UserController::class, 'index'])->name('akun_user');
    });
    Route::get('/admin/users/siswa', [UserController::class, 'indexSiswa'])->name('users.siswa');
    Route::post('/admin/siswa/reset-password/{id}', [UserController::class, 'resetPasswordSiswa'])
    ->name('admin.reset_password_siswa');
    Route::delete('/admin/siswa/{id}', [UserController::class, 'destroySiswa'])
    ->name('admin.destroy_siswa');
    Route::post('/admin/siswa/bulk-update-kelas', [UserController::class, 'bulkUpdateKelas'])->name('users.bulkUpdateKelas');
    Route::post('/admin/siswa/bulk-alumni', [UserController::class, 'bulkAlumni'])->name('users.bulkAlumni');

    // Profilecontroller
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // KatalogController

    // peminjamancontroller
    Route::get('/pinjam/{id}', [App\Http\Controllers\PeminjamanController::class, 'create'])->name('peminjaman');
    Route::post('/peminjaman/store', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::post('/peminjaman/store-masal', [PeminjamanController::class, 'storeMasal'])->name('peminjaman.store.masal');
    Route::get('/my-peminjaman', [PeminjamanController::class, 'history'])->name('mypinjaman');
    Route::put('/peminjaman/ajukan-kembali/{id}', [PeminjamanController::class, 'ajukanKembali'])
        ->name('peminjaman.ajukan_kembali');
    Route::delete('/peminjaman/{id}/batal', [PeminjamanController::class, 'destroy'])->name('peminjaman.cancel');
    Route::get('/my-history', [PeminjamanController::class, 'history'])->name('peminjaman.history');
    Route::put('/admin/setujui/{id}', [PeminjamanController::class, 'setujuiPinjam'])->name('admin.setujui');
    Route::get('/pinjam-masal', [PeminjamanController::class, 'createMasal'])->name('peminjaman.masal');
    Route::get('/pinjam/{id}', [PeminjamanController::class, 'pinjam'])->name('peminjaman');
    Route::get('/admin/peminjaman/data', [PeminjamanController::class, 'index'])->name('admin.peminjaman.data');
    Route::get('/admin/peminjaman/data', [PeminjamanController::class, 'peminjamanData'])
        ->name('persetujuan.data');
    Route::get('/admin/laporan', [App\Http\Controllers\PeminjamanController::class, 'halamanLaporan'])->name('admin.laporan.index');
    Route::get('/admin/laporan/export', [App\Http\Controllers\PeminjamanController::class, 'exportWord'])->name('admin.laporan.export');
    Route::get('/laporan-user', [PeminjamanController::class, 'laporanUser'])->name('laporan_user');

    // WishlistController
    Route::get('/peminjaman-beda', [WishlistController::class, 'peminjamanBeda'])->name('peminjaman.beda');
    Route::post('/wishlist/store', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // AdminPeminjamanController
    Route::patch('/admin/persetujuan/{id}/tolak', [AdminPeminjamanController::class, 'tolak'])->name('admin.tolak');
    Route::get('/admin/persetujuan', [AdminPeminjamanController::class, 'persetujuan'])->name('admin.persetujuan');

    // KategoriController
    Route::get('/kategori-buku', [KategoriController::class, 'index'])->name('kategori.buku');
    Route::get('/kategori-buku/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('/kategori-buku/store', [KategoriController::class, 'store'])->name('kategori.store');
    Route::post('/kategori-buku', [KategoriController::class, 'store'])->name('kategori.store');
    Route::put('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    Route::get('/kategori/{id}', [KategoriController::class, 'show'])->name('isikategori');

    // AdminPengembalianController
    Route::get('/admin/pengembalian/data', [AdminPengembalianController::class, 'history'])
        ->name('mybalik');
    Route::put('/admin/pengembalian/konfirmasi/{id}', [AdminPengembalianController::class, 'konfirmasi'])->name('admin.konfirmasi_kembali');
    Route::get('/admin/pengembalian', [AdminPengembalianController::class, 'index'])->name('pengembalian');
    Route::get('/admin/pengembalian/data', [AdminPengembalianController::class, 'history'])->name('pengembalian.data');
    Route::put('/admin/konfirmasi-kembali/{id}', [AdminPengembalianController::class, 'konfirmasi'])->name('admin.konfirmasi_kembali');

    // NotifikasiController
    Route::middleware(['auth'])->group(function () {
    Route::get('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('markNotificationsRead');
    });

});
