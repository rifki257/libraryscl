<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\pengembalian;
use App\Models\Peminjaman;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    public function index()
    {
        $dipinjam = \App\Models\Peminjaman::with(['user', 'buku'])
            ->whereIn('status', ['dipinjam', 'proses', 'terlambat', 'pending', 'dikembalikan'])
            ->latest()
            ->get();

        $dataHistory = \App\Models\Pengembalian::with(['user', 'buku'])
            ->latest()
            ->get();
        $totalKonfirmasi = \App\Models\Peminjaman::whereIn('status', ['proses', 'pending'])->count();

        $totalSemua = $dipinjam->count();

        return view('pengembalian', compact('dipinjam', 'dataHistory', 'totalKonfirmasi', 'totalSemua'));
    }


    public function konfirmasi(Request $request, $id_pinjam)
    {
        $semuaPeminjaman = Peminjaman::where('id_pinjam', $id_pinjam)
            ->whereIn('status', ['dipinjam', 'proses', 'terlambat', 'pending', 'dikembalikan'])
            ->get();

        if ($semuaPeminjaman->isEmpty()) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $hariIni = \Carbon\Carbon::now()->startOfDay();
        $totalDendaKumulatif = 0;

        \DB::transaction(function () use ($semuaPeminjaman, $hariIni, &$totalDendaKumulatif) {
            foreach ($semuaPeminjaman as $peminjaman) {
                $tglJatuhTempo = \Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo)->startOfDay();
                $selisihHari = $hariIni->gt($tglJatuhTempo) ? $hariIni->diffInDays($tglJatuhTempo) : 0;
                $tarifDenda = 50000;
                $dendaPerBuku = abs($selisihHari * $tarifDenda);

                $totalDendaKumulatif += $dendaPerBuku;
                \App\Models\Pengembalian::create([
                    'id_pinjam'   => $peminjaman->id_pinjam,
                    'id_buku'     => $peminjaman->id_buku,
                    'id'          => $peminjaman->id,
                    'tgl_pinjam'  => $peminjaman->tgl_pinjam,
                    'tgl_kembali' => $hariIni->format('Y-m-d'),
                    'denda'       => $dendaPerBuku,
                ]);
                $peminjaman->update([
                    'status'      => 'kembali',
                    'tgl_kembali' => $hariIni->format('Y-m-d'),
                    'denda'       => $dendaPerBuku
                ]);
                if ($peminjaman->buku) {
                    $peminjaman->buku->increment('jumlah');
                }
            }
        });

        return redirect()->back()->with('success', 'Konfirmasi Berhasil! Semua buku telah kembali. Total Denda: Rp ' . number_format($totalDendaKumulatif, 0, ',', '.'));
    }
    public function ajukan_kembali(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update([
            'status' => 'dikembalikan',
            'denda'  => $request->denda,
        ]);

        return back()->with('success', 'Pengembalian berhasil diajukan.');
    }

    public function store(Request $request)
    {
        $pinjam = Peminjaman::findOrFail($request->id_pinjam);
        $tgl_kembali = Carbon::now();
        $tgl_jatuh_tempo = Carbon::parse($pinjam->tgl_jatuh_tempo);
        $hari_terlambat = $tgl_jatuh_tempo->diffInDays($tgl_kembali, false);

        $total_denda = 0;
        if ($tgl_kembali > $tgl_jatuh_tempo) {
            $nominal_per_hari = 1000; 
            $total_denda = $hari_terlambat * $nominal_per_hari;
        }
        Pengembalian::create([
            'id_pinjam'       => $pinjam->id_pinjam,
            'tgl_kembali'     => $tgl_kembali,
            'tgl_jatuh_tempo' => $tgl_jatuh_tempo,
            'id_buku'         => $pinjam->id_buku,
            'id'              => $pinjam->id,
            'denda'           => $total_denda,
        ]);
        $pinjam->update(['status' => 'Kembali']);

        return redirect()->route('pengembalian.index')->with('success', 'Buku kembali! Denda: Rp ' . number_format($total_denda));
    }

    public function history()
    {
        $userId = auth()->id();

        $riwayat = \App\Models\Peminjaman::where('user_id', $userId)
            ->where('status', 'kembali')
            ->with('buku')
            ->latest()
            ->get();

        return view('mybalik', compact('riwayat'));
    }
}
