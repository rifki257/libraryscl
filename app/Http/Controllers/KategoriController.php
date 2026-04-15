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

    $kategoris = $query->paginate(5)->onEachSide(2);

    if ($request->ajax()) {
        return response()->json([
            'html' => view('admin.table_rows', compact('kategoris'))->render()
        ]);
    }

    return view('kategori', compact('kategoris'));
    }

public function katalog()
{
    $allKategori = Kategori::with(['buku' => function($q) {
        $q->take(5); 
    }])->withCount('buku')->get();

    $topKategori = $allKategori->sortByDesc('buku_count')->take(5);

    return view('katalog', compact('allKategori', 'topKategori'));
}
    // isi katgeori
    public function show($id, Request $request)
{
    $kategori = Kategori::findOrFail($id);
    
    $search = $request->query('search');

    $dataBuku = \App\Models\Buku::where('id_kategori', $id)
        ->where(function($query) use ($search) {
            if (!empty($search)) {
                $query->where('judul', 'like', '%' . $search . '%')
                ->orWhere('penulis', 'like', '%' . $search . '%');
            }
        })
        ->get();

    return view('isikategori', compact('kategori', 'dataBuku'));
}

    // proses buat kategori
    public function store(Request $request)
{
    $request->validate([
        'nama_kategori' => 'required|string|max:255',
        'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg,gif,bmp|max:5120' 
    ]);

    $nama_file = null;

    if ($request->hasFile('gambar')) {
        $file = $request->file('gambar');
        $nama_file = time() . '_' . $file->hashName(); 
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
        'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg,gif,bmp|max:5120'
    ]);

    $kategori->nama_kategori = $request->nama_kategori;

    if ($request->hasFile('gambar')) {
        $file = $request->file('gambar');
        $nama_file = time() . '_' . $file->hashName();
        $file->storeAs('kategori', $nama_file, 'public');

        if ($kategori->gambar && Storage::disk('public')->exists('kategori/' . $kategori->gambar)) {
            Storage::disk('public')->delete('kategori/' . $kategori->gambar);
        }

        $kategori->gambar = $nama_file;
    }

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