<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengembalian;

class UserpengembalianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    // Di dalam UserpengembalianController.php
    public function history()
    {
        // 1. Ambil ID user yang sedang login
        $userId = auth()->id();

        // 2. Ambil data dari model Pengembalian (bukan Peminjaman)
        // Gunakan 'id' sebagai foreign key sesuai foto database Anda
        $riwayatSelesai = Pengembalian::with(['buku'])
            ->where('id', $userId)
            ->latest()
            ->get();

        // 3. Kirim variabel $riwayatSelesai ke view 'mybalik'
        return view('mybalik', compact('riwayatSelesai'));
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
