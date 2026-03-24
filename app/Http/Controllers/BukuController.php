<?php

namespace App\Http\Controllers;

use App\Models\Buku;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->check() && auth()->user()->role === 'anggota') {
            return redirect()->route('katalog')->with('error', 'Akses ditolak!');
        }

        $dataBuku = Buku::all();
        return view('buku', compact('dataBuku'));
    }
    
    // ... fungsi lainnya otomatis aman


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('add.bukucreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|max:225',
            'penerbit' => 'required|max:225',
            'penulis' => 'required|max:225',
            'jumlah' => 'required|numeric',
            'gambar' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('buku', 'public');
        }
        Buku::create($data);
        return redirect()->route('buku')->with('success', 'Buku berhasil ditambah!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $buku = Buku::findOrFail($id);
        return view('detail.bukudetail', compact('buku'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $buku = Buku::findOrFail($id);
        return view('edit.bukuedit', compact('buku'));
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $buku = Buku::findOrFail($id);

        if ($buku->gambar) {
            Storage::disk('public')->delete($buku->gambar);
        }
        $buku->delete();
        return redirect()->route('buku')->with('success', 'Buku berhasil dihapus!');
    }
    //search
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
