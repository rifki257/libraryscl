<?php

namespace App\Http\Controllers;

use App\Models\peminjaman;
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

        $bukus = \App\Models\Buku::when($query, function ($q) use ($query) {
            $q->where('judul', 'like', "%{$query}%")
                ->orWhere('penulis', 'like', "%{$query}%")
                ->orWhere('penerbit', 'like', "%{$query}%");
        })->get();
        if ($request->ajax()) {
            return view('partials.katalog_isi', compact('bukus'))->render();
        }

        return view('katalog', compact('bukus'));
    }
}
