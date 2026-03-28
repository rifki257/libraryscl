<?php

namespace App\Http\Controllers;

use App\Models\peminjaman;
use Illuminate\Http\Request;
use App\Models\buku;

class KatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    // Filter berdasarkan judul, penulis, atau penerbit
    $bukus = \App\Models\Buku::when($query, function ($q) use ($query) {
    $q->where('judul', 'like', "%{$query}%")
      ->orWhere('penulis', 'like', "%{$query}%")
      ->orWhere('penerbit', 'like', "%{$query}%");
    })->get();

    // Jika request datang dari AJAX (pencarian real-time)
    if ($request->ajax()) {
        return view('partials.katalog_isi', compact('bukus'))->render();
    }

    return view('katalog', compact('bukus'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
