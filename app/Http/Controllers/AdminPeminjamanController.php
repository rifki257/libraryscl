<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPeminjamanController extends Controller
{
    public function index()
    {
        $peminjamanPending = Peminjaman::with(['user', 'buku'])
            ->where('status', 'pending')
            ->get();
        $semuaPeminjaman = Peminjaman::with(['user', 'buku'])
            ->whereIn('status', ['dipinjam', 'kembali', 'ditolak'])
            ->orderBy('updated_at', 'desc')
            ->get();
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
}
