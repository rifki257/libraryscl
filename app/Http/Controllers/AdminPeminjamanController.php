<?php

namespace App\Http\Controllers;

use App\Notifications\PeminjamanDitolak;
use App\Models\Peminjaman;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPeminjamanController extends Controller
{
    // konfirmasi peminjaman
    public function index()
    {
        $peminjamanPending = Peminjaman::with(['user', 'buku'])
            ->whereIn('status', ['pending', 'menunggu'])
            ->get();

        $semuaPeminjaman = Peminjaman::with(['user', 'buku'])
            ->whereIn('status', ['dipinjam', 'kembali', 'ditolak', 'terlambat'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin_persetujuan', compact('peminjamanPending', 'semuaPeminjaman'));
    }

    // admin menyetujui
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

    public function persetujuan(Request $request)
    {
    $query = Peminjaman::with(['buku', 'user'])
        ->where('status', 'menunggu');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('user', function($u) use ($search) {
                $u->where('name', 'like', '%' . $search . '%');
            })->orWhereHas('buku', function($b) use ($search) {
                $b->where('judul', 'like', '%' . $search . '%');
            });
        });
        
    }

    $semuaPeminjaman = $query->latest()->paginate(6); 

    if ($request->ajax()) {
        return view('partials.konfir_pinjam', compact('semuaPeminjaman'))->render();
    }

    return view('admin_persetujuan', compact('semuaPeminjaman'));
    }
    
    // admin menolak
    public function tolak(Request $request, $id)
    {
        $pinjam = Peminjaman::where('id_pinjam', $id)->firstOrFail();

        $pinjam->update([
            'status' => 'ditolak',
            'pesan_admin' => $request->alasan
        ]);

        $user = $pinjam->user; 
        $user->notify(new PeminjamanDitolak($pinjam, $request->alasan));

        return back()->with('info', 'Permintaan ditolak dan notifikasi telah dikirim.');
    }

    // konfirmasi
    public function konfirmasi($id)
    {
        $pinjam = Peminjaman::where('id_pinjam', $id)->firstOrFail();
        $buku = Buku::where('id_buku', $pinjam->id_buku)->first();

        try {
            DB::transaction(function () use ($pinjam, $buku) {
                if ($buku) {
                    $buku->increment('jumlah');
                }

                $pinjam->update([
                    'status' => 'kembali',
                    'tgl_kembali' => now(), 
                ]);
            });

            return back()->with('success', 'Buku telah diterima dan stok dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal konfirmasi: ' . $e->getMessage());
        }
    }
    
    
}
