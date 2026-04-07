<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;

class AdminPengembalianController extends Controller
{
    public function index()
    {
        // HANYA ambil yang minta balik buku
        $permintaanKembali = Peminjaman::with(['user', 'buku'])
            ->where('status', 'ajukan_kembali')
            ->latest()
            ->get();

        return view('pengembalian', compact('permintaanKembali'));
    }
}
