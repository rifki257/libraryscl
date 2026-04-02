<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use Illuminate\Support\Facades\DB;
use App\Models\Peminjaman;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sedangDipinjam = Peminjaman::where('id', auth()->id())
            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat'])
            ->with('buku')
            ->get();

        // Hitung total fisik buku, bukan jumlah baris transaksi
        $totalFisikBuku = $sedangDipinjam->sum('total_pinjam');

        return view('peminjaman.index', compact('sedangDipinjam', 'totalFisikBuku'));
    }

    // Cari fungsi yang merujuk ke rute /admin/persetujuan
    public function persetujuan()
    {
        // Untuk Dashboard Admin agar badge count tidak error
        $semuaPeminjam = Peminjaman::all();
        $peminjamanPending = Peminjaman::where('status', 'pending')->get();

        return view('admin_persetujuan', compact('semuaPeminjam', 'peminjamanPending'));
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

    public function setujuiPinjam($id_peminjaman)
    {
        $pinjam = Peminjaman::findOrFail($id_peminjaman);

        // Update status saja ke 'dipinjam'
        $pinjam->update([
            'status' => 'dipinjam'
        ]);

        // PASTIKAN BARIS DI BAWAH INI SUDAH DIHAPUS/KOMENTAR:
        // $pinjam->buku->decrement('jumlah', $pinjam->total_pinjam); 

        return back()->with('success', 'Peminjaman disetujui.');
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

    public function pinjam($id)
    {
        $buku = Buku::findOrFail($id);

        if ($buku->jumlah <= 0) {
            return redirect()->back()->with('error', 'Maaf, jumlah buku sudah habis!');
        }
    }

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
}
