<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KategoriController extends Controller
{
   // Tambahkan Request di sini
public function index(Request $request) 
{
    $query = Kategori::withCount('buku');

    // Sekarang $request sudah terdefinisi dan bisa digunakan
    if ($request->has('search') && $request->search != '') {
        $query->where('nama_kategori', 'like', '%' . $request->search . '%');
    }

    $kategoris = $query->paginate(3); // Gunakan paginate sesuai kebutuhan

    // Cek jika permintaan datang dari AJAX (Live Search)
    if ($request->ajax()) {
        return response()->json([
            'html' => view('admin.table_rows', compact('kategoris'))->render()
        ]);
    }

    return view('kategori', compact('kategoris'));
}


    public function katalog()
{
    // Ambil 5 kategori dengan jumlah buku terbanyak
    $kategoris = Kategori::withCount('buku') // Asumsi relasi di model Kategori bernama 'buku'
        ->orderBy('buku_count', 'desc')     
        ->take(5)                        
        ->get();

    $dataBuku = Buku::all(); 

    return view('katalog', compact('kategoris', 'dataBuku'));
}
    
public function show($id)
{
    $kategori = Kategori::findOrFail($id);
    $dataBuku = \App\Models\Buku::where('id_kategori', $id)->paginate(6);
    return view('isikategori', compact('kategori', 'dataBuku'));
}

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Validasi gambar
        ]);

        $nama_file = null;

        // Logika simpan gambar untuk Create
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $nama_file = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('kategori', $nama_file, 'public');
        }

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
            'gambar' => $nama_file,
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada di storage
            if ($kategori->gambar && Storage::disk('public')->exists('kategori/' . $kategori->gambar)) {
                Storage::disk('public')->delete('kategori/' . $kategori->gambar);
            }

            // Simpan gambar baru
            $file = $request->file('gambar');
            $nama_file = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('kategori', $nama_file, 'public');
            
            $kategori->gambar = $nama_file;
        }

        $kategori->nama_kategori = $request->nama_kategori;
        $kategori->save();

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);

        // Hapus file gambar dari storage saat data dihapus
        if ($kategori->gambar && Storage::disk('public')->exists('kategori/' . $kategori->gambar)) {
            Storage::disk('public')->delete('kategori/' . $kategori->gambar);
        }

        $kategori->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}