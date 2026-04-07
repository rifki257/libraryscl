<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Buku;
use Illuminate\Support\Facades\Auth;
use App\Models\Peminjaman;

class WishlistController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $wishlistItems = Wishlist::with('buku')
            ->where('id', $userId)
            ->latest()
            ->get();

        $totalDipinjam = Peminjaman::where('id', $userId)
            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat', 'menunggu'])
            ->count();

        $sisaJatah = 6 - $totalDipinjam;

        return view('wishlist', compact('wishlistItems', 'sisaJatah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_buku' => 'required|exists:buku,id_buku',
        ]);

        $userId = Auth::id();
        $idBuku = $request->id_buku;

        $exists = Wishlist::where('id', $userId)
            ->where('id_buku', $idBuku)
            ->first();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Buku ini sudah ada di wishlist kamu.'
            ], 422);
        }

        Wishlist::create([
            'id' => $userId,
            'id_buku' => $idBuku,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil ditambahkan!'
        ]);
    }

    public function destroy($id)
    {
        $wishlist = Wishlist::where('id', auth()->id())
            ->where('id_wishlist', $id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return back()->with('success', 'Buku berhasil dihapus dari wishlist.');
        }

        return back()->with('error', 'Data tidak ditemukan.');
    }
    public function peminjamanBeda(Request $request)
    {
        // Mengambil ID buku dari parameter URL ?ids=1,2,3
        $ids = explode(',', $request->query('ids'));

        // Ambil data buku berdasarkan ID tersebut
        $bukuTerpilih = Buku::whereIn('id_buku', $ids)->get();

        // Kirim ke view pinjambeda.blade.php
        return view('pinjambeda', compact('bukuTerpilih'));
    }
}
