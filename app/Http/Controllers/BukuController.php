<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    public function index(Request $request)
{
    if (auth()->check() && auth()->user()->role === 'anggota') {
        return redirect()->route('katalog')->with('error', 'Akses ditolak!');
    }

    $kategoris = Kategori::all();
    $query = Buku::with('kategori');

    // Filter Search (Judul & Penulis)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('penulis', 'like', "%{$search}%");
        });
    }

    // Filter Kategori
    if ($request->filled('filter_kategori')) {
        $query->where('id_kategori', $request->filter_kategori);
    }

    $dataBuku = $query->paginate(6)->withQueryString();

    // JIKA REQUEST ADALAH AJAX, kembalikan hanya tabel isinya saja
    if ($request->ajax()) {
        return view('partials.tabel_isi', compact('dataBuku'))->render();
    }

    return view('buku', compact('dataBuku', 'kategoris'));
}

    public function create()
    {
        // 1. Ambil semua data kategori dari tabel kategoris
        $kategoris = Kategori::all();

        // 2. Kirim data tersebut ke view menggunakan compact
        return view('add.bukucreate', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|max:225',
            'penerbit' => 'required|max:225',
            'penulis' => 'required|max:225',
            'jumlah' => 'required|numeric',
            // Tambahkan validasi untuk id_kategori agar tidak error SQL nantinya
            'id_kategori' => 'required|exists:kategoris,id_kategori',
            'gambar' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('buku', 'public');
        }

        Buku::create($data);
        return redirect()->route('buku')->with('success', 'Buku berhasil ditambah!');
    }

    public function show($id)
    {
        $buku = Buku::findOrFail($id);
        return view('detail.bukudetail', compact('buku'));
    }

    public function edit($id)
    {
        $buku = Buku::findOrFail($id);
        $kategoris = Kategori::all();
        return view('edit.bukuedit', compact('buku','kategoris'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|max:225',
            'penerbit' => 'required|max:225',
            'penulis' => 'required|max:225',
            'jumlah' => 'required|numeric',
            'gambar' => 'nullable|image|max:2048',
        ]);
        $buku = Buku::findOrFail($id);
        $data = $request->all();
        if ($request->hasFile('gambar')) {
            if ($buku->gambar) {
                Storage::disk('public')->delete($buku->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('buku', 'public');
        }
        $buku->update($data);
        return redirect()->route('buku')->with('success', 'Buku berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);

        if ($buku->gambar) {
            Storage::disk('public')->delete($buku->gambar);
        }
        $buku->delete();
        return redirect()->route('buku')->with('success', 'Buku berhasil dihapus!');
    }

    public function search(Request $request)
    {
        $search = $request->query('search');
        $dataBuku = Buku::where('judul', 'like', "%{$search}%")
            ->orWhere('penulis', 'like', "%{$search}%")
            ->get();
        if ($request->ajax()) {
            return view('partials.tabel_isi', compact('dataBuku'))->render();
        }
        return view('buku', compact('dataBuku'));
    }
}
