<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class UserdashboardController extends Controller
{
    public function index()
{
    $id = auth()->id();

    // 1. Hitung data untuk Chart
    $countAjuan = \App\Models\Peminjaman::where('id', $id)
                    ->whereIn('status', ['menunggu', 'pending'])->count();
                    
    $countPinjam = \App\Models\Peminjaman::where('id', $id)
                    ->where('status', 'dipinjam')->count();
                    
    $countKembali = \App\Models\Peminjaman::where('id', $id)
                    ->whereIn('status', ['proses', 'ajukan_kembali'])->count();

    // 2. Ambil 3 Aktivitas Terakhir
    $recentActivities = \App\Models\Peminjaman::with('buku')
                        ->where('id', $id)
                        ->orderBy('updated_at', 'desc')
                        ->take(3)
                        ->get();

    return view('userdashboard', compact('countAjuan', 'countPinjam', 'countKembali', 'recentActivities'));
}
}
