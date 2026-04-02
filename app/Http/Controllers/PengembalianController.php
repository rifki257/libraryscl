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
        // Menggunakan where('id_pinjam', ...) sesuai dengan struktur tabel Anda
        $peminjaman = Peminjaman::where('id_pinjam', $id_pinjam)->firstOrFail();

        // 1. Ambil Tanggal (Gunakan startOfDay agar jam tidak mengacaukan hitungan hari)
        $tglJatuhTempo = \Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo)->startOfDay();
        $hariIni = \Carbon\Carbon::now()->startOfDay();

        // 2. Kalkulasi Selisih Hari yang Lebih Akurat
        // Jika hari ini sudah LEWAT dari jatuh tempo, hitung selisihnya
        if ($hariIni->gt($tglJatuhTempo)) {
            $selisihHari = $hariIni->diffInDays($tglJatuhTempo);
        } else {
            $selisihHari = 0;
        }

        // 3. Hitung Total Denda (Pastikan hasil akhirnya positif dengan abs)
        $tarifDenda = 50000;
        $totalDenda = abs($selisihHari * $tarifDenda);

        \DB::transaction(function () use ($peminjaman, $totalDenda, $hariIni) {
            // Simpan ke tabel riwayat pengembalian
            \App\Models\Pengembalian::create([
                'id_pinjam'   => $peminjaman->id_pinjam,
                'id_buku'     => $peminjaman->id_buku,
                'id'          => $peminjaman->id,
                'tgl_pinjam'  => $peminjaman->tgl_pinjam,
                'tgl_kembali' => $hariIni->format('Y-m-d'), // Simpan format tanggal saja
                'denda'       => $totalDenda,
            ]);

            // Update status di tabel peminjaman
            $peminjaman->update([
                'status'      => 'kembali',
                'tgl_kembali' => $hariIni->format('Y-m-d'),
                'denda'       => $totalDenda
            ]);

            // Kembalikan stok buku
            if ($peminjaman->buku) {
                $peminjaman->buku->increment('jumlah');
            }
        });

        return redirect()->back()->with('success', 'Konfirmasi Berhasil! Denda: Rp ' . number_format($totalDenda, 0, ',', '.'));
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
