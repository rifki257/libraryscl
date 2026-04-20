<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Wishlist;

class PeminjamanController extends Controller
{
    // peminjamnan
    public function index()
    {
        $semuaPeminjaman = Peminjaman::with(['buku', 'user'])
            ->whereIn('status', ['dipinjam', 'kembali', 'ditolak'])
            ->latest()
            ->get();

        return view('partials.pinjam_data', compact('semuaPeminjaman'));
    }

    // peminjaman table
    public function peminjamanData(Request $request)
    {
    $query = Peminjaman::with(['buku', 'user'])
        ->whereIn('status', ['dipinjam', 'kembali', 'ditolak']);

    // Logika Pencarian
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('user', function($u) use ($search) {
                $u->where('name', 'like', '%' . $search . '%');
            })->orWhereHas('buku', function($b) use ($search) {
                $b->where('judul', 'like', '%' . $search . '%');
            })->orWhere('id_pinjam', 'like', '%' . $search . '%');
        });

        $semuaPeminjaman = $query->latest()->get();
    } else {
        $semuaPeminjaman = $query->latest()->paginate(5)->onEachSide(2);
    }

    if ($request->ajax()) {
        return view('admin.table_peminjaman_rows', compact('semuaPeminjaman'))->render();
    }

    return view('partials.pinjam_data', compact('semuaPeminjaman'));
    }


    // proses minjam kayaknya
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
            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat', 'menunggu', 'ajukan_kembali'])
            ->count();

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

    // user peminjaman
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

    // user ajukan penembalian
    public function ajukanKembali($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update([
            'status' => 'ajukan_kembali'
        ]);

        return redirect()->back()->with('success', 'Pengembalian diajukan ke admin.');
    }

    // konfir kembali admin
    public function konfirmasi_kembali(Request $request, $id)
    {
        $peminjaman = \App\Models\Peminjaman::findOrFail($id);

        $peminjaman->update([
            'status' => 'kembali', 
            'tgl_kembali_aktual' => now(), 
        ]);

        if ($peminjaman->book) {
            $peminjaman->book->increment('stok');
        }

        return redirect()->back()->with('success', 'Buku berhasil dikonfirmasi dan stok telah diperbarui.');
    }

    // histori user
    public function history()
    {
        $userId = auth()->id();

        $sedangDipinjam = Peminjaman::with('buku')
            ->where('id', $userId)
            ->whereIn('status', ['menunggu', 'dipinjam', 'ajukan_kembali', 'proses'])
            ->latest()
            ->get();

        $riwayatSelesai = Peminjaman::with('buku')
            ->where('id', $userId)
            ->where('status', 'kembali')
            ->latest()
            ->get();

        return view('mypinjaman', compact('sedangDipinjam', 'riwayatSelesai'));
    }

    // hapus
    public function destroy($id)
    {
        $peminjaman = Peminjaman::where('id_pinjam', $id)->firstOrFail();

        if ($peminjaman->status == 'menunggu') {
            DB::transaction(function () use ($peminjaman) {
                if ($peminjaman->buku) {
                    $peminjaman->buku->increment('jumlah'); 
                }
                $peminjaman->delete(); 
            });
            return redirect()->back()->with('success', 'Peminjaman dibatalkan.');
        }
        return redirect()->back()->with('error', 'Tidak bisa membatalkan buku yang sudah dipinjam.');
    }

    // minjam banyak
    public function storeMasal(Request $request) {
    $ids = (array) $request->id_buku;
    
    if (Auth::user()->denda > 0) {
        return response()->json(['message' => 'Selesaikan denda Anda dulu!'], 403);
    }

    try {
        DB::transaction(function () use ($ids) {
            foreach ($ids as $id) {
                $buku = Buku::lockForUpdate()->find($id); 
                
                if ($buku && $buku->jumlah > 0) {
                    Peminjaman::create([
                        'id' => Auth::id(),
                        'id_buku' => $id,
                        'tgl_pinjam' => now(),
                        'tgl_jatuh_tempo' => now()->addDays(30),
                        'status' => 'menunggu'
                    ]);
                    
                    $buku->decrement('jumlah');

                    \App\Models\Wishlist::where('id', Auth::id())
                        ->where('id_buku', $id)
                        ->delete();
                }
            }
        });

        return redirect()->back()->with('success', 'Buku berhasil dipinjam dan daftar wishlist diperbarui!');
        
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
    }
}

    // tolak peminjaman
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

    // stujui pinjam
    public function setujuiPinjam($id)
    {
        $pinjam = Peminjaman::where('id_pinjam', $id)->firstOrFail();
        $pinjam->update([
            'status'     => 'dipinjam',
            'tgl_pinjam' => now(),
        ]);

        return redirect()->back()->with('success', 'Peminjaman disetujui!');
    }

    // laporan
    public function halamanLaporan(Request $request)
    {
    $query = Peminjaman::with(['user', 'buku']);
    $tgl_mulai = $request->tgl_mulai;
    $tgl_selesai = $request->tgl_selesai;
    $jenis = $request->get('jenis', 'peminjaman');

    if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
        $query->whereBetween('tgl_pinjam', [$tgl_mulai, $tgl_selesai]);
    } else {
        $query->whereMonth('tgl_pinjam', now()->month)->whereYear('tgl_pinjam', now()->year);
    }

    if ($jenis == 'pengembalian') {
        $query->where('status', 'kembali');
    }

    $semuaPeminjaman = $query->latest()->paginate(10)->onEachSide(2);

    return view('admin.laporan', compact('semuaPeminjaman', 'tgl_mulai', 'tgl_selesai', 'jenis'));
    }

    // ekspor ke word
    public function exportWord(Request $request)
    {
    $jenis = $request->jenis;
    $tgl_mulai = $request->tgl_mulai;
    $tgl_selesai = $request->tgl_selesai;

    $query = Peminjaman::with(['user', 'buku']);

    if ($tgl_mulai && $tgl_selesai) {
        $query->whereBetween('tgl_pinjam', [$tgl_mulai, $tgl_selesai]);
        $periode = \Carbon\Carbon::parse($tgl_mulai)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($tgl_selesai)->format('d M Y');
    } else {
        $query->whereMonth('tgl_pinjam', now()->month)->whereYear('tgl_pinjam', now()->year);
        $periode = now()->translatedFormat('F Y');
    }

    if ($jenis == 'pengembalian') {
        $query->where('status', 'kembali');
    }

    $data = $query->get();
    $totalDenda = $data->sum('denda');

    $filename = "Laporan_" . ucfirst($jenis) . ".doc";

    return response()->view('admin.export_word', compact('data', 'jenis', 'periode', 'totalDenda', 'tgl_mulai', 'tgl_selesai'))
        ->header('Content-Type', 'application/msword')
        ->header('Content-Disposition', "attachment; filename=$filename");
    }

    // laporan user
    public function laporanUser(Request $request)
    {
    $id = auth()->id();
    
    $query = \App\Models\Peminjaman::with('buku')
                ->where('id', $id) 
                ->orderBy('created_at', 'desc');

    $query->when($request->status, function ($q) use ($request) {
        return $q->where('status', $request->status);
    });

    $riwayat = $query->paginate(10)->onEachSide(2);

    return view('laporan_user', compact('riwayat'));
    }
}
