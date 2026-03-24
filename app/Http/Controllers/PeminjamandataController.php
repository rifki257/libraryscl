<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class PeminjamandataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // 1. Tambahkan parameter Request $request di sini
    {
        $status = $request->query('status');

        // 2. Variabel ini sudah didefinisikan
        $isFilteringTerlambat = ($status == 'terlambat' || $status == 'denda');
        $hariIni = \Carbon\Carbon::now()->startOfDay();

        $dipinjam = \App\Models\Peminjaman::with(['user', 'buku'])
            ->whereIn('status', ['dipinjam', 'proses'])
            ->get()
            ->map(function ($item) use ($hariIni) {
                $tglJatuhTempo = \Carbon\Carbon::parse($item->tgl_jatuh_tempo)->startOfDay();
                $item->is_telat = $hariIni->gt($tglJatuhTempo);
                return $item;
            });

        // 3. Masukkan 'isFilteringTerlambat' ke dalam compact agar sampai ke Blade
        return view('peminjamandata', compact('dipinjam', 'isFilteringTerlambat'));
    }

    /**
     * Fungsi untuk Admin menyetujui pengembalian buku
     */
    public function konfirmasi_kembali(Request $request, $id)
    {
        $peminjaman = \App\Models\Peminjaman::findOrFail($id);

        $tglJatuhTempo = \Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo)->startOfDay();
        $hariIni = \Carbon\Carbon::now()->startOfDay();
        $selisihRaw = $hariIni->diffInDays($tglJatuhTempo, false);

        $isTelat = $selisihRaw < 0;
        $jumlahHariTelat = $isTelat ? abs($selisihRaw) : 0;
        $totalDenda = $jumlahHariTelat * 150000;

        try {
            \DB::transaction(function () use ($peminjaman, $totalDenda, $isTelat) {

                \App\Models\Pengembalian::create([
                    'id_pinjam'   => $peminjaman->id_pinjam,
                    'id_buku'     => $peminjaman->id_buku,
                    'id'          => $peminjaman->id,
                    'tgl_pinjam'  => $peminjaman->tgl_pinjam,
                    'tgl_kembali' => now(),
                    'denda'       => $totalDenda,
                ]);

                if ($isTelat) {
                    \DB::table('denda')->insert([
                        'jumlah_denda' => (string) $totalDenda,
                        'id'           => $peminjaman->id,
                        'id_buku'      => $peminjaman->id_buku,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }

                \App\Models\Buku::where('id_buku', $peminjaman->id_buku)->increment('jumlah');

                $peminjaman->delete();
            });

            return redirect()->back()->with('success', 'Berhasil konfirmasi! Data telah dipindahkan ke tabel pengembalian.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal konfirmasi: ' . $e->getMessage());
        }
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
