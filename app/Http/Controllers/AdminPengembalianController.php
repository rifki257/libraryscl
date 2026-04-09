<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminPengembalianController extends Controller
{
    public function index()
    {
        // Menggunakan 'buku' sesuai model
        $semuaPeminjaman = Peminjaman::with(['user', 'buku'])
            ->whereIn('status', ['dipinjam', 'dikembalikan', 'ajukan_kembali', 'Dikembalikan'])
            ->get();

        return view('pengembalian', compact('semuaPeminjaman'));
    }

    public function konfirmasi(Request $request, $id)
    {
        $pinjam = Peminjaman::where('id_pinjam', $id)->firstOrFail();

        DB::transaction(function () use ($pinjam) {
            // 1. Hitung Denda
            $jt = Carbon::parse($pinjam->tgl_jatuh_tempo)->startOfDay();
            $kb = now()->startOfDay();
            $selisih = $kb->gt($jt) ? $kb->diffInDays($jt) : 0;
            $denda = $selisih * 50000;

            // 2. Simpan ke tabel pengembalian (tetap dilakukan sebagai riwayat)
            Pengembalian::create([
                'id_pinjam'       => $pinjam->id_pinjam,
                'id_buku'         => $pinjam->id_buku,
                'id'              => $pinjam->id,
                'tgl_pinjam'      => $pinjam->tgl_pinjam,
                'tgl_jatuh_tempo' => $pinjam->tgl_jatuh_tempo,
                'tgl_kembali'     => now(),
                'denda'           => $denda
            ]);

            // --- TAMBAHAN LOGIKA DENDA KE DATABASE DENDA ---
            if ($denda > 0) {
                \App\Models\Denda::create([
                    'jumlah_denda' => $denda,
                    'id'           => $pinjam->id,      // ID User
                    'id_buku'      => $pinjam->id_buku,
                ]);
            }
            // ----------------------------------------------

            // 3. Update status peminjaman
            $pinjam->update(['status' => 'kembali']);

            // 4. Update stok buku
            if ($pinjam->buku) {
                $pinjam->buku->increment('jumlah');
            }
        });

        return redirect()->route('pengembalian')->with('success', 'Buku berhasil dikonfirmasi!');
    }

    public function history()
    {
        $semuaPeminjaman = \App\Models\Peminjaman::with(['user', 'buku'])
            ->where('status', 'kembali')
            ->latest()
            ->get();
        return view('datapengembalian', compact('semuaPeminjaman'));
    }

    public function peminjamanData()
    {
        $semuaPeminjaman = \App\Models\Peminjaman::with(['user', 'buku'])->latest()->get();
        return view('partials.pinjam_data', compact('semuaPeminjaman'));
    }
}
