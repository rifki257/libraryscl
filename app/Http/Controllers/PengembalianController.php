<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\pengembalian;
use App\Models\Peminjaman;

class PengembalianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // Mengambil semua data yang belum berstatus 'kembali'
    $dipinjam = Peminjaman::with(['user', 'buku'])
        ->whereIn('status', ['dipinjam', 'proses', 'terlambat']) // Sesuaikan dengan semua status yang muncul di tabel
        ->latest()
        ->get();

    $dikembalikan = Peminjaman::with(['user', 'buku'])
        ->where('status', 'kembali')
        ->latest()
        ->get();

    // Hitung TOTAL semua data yang ada di list konfirmasi
    $totalKonfirmasi = $dipinjam->count(); 

    return view('pengembalian', compact('dipinjam', 'dikembalikan', 'totalKonfirmasi'));
}

    public function konfirmasi(Request $request, $id_pinjam)
{
    $peminjaman = Peminjaman::where('id_pinjam', $id_pinjam)->firstOrFail();

    // Update status peminjaman
    $peminjaman->update([
        'status' => 'kembali',
        'tgl_kembali' => now(),
    ]);

    // Update jumlah buku (Kembalikan stok)
    if ($peminjaman->buku) {
        // GANTI 'stok' MENJADI 'jumlah'
        $peminjaman->buku->increment('jumlah'); 
    }

    return redirect()->back()->with('success', 'Buku berhasil dikembalikan!');
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
        //
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
