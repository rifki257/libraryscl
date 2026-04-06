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
    public function index()
{
    $semuaPeminjaman = Peminjaman::with(['buku', 'user'])
        ->whereIn('status', ['dipinjam', 'kembali', 'ditolak'])
        ->latest()
        ->get();

    return view('partials.pinjam_data', compact('semuaPeminjaman'));
}

    public function persetujuan()
    {
        $semuaPeminjaman = Peminjaman::with('buku', 'user')
            ->where('status', 'pending') 
            ->latest()
            ->get();

        return view('admin_persetujuan', compact('semuaPeminjaman'));
    }

    public function create($id = null)
    {
        if (!$id) {
            return redirect()->route('katalog');
        }
        $buku = Buku::findOrFail($id);
        $totalBukuAktif = DB::table('peminjaman')
            ->where('id', auth()->id())
            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat'])
            ->sum('total_pinjam') ?? 0;

        return view('peminjaman', compact('buku', 'totalBukuAktif'));
    }

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
            DB::transaction(function () use ($request, $buku) {
                Peminjaman::create([
                    'id_buku' => $request->id_buku,
                    'id' => auth()->id(),
                    'tgl_pinjam' => now(),
                    'tgl_jatuh_tempo' => $request->tgl_kembali,
                    'status' => 'pending',
                    'total_pinjam' => $request->total_pinjam,
                    'denda' => 0,
                ]);
                $buku->decrement('jumlah', $request->total_pinjam);
            });

            return redirect()->route('katalog')->with('success', 'Berhasil! Permintaan pinjam dikirim.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Sistem error: ' . $e->getMessage());
        }
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

    public function ajukanKembali($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $peminjaman->update([
            'status' => 'proses'
        ]);

        return redirect()->back()->with('success', 'Pengembalian berhasil diajukan. Tunggu konfirmasi admin.');
    }

    public function konfirmasi_kembali($id_pinjam)
    {
        return DB::transaction(function () use ($id_pinjam) {
            $peminjaman = Peminjaman::where('id_pinjam', $id_pinjam)->firstOrFail();

            $peminjaman->update([
                'status' => 'kembali',
                'tgl_kembali' => Carbon::now()->toDateString(),
            ]);
            if ($peminjaman->buku) {
                $peminjaman->buku->increment('jumlah', $peminjaman->total_pinjam);
            }

            return redirect()->back()->with('success', 'Buku berhasil dikembalikan!');
        });
    }

    public function history()
    {
        $userId = auth()->id();

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

        if ($peminjaman->status == 'pending') {

            DB::transaction(function () use ($peminjaman) {
                if ($peminjaman->buku) {
                    $peminjaman->buku->increment('jumlah', $peminjaman->total_pinjam);
                }
                $peminjaman->update([
                    'status' => 'dibatalkan'
                ]);
            });
            return redirect()->back()->with('success', 'Peminjaman berhasil dibatalkan dan stok dikembalikan.');
        }
        return redirect()->back()->with('error', 'Gagal membatalkan. Status buku sudah berubah.');
    }


    public function pinjam($id)
    {
        $buku_tunggal = Buku::findOrFail($id);

        if ($buku_tunggal->jumlah <= 0) {
            return redirect()->back()->with('error', 'Maaf, stok buku sedang habis!');
        }
        $books = collect([$buku_tunggal]);
        $totalBukuAktif = Peminjaman::where('id', auth()->id())
            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat'])
            ->sum('total_pinjam') ?? 0;
        return view('peminjaman', compact('books', 'totalBukuAktif'));
    }

    public function createMasal(Request $request)
    {
        $idsString = $request->query('ids');

        if (!$idsString) {
            return redirect()->route('dashboard')->with('error', 'Pilih buku terlebih dahulu.');
        }
        $idsArray = explode(',', $idsString);
        $books = Buku::whereIn('id_buku', $idsArray)->get();
        $totalBukuAktif = Peminjaman::where('id', auth()->id())
            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat'])
            ->sum('total_pinjam') ?? 0;
        return view('peminjaman', compact('books', 'totalBukuAktif'));
    }

    public function storeMasal(Request $request)
    {
        $request->validate([
            'id_buku' => 'required|array',
            'total_pinjam' => 'required|array',
            'tgl_kembali' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $userId = auth()->id();
                $tgl_kembali = $request->tgl_kembali;
                foreach ($request->id_buku as $key => $id_buku) {
                    $jumlah_pinjam = $request->total_pinjam[$key];
                    $buku = Buku::findOrFail($id_buku);
                    Peminjaman::create([
                        'id_buku' => $id_buku,
                        'id' => $userId,
                        'tgl_pinjam' => now(),
                        'tgl_jatuh_tempo' => $tgl_kembali,
                        'status' => 'pending',
                        'total_pinjam' => $jumlah_pinjam,
                        'denda' => 0,
                    ]);
                    $buku->decrement('jumlah', $jumlah_pinjam);
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

            if ($pinjam->buku) {
                $pinjam->buku->increment('jumlah', $pinjam->total_pinjam);
            }

            $pinjam->update(['status' => 'ditolak']);

            return back()->with('success', 'Peminjaman ditolak dan stok dikembalikan.');
        });
    }

    public function setujuiPinjam($id) 
{
    $pinjam = Peminjaman::where('id_pinjam', $id)->first();
    if (!$pinjam) {
        return "Data dengan ID $id tidak ditemukan di database!";
    }
    $pinjam->update([
        'status'     => 'dipinjam',
        'tgl_pinjam' => now(),
    ]);

    return redirect()->route('index')->with('success', 'Peminjaman disetujui!');
}
}
