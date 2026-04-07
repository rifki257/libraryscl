<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = \App\Models\Kategori::withCount('buku')->get();

        return view('kategori', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('kategori.buku')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori = \App\Models\Kategori::findOrFail($id);
        $kategori->update([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()->back()->with('success', 'Kategori berhasil diupdate!');
    }

    // app/Http/Controllers/KategoriController.php

    public function destroy($id)
    {
        // Cari kategori berdasarkan ID
        $kategori = \App\Models\Kategori::findOrFail($id);

        // Hapus data kategori tersebut
        $kategori->delete();

        // Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}
