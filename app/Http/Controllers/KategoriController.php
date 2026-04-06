<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        // Mengambil semua data dari tabel kategoris
        $kategoris = \App\Models\Kategori::all();

        return view('kategori', compact('kategoris'));
    }
    
    public function store(Request $request)
    {
        // 1. Validasi input (nama_kategori harus sama dengan nama di <input>)
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        // 2. Simpan ke database manual via Model
        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        // 3. Kembalikan ke halaman sebelumnya dengan pesan sukses
        return redirect()->route('kategori.buku')->with('success', 'Kategori berhasil ditambahkan!');
    }
}
