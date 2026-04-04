<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use Illuminate\Support\Facades\DB;
use App\Models\Peminjaman;
use Carbon\Carbon;
use App\Models\Wishlist;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // Mengambil semua yang statusnya SUDAH diproses (dipinjam, kembali, ditolak)
    $semuaPeminjaman = Peminjaman::with(['buku', 'user'])
        ->whereIn('status', ['dipinjam', 'kembali', 'ditolak'])
        ->latest()
        ->get();

    return view('partials.pinjam_data', compact('semuaPeminjaman'));
}

    public function persetujuan()
    {
        $semuaPeminjaman = Peminjaman::with('buku', 'user')
            ->where('status', 'pending') // Filter khusus pengajuan baru
            ->latest()
            ->get();

        return view('admin_persetujuan', compact('semuaPeminjaman'));
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

        // LOGIKA: Gunakan 'total_pinjam' sesuai kolom baru di database kamu
        $totalBukuAktif = DB::table('peminjaman')
            ->where('id', auth()->id())
            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat'])
            ->sum('total_pinjam') ?? 0;

        return view('peminjaman', compact('buku', 'totalBukuAktif'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_buku' => 'required',
            'tgl_kembali' => 'required|date',
            'total_pinjam' => 'required|integer|min:1',
        ]);

        $limitMaksimal = 6;
        $buku = Buku::where('id_buku', $request->id_buku)->first();

        $totalBukuAktif = DB::table('peminjaman')
            ->where('id', auth()->id())
            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat'])
            ->sum('total_pinjam') ?? 0;

        $slotTersedia = $limitMaksimal - $totalBukuAktif;

        if ($totalBukuAktif >= $limitMaksimal) {
            return redirect()->back()->with('error', 'Gagal! Kamu sudah mencapai limit peminjaman 6 buku.');
        }

        if ($request->total_pinjam > $slotTersedia) {
            return redirect()->back()->with('error', 'Gagal! Kamu hanya bisa meminjam ' . $slotTersedia . ' buku lagi.');
        }

        if (!$buku || $buku->jumlah < $request->total_pinjam) {
            return redirect()->back()->with('error', 'Maaf, stok tidak mencukupi. Sisa: ' . ($buku->jumlah ?? 0));
        }

        try {
            // Menggunakan transaksi agar data peminjaman dan pengurangan stok aman
            DB::transaction(function () use ($request, $buku) {
                // 1. Buat data peminjaman
                Peminjaman::create([
                    'id_buku' => $request->id_buku,
                    'id' => auth()->id(),
                    'tgl_pinjam' => now(),
                    'tgl_jatuh_tempo' => $request->tgl_kembali,
                    'status' => 'pending',
                    'total_pinjam' => $request->total_pinjam,
                    'denda' => 0,
                ]);

                // 2. Kurangi stok buku sesuai jumlah yang dipinjam
                $buku->decrement('jumlah', $request->total_pinjam);
            });

            return redirect()->route('katalog')->with('success', 'Berhasil! Permintaan pinjam dikirim.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Sistem error: ' . $e->getMessage());
        }
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
    /**
     * Menghapus/Membatalkan pengajuan peminjaman.
     */



    public function ajukanKembali($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // User hanya mengubah status menjadi 'proses'
        $peminjaman->update([
            'status' => 'proses'
        ]);

        return redirect()->back()->with('success', 'Pengembalian berhasil diajukan. Tunggu konfirmasi admin.');
    }

    public function konfirmasi_kembali($id_pinjam)
    {
        // Menggunakan DB transaction agar kedua proses (update & stok) harus berhasil semua atau gagal semua
        return DB::transaction(function () use ($id_pinjam) {
            $peminjaman = Peminjaman::where('id_pinjam', $id_pinjam)->firstOrFail();

            // 1. Update status peminjaman
            $peminjaman->update([
                'status' => 'kembali',
                'tgl_kembali' => Carbon::now()->toDateString(),
            ]);

            // 2. Kembalikan stok buku sesuai 'total_pinjam'
            if ($peminjaman->buku) {
                // Ini akan menambah stok sejumlah angka di kolom total_pinjam
                $peminjaman->buku->increment('jumlah', $peminjaman->total_pinjam);
            }

            return redirect()->back()->with('success', 'Buku berhasil dikembalikan!');
        });
    }
    // ... inside PeminjamanController class

    public function history()
    {
        $userId = auth()->id();

        // Mengambil buku dengan status pending, dipinjam, atau proses
        $sedangDipinjam = \App\Models\Peminjaman::with('buku')
            ->where('id', $userId)
            ->whereIn('status', ['pending', 'dipinjam', 'proses'])
            ->get();

        $riwayatSelesai = \App\Models\Peminjaman::with('buku')
            ->where('id', $userId)
            ->where('status', 'kembali')
            ->get();

        return view('mypinjaman', compact('sedangDipinjam', 'riwayatSelesai'));
    }

    public function destroy($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        // Pastikan hanya bisa dibatalkan jika masih pending
        if ($peminjaman->status == 'pending') {

            DB::transaction(function () use ($peminjaman) {
                // KEMBALIKAN STOK karena batal pinjam
                if ($peminjaman->buku) {
                    $peminjaman->buku->increment('jumlah', $peminjaman->total_pinjam);
                }

                // Ubah status atau hapus datanya
                $peminjaman->update([
                    'status' => 'dibatalkan'
                ]);
            });

            return redirect()->back()->with('success', 'Peminjaman berhasil dibatalkan dan stok dikembalikan.');
        }

        return redirect()->back()->with('error', 'Gagal membatalkan. Status buku sudah berubah.');
    }

    // ... di dalam class PeminjamanController

    public function pinjam($id)
    {
        // 1. Ambil data satu buku
        $buku_tunggal = Buku::findOrFail($id);

        if ($buku_tunggal->jumlah <= 0) {
            return redirect()->back()->with('error', 'Maaf, stok buku sedang habis!');
        }

        // 2. Bungkus ke Collection agar @forelse ($books as $buku) di Blade tidak error
        $books = collect([$buku_tunggal]);

        // 3. Hitung total buku aktif (Gunakan kolom 'id' sesuai fungsi store kamu)
        $totalBukuAktif = Peminjaman::where('id', auth()->id())
            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat'])
            ->sum('total_pinjam') ?? 0;

        // 4. Kirim ke view 'peminjaman' (sesuaikan nama file blade kamu)
        return view('peminjaman', compact('books', 'totalBukuAktif'));
    }

    public function createMasal(Request $request)
    {
        $idsString = $request->query('ids');

        if (!$idsString) {
            return redirect()->route('dashboard')->with('error', 'Pilih buku terlebih dahulu.');
        }

        $idsArray = explode(',', $idsString);

        // Pastikan variabel bernama $books
        $books = Buku::whereIn('id_buku', $idsArray)->get();

        // Pastikan kolom 'id' konsisten dengan tabel peminjaman kamu
        $totalBukuAktif = Peminjaman::where('id', auth()->id())
            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat'])
            ->sum('total_pinjam') ?? 0;

        // Sesuaikan nama view dengan file konfirmasi masal kamu
        return view('peminjaman', compact('books', 'totalBukuAktif'));
    }

    public function storeMasal(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'id_buku' => 'required|array',
            'total_pinjam' => 'required|array',
            'tgl_kembali' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $userId = auth()->id();
                $tgl_kembali = $request->tgl_kembali;

                // Looping setiap buku yang dipilih
                foreach ($request->id_buku as $key => $id_buku) {
                    $jumlah_pinjam = $request->total_pinjam[$key];
                    $buku = Buku::findOrFail($id_buku);

                    // A. Simpan ke tabel Peminjaman
                    Peminjaman::create([
                        'id_buku' => $id_buku,
                        'id' => $userId, // Kolom user sesuai database kamu
                        'tgl_pinjam' => now(),
                        'tgl_jatuh_tempo' => $tgl_kembali,
                        'status' => 'pending',
                        'total_pinjam' => $jumlah_pinjam,
                        'denda' => 0,
                    ]);

                    // B. Kurangi Stok Buku
                    $buku->decrement('jumlah', $jumlah_pinjam);

                    // C. Hapus dari Wishlist (Hanya buku yang dipinjam saja)
                    \App\Models\Wishlist::where('id', $userId)
                        ->where('id_buku', $id_buku)
                        ->delete();
                }
            });

            return redirect()->route('katalog')->with('success', 'Peminjaman berhasil diajukan dan wishlist diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function tolakPinjam($id)
    {
        return DB::transaction(function () use ($id) {
            $pinjam = Peminjaman::findOrFail($id);

            // 1. Kembalikan stok buku
            if ($pinjam->buku) {
                $pinjam->buku->increment('jumlah', $pinjam->total_pinjam);
            }

            // 2. Update status jadi ditolak atau hapus
            $pinjam->update(['status' => 'ditolak']);

            return back()->with('success', 'Peminjaman ditolak dan stok dikembalikan.');
        });
    }

    public function setujuiPinjam($id) 
{
    // 1. Cari data berdasarkan id_pinjam (karena primary key kamu bukan 'id')
    $pinjam = Peminjaman::where('id_pinjam', $id)->first();

    // Cek apakah data ketemu. Jika muncul layar hitam saat klik, berarti data tidak ketemu
    if (!$pinjam) {
        return "Data dengan ID $id tidak ditemukan di database!";
    }

    // 2. Update status & pastikan tgl_pinjam terisi saat disetujui
    $pinjam->update([
        'status'     => 'dipinjam',
        'tgl_pinjam' => now(), // Opsional: mengisi tanggal saat admin klik setujui
    ]);

    // 3. Redirect ke halaman data pinjam untuk melihat hasilnya
    return redirect()->route('admin.index')->with('success', 'Peminjaman disetujui!');
}
}
