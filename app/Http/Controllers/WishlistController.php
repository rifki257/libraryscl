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
            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat', 'menunggu', 'ajukan_kembali'])
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
        $userId = auth()->id();

        $singleId = $request->query('id');  
        $multiIds = $request->query('ids');  

        if ($singleId) {
            $books = \App\Models\Buku::where('id_buku', $singleId)->get();
        } elseif ($multiIds) {
            $ids = explode(',', $multiIds);
            $books = \App\Models\Buku::whereIn('id_buku', $ids)->get();
        } else {
            $books = \App\Models\Wishlist::where('id', $userId)
                ->with('buku')
                ->get()
                ->pluck('buku')
                ->filter();
        }

        if ($books->isEmpty()) {
            return redirect()->route('katalog')->with('error', 'Pilih buku terlebih dahulu.');
        }

        $totalBukuAktif = \App\Models\Peminjaman::where('id', $userId)
            ->whereIn('status', ['diajukan', 'dipinjam'])
            ->count();

        return view('pinjambeda', [
            'books' => $books,        
            'bukuTerpilih' => $books, 
            'totalBukuAktif' => $totalBukuAktif
        ]);
    }
}
