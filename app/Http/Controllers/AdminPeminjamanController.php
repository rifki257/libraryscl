<?php

namespace App\Http\Controllers;

use App\Notifications\PeminjamanDitolak;
use App\Models\Peminjaman;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPeminjamanController extends Controller
{
    // AdminPeminjamanController.php

    public function index()
    {
        // Hanya ambil permintaan pinjam baru
        $peminjamanPending = Peminjaman::with(['user', 'buku'])
            ->whereIn('status', ['pending', 'menunggu'])
            ->get();

        // Hanya ambil data yang statusnya aktif dipinjam/selesai
        $semuaPeminjaman = Peminjaman::with(['user', 'buku'])
            ->whereIn('status', ['dipinjam', 'kembali', 'ditolak', 'terlambat'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // JANGAN ambil ajukan_kembali di sini
        return view('admin_persetujuan', compact('peminjamanPending', 'semuaPeminjaman'));
    }

    public function setujui($id)
    {
        $pinjam = Peminjaman::where('id_pinjam', $id)->firstOrFail();
        $buku = Buku::where('id_buku', $pinjam->id_buku)->first();
        if (!$buku || $buku->jumlah <= 0) {
            return back()->with('error', 'Gagal! Stok buku sudah habis atau tidak ditemukan.');
        }
        try {
            DB::transaction(function () use ($pinjam, $buku) {
                $buku->decrement('jumlah');
                $pinjam->update([
                    'status'     => 'dipinjam',
                    'tgl_pinjam' => now(),
                ]);
            });
            return back()->with('success', 'Peminjaman telah disetujui dan stok buku berkurang.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function tolak(Request $request, $id)
    {
        // 1. Cari data peminjaman
        $pinjam = Peminjaman::where('id_pinjam', $id)->firstOrFail();

        // 2. Update status (dan simpan alasan ke kolom pesan_admin jika ada)
        $pinjam->update([
            'status' => 'ditolak',
            'pesan_admin' => $request->alasan
        ]);

        // 3. Kirim notifikasi ke User pemilik pinjaman
        $user = $pinjam->user; // Pastikan relasi 'user' ada di Model Peminjaman
        $user->notify(new PeminjamanDitolak($pinjam, $request->alasan));

        return back()->with('info', 'Permintaan ditolak dan notifikasi telah dikirim.');
    }

    public function konfirmasi($id)
    {
        $pinjam = Peminjaman::where('id_pinjam', $id)->firstOrFail();
        $buku = Buku::where('id_buku', $pinjam->id_buku)->first();

        try {
            DB::transaction(function () use ($pinjam, $buku) {
                // 1. Tambah kembali stok buku (karena sudah dikembalikan fisik)
                if ($buku) {
                    $buku->increment('jumlah'); // Pastikan nama kolom stok kamu 'jumlah'
                }

                // 2. Update status jadi 'kembali' (Status FINAL)
                $pinjam->update([
                    'status' => 'kembali',
                    'tgl_kembali' => now(), // Tanggal buku benar-benar diterima admin
                ]);
            });

            return back()->with('success', 'Buku telah diterima dan stok dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal konfirmasi: ' . $e->getMessage());
        }
    }
}
