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
    $dipinjam = Peminjaman::with(['user', 'buku'])
                ->where('status', 'dikembalikan') // <--- PASTI KAN STATUS INI SESUAI
                ->latest()
                ->get();

    // Data untuk tab Semua Data (Arsip)
    $dikembalikan = Pengembalian::with(['user', 'buku'])->latest()->get();

    return view('pengembalian', compact('dipinjam', 'dikembalikan'));
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
