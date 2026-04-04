<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\pengembalian;
use App\Models\Peminjaman;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Tabel Atas (Peminjaman Aktif)
        $dipinjam = \App\Models\Peminjaman::with(['user', 'buku'])
            ->whereIn('status', ['dipinjam', 'proses', 'terlambat', 'pending', 'dikembalikan'])
            ->latest()
            ->get();

        // Tabel Bawah (Riwayat Pengembalian)
        $dataHistory = \App\Models\Pengembalian::with(['user', 'buku'])
            ->latest()
            ->get();

        // Menghitung total untuk badge Konfirmasi
        $totalKonfirmasi = \App\Models\Peminjaman::whereIn('status', ['proses', 'pending'])->count();

        // Menghitung total untuk badge Semua Data (berdasarkan hasil query $dipinjam)
        $totalSemua = $dipinjam->count();

        return view('pengembalian', compact('dipinjam', 'dataHistory', 'totalKonfirmasi', 'totalSemua'));
    }


    public function konfirmasi(Request $request, $id_pinjam)
    {
        // 1. Ambil SEMUA data peminjaman dengan ID Pinjam yang sama
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
                // Kalkulasi denda per buku
                $tglJatuhTempo = \Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo)->startOfDay();
                $selisihHari = $hariIni->gt($tglJatuhTempo) ? $hariIni->diffInDays($tglJatuhTempo) : 0;
                $tarifDenda = 50000;
                $dendaPerBuku = abs($selisihHari * $tarifDenda);

                $totalDendaKumulatif += $dendaPerBuku;

                // 2. Simpan ke tabel riwayat pengembalian untuk SETIAP buku
                \App\Models\Pengembalian::create([
                    'id_pinjam'   => $peminjaman->id_pinjam,
                    'id_buku'     => $peminjaman->id_buku,
                    'id'          => $peminjaman->id, // User ID
                    'tgl_pinjam'  => $peminjaman->tgl_pinjam,
                    'tgl_kembali' => $hariIni->format('Y-m-d'),
                    'denda'       => $dendaPerBuku,
                ]);

                // 3. Update status per baris buku
                $peminjaman->update([
                    'status'      => 'kembali',
                    'tgl_kembali' => $hariIni->format('Y-m-d'),
                    'denda'       => $dendaPerBuku
                ]);

                // 4. Kembalikan stok untuk masing-masing buku
                if ($peminjaman->buku) {
                    $peminjaman->buku->increment('jumlah');
                }
            }
        });

        return redirect()->back()->with('success', 'Konfirmasi Berhasil! Semua buku telah kembali. Total Denda: Rp ' . number_format($totalDendaKumulatif, 0, ',', '.'));
    }

    // Contoh di PeminjamanController (Bagian User)
    public function ajukan_kembali(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update([
            'status' => 'dikembalikan', // Pastikan teks ini SAMA dengan yang dicari Admin
            'denda'  => $request->denda,
        ]);

        return back()->with('success', 'Pengembalian berhasil diajukan.');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Ambil data peminjaman terkait
        $pinjam = Peminjaman::findOrFail($request->id_pinjam);

        // 2. Hitung selisih hari (Tanggal Kembali vs Jatuh Tempo)
        $tgl_kembali = Carbon::now(); // atau $request->tgl_kembali
        $tgl_jatuh_tempo = Carbon::parse($pinjam->tgl_jatuh_tempo);

        // diffInDays akan menghasilkan angka positif jika terlambat
        $hari_terlambat = $tgl_jatuh_tempo->diffInDays($tgl_kembali, false);

        $total_denda = 0;
        if ($tgl_kembali > $tgl_jatuh_tempo) {
            $nominal_per_hari = 1000; // Set tarif denda kamu di sini
            $total_denda = $hari_terlambat * $nominal_per_hari;
        }

        // 3. Simpan ke tabel Pengembalian
        Pengembalian::create([
            'id_pinjam'       => $pinjam->id_pinjam,
            'tgl_kembali'     => $tgl_kembali,
            'tgl_jatuh_tempo' => $tgl_jatuh_tempo,
            'id_buku'         => $pinjam->id_buku,
            'id'              => $pinjam->id, // User ID
            'denda'           => $total_denda,
        ]);

        // 4. Update status di tabel Peminjaman agar buku tersedia lagi
        $pinjam->update(['status' => 'Kembali']);

        return redirect()->route('pengembalian.index')->with('success', 'Buku kembali! Denda: Rp ' . number_format($total_denda));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
