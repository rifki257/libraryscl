<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use Illuminate\Support\Facades\DB;
use App\Models\Peminjaman;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id = null)
    {
        if (!$id) {
            return redirect()->route('katalog');
        }

        $buku = Buku::findOrFail($id);

        return view('peminjaman', compact('buku'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_buku' => 'required',
            'tgl_kembali' => 'required|date',
        ]);

        $buku = \App\Models\Buku::where('id_buku', $request->id_buku)->first();

        // 1. Cek ketersediaan stok
        if (!$buku || $buku->jumlah <= 0) {
            return redirect()->back()->with('error', 'Maaf, stok buku ini sedang habis.');
        }

        // 2. Cek limit peminjaman (termasuk yang masih pending)
        $jumlahPinjam = \DB::table('peminjaman')
            ->where('id', auth()->id())
            ->whereIn('status', ['pending', 'dipinjam']) // Hitung yang sedang diajukan & sedang dipinjam
            ->count();

        if ($jumlahPinjam >= 3) {
            return redirect()->back()->with('error', 'Gagal! Limit pinjam tercapai (Maksimal 3 buku termasuk yang menunggu konfirmasi).');
        }

        try {
            \App\Models\Peminjaman::create([
                'id_buku'         => $request->id_buku,
                'id'              => auth()->id(),
                'tgl_pinjam'      => now(),
                'tgl_jatuh_tempo' => $request->tgl_kembali,
                'status'          => 'pending', // Status awal menjadi pending
                'denda'           => 0,
            ]);

            // Catatan: Kita TIDAK melakukan decrement stok di sini. 
            // Stok dikurangi nanti di method konfirmasi milik Admin.

            return redirect()->route('katalog')->with('success', 'Permintaan pinjam berhasil dikirim. Silakan tunggu konfirmasi Admin.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }


    public function setujuiPinjam($id_peminjaman)
    {
        $pinjam = Peminjaman::find($id_peminjaman);

        // Kurangi stok saat admin setuju
        \App\Models\Buku::where('id_buku', $pinjam->id_buku)->decrement('jumlah');

        $pinjam->update(['status' => 'dipinjam']);

        return back()->with('success', 'Peminjaman disetujui dan stok telah diperbarui.');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function myPinjaman(Request $request)
    {
        $sedangDipinjam = Peminjaman::where('id_user', auth()->id())
            ->whereIn('status', ['dipinjam', 'proses'])
            ->get();

        $riwayatSelesai = Peminjaman::where('id_user', auth()->id())
            ->where('status', 'kembali')
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'html_aktif' => view('partials.pinjaman_aktif', compact('sedangDipinjam'))->render(),
                'html_riwayat' => view('partials.kembalikan', compact('riwayatSelesai'))->render(),
            ]);
        }

        return view('mypinjaman', compact('sedangDipinjam', 'riwayatSelesai'));
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

    public function history()
    {
        $userId = auth()->id();

        $sedangDipinjam = \App\Models\Peminjaman::with('buku')
            ->where('id', $userId)
            ->whereIn('status', ['dipinjam', 'proses'])
            ->get();

        $riwayatSelesai = \App\Models\Peminjaman::with('buku')
            ->where('id', $userId)
            ->where('status', 'kembali')
            ->get();

        return view('mypinjaman', compact('sedangDipinjam', 'riwayatSelesai'));
    }

    public function ajukan_kembali(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $peminjaman->update([
            'status' => 'proses',
            'denda' => $request->denda,
        ]);

        return redirect()->back()->with('success', 'Pengembalian diajukan. Silakan kembalikan buku ke perpustakaan.');
    }

    public function pinjam($id)
    {
        $buku = Buku::findOrFail($id);

        if ($buku->jumlah <= 0) {
            return redirect()->back()->with('error', 'Maaf, stok buku sudah habis!');
        }
    }
    public function ajukanKembali(Request $request, $id)
    {
        $peminjaman = \App\Models\Peminjaman::findOrFail($id);
        $peminjaman->update([
            'status' => 'proses',
            'denda'  => $request->denda ?? 0
        ]);

        return redirect()->back()->with('success', 'Berhasil mengajukan pengembalian. Silakan temui petugas perpustakaan.');
    }
}
