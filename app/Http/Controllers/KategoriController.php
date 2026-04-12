<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KategoriController extends Controller
{
    // data kategori
public function index(Request $request) 
    {
    $query = Kategori::withCount('buku');

    if ($request->has('search') && $request->search != '') {
        $query->where('nama_kategori', 'like', '%' . $request->search . '%');
    }

    $kategoris = $query->paginate(3); 

    if ($request->ajax()) {
        return response()->json([
            'html' => view('admin.table_rows', compact('kategoris'))->render()
        ]);
    }

    return view('kategori', compact('kategoris'));
    }

    // halaman buku user
    public function katalog()
    {
    $kategoris = Kategori::withCount('buku') 
        ->orderBy('buku_count', 'desc')     
        ->take(5)                        
        ->get();

    $dataBuku = Buku::all(); 

    return view('katalog', compact('kategoris', 'dataBuku'));
    }
    
    // isi katgeori
    public function show($id)
    {
    $kategori = Kategori::findOrFail($id);
    $dataBuku = \App\Models\Buku::where('id_kategori', $id)->paginate(6);
    return view('isikategori', compact('kategori', 'dataBuku'));
    }

    // proses buat kategori
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' 
        ]);

        $nama_file = null;

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

    // update
    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('gambar')) {
            if ($kategori->gambar && Storage::disk('public')->exists('kategori/' . $kategori->gambar)) {
                Storage::disk('public')->delete('kategori/' . $kategori->gambar);
            }

            $file = $request->file('gambar');
            $nama_file = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('kategori', $nama_file, 'public');
            
            $kategori->gambar = $nama_file;
        }

        $kategori->nama_kategori = $request->nama_kategori;
        $kategori->save();

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui!');
    }

    // hapus
    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->gambar && Storage::disk('public')->exists('kategori/' . $kategori->gambar)) {
            Storage::disk('public')->delete('kategori/' . $kategori->gambar);
        }

        $kategori->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}