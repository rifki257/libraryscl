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

    // Untuk Tab Data Pinjam (Semua yang sudah diproses: dipinjam, kembali, atau ditolak)
    $semuaPeminjaman = Peminjaman::with(['user', 'buku'])
        ->whereIn('status', ['dipinjam', 'kembali', 'ditolak'])
        ->orderBy('updated_at', 'desc') // Yang terbaru diproses muncul di atas
        ->get();

        return view('admin_persetujuan', compact('peminjamanPending', 'semuaPeminjaman'));
    }

    public function setujui($id)
{
    // Menggunakan where agar lebih pasti mencari ke kolom id_pinjam
    $pinjam = Peminjaman::where('id_pinjam', $id)->firstOrFail();
    $buku = Buku::where('id_buku', $pinjam->id_buku)->first();

    if (!$buku || $buku->jumlah <= 0) {
        return back()->with('error', 'Gagal! Stok buku sudah habis atau tidak ditemukan.');
    }

    try {
        DB::transaction(function () use ($pinjam, $buku) {
            // 1. Kurangi stok buku
            $buku->decrement('jumlah');

            // 2. Update status & tanggal pinjam yang sebenarnya
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
}
