<?php

namespace App\Http\Controllers;

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

    public function tolak($id)
    {
        $pinjam = Peminjaman::where('id_pinjam', $id)->firstOrFail();
        $pinjam->update(['status' => 'ditolak']);
        return back()->with('info', 'Permintaan peminjaman telah ditolak.');
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
