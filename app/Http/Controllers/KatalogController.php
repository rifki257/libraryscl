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
        dd('Saya di fungsi INDEX');
        $dataBuku = Buku::all();
        if ($request->ajax()) {
            return view('partials.katalog_isi', compact('dataBuku'))->render();
        }
        return view('katalog', compact('dataBuku'));
    }

    public function katalog(Request $request)
    {
        dd('Saya di fungsi KATALOG');
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
//     public function showKategori($id, Request $request)
// {
//     // 1. Pastikan Kategori ada
//     $kategori = Kategori::findOrFail($id);
    
//     // 2. Ambil kata kunci pencarian
//     $search = $request->input('search');
//     dd('Saya di fungsi SHOW KATEGORI');
//     // 3. Eksekusi Query
//     $dataBuku = Buku::where('id_kategori', $id)
//         ->where(function($query) use ($search) {
//             if (!empty($search)) {
//                 // Parameter Grouping: (judul LIKE %X% OR penulis LIKE %X%)
//                 $query->where('judul', 'like', '%' . $search . '%')
//                     ->orWhere('penulis', 'like', '%' . $search . '%');
//             }
//         })
//         ->get();

//     // 4. Kirim ke View
//     return view('isikategori', compact('kategori', 'dataBuku'));
// }
}
