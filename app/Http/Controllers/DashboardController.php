<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDenda = \App\Models\Pengembalian::sum('denda');

        $topDenda = \App\Models\Pengembalian::with(['user'])
            ->where('denda', '>', 0)
            ->orderBy('denda', 'desc')
            ->take(5)
            ->get();

        $totalPinjamAktif = \App\Models\Peminjaman::whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat'])->count();
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
}
