<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    // halaman buku
    public function index(Request $request)
{
    if (auth()->check() && auth()->user()->role === 'anggota') {
        return redirect()->route('katalog')->with('error', 'Akses ditolak!');
    }

    $kategoris = Kategori::all();
    $query = Buku::with('kategori');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
            ->orWhere('penulis', 'like', "%{$search}%");
        });
    }

    if ($request->filled('filter_kategori')) {
        $query->where('id_kategori', $request->filter_kategori);
    }

    $dataBuku = $query->paginate(6)->onEachSide(2);

    if ($request->ajax()) {
        return view('partials.tabel_isi', compact('dataBuku'))->render();
    }

    return view('buku', compact('dataBuku', 'kategoris'));
    }

    // buat buku
    public function create()
    {
        $kategoris = Kategori::all();

        return view('add.bukucreate', compact('kategoris'));
    }

    // create buku sistemnya atau proses
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|max:225',
            'penerbit' => 'required|max:225',
            'penulis' => 'required|max:225',
            'jumlah' => 'required|numeric',
            'id_kategori' => 'required|exists:kategoris,id_kategori',
            'gambar' => 'nullable|image|max:5048',
        ]);

        $data = $request->all();
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('buku', 'public');
        }

        Buku::create($data);
        return redirect()->back()->with('success', 'Buku berhasil ditambah!');
    }

    // detail
    public function show($id)
    {
        $buku = Buku::findOrFail($id);
        return view('detail.bukudetail', compact('buku'));
    }

    // edit buku
    public function edit($id)
    {
        $buku = Buku::findOrFail($id);
        $kategoris = Kategori::all();
        return view('edit.bukuedit', compact('buku','kategoris'));
    }

    // update buku proses
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

    // hapus buku
    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);

        if ($buku->gambar) {
            Storage::disk('public')->delete($buku->gambar);
        }
        $buku->delete();
        return redirect()->route('buku')->with('success', 'Buku berhasil dihapus!');
    }

    // cari buku
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
