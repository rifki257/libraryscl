<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalDenda = \App\Models\Pengembalian::sum('denda');

        $topDenda = \App\Models\Pengembalian::with(['user'])
            ->where('denda', '>', 0)
            ->orderBy('denda', 'desc')
            ->take(5)
            ->get();

        $totalPinjamAktif = \App\Models\Peminjaman::where('status', 'dipinjam')->count();
        $totalSudahKembali = \App\Models\Peminjaman::where('status', 'kembali')->count();
        $totalRiwayat = $totalPinjamAktif + $totalSudahKembali; 

        $data = [
            'totalBuku' => \App\Models\Buku::count(),
            'totalPinjam' => $totalPinjamAktif,
            'totalKembali' => $totalSudahKembali,
            'totalAdmin' => \App\Models\User::whereIn('role', ['admin', 'kepper'])->count(),
            'totalUser' => \App\Models\User::where('role', 'anggota')->count(),
            'totalDenda' => $totalDenda,
            'topDenda' => $topDenda,
            'totalRiwayat' => $totalRiwayat,
        ];

        return view('dashboard', $data);
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
