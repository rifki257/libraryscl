<?php

namespace App\Http\Controllers;

use App\Models\peminjaman;
use App\Models\katalog;
use Illuminate\Http\Request;
use App\Models\buku;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $dataBuku = Buku::all();
        if ($request->ajax()) {
            return view('partials.katalog_isi', compact('dataBuku'))->render();
        }
        return view('katalog', compact('dataBuku'));
    }

    public function katalog(Request $request)
    {
        $query = $request->input('q');
        $kategoriSiswa = Kategori::with(['buku' => function($q) use ($query) {
            if ($query) {
                $q->where('judul', 'like', "%{$query}%")
                ->orWhere('penulis', 'like', "%{$query}%");
            }
        }])->get();

        $kategoriSiswa = $kategoriSiswa->filter(function($kat) {
            return $kat->buku->count() > 0;
        });

        if ($request->ajax()) {
            return view('partials.katalog_isi', compact('kategoriSiswa'))->render();
        }

        return view('katalog', compact('kategoriSiswa'));
    }
}
