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
    public function index()
    {
        $semuaPeminjaman = Peminjaman::with(['buku', 'user'])
            ->whereIn('status', ['dipinjam', 'kembali', 'ditolak'])
            ->latest()
            ->get();

        return view('partials.pinjam_data', compact('semuaPeminjaman'));
    }

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

        // Jika search, ambil semua (tanpa paginate)
        $semuaPeminjaman = $query->latest()->get();
    } else {
        // Jika normal, gunakan paginate
        $semuaPeminjaman = $query->latest()->paginate(5);
    }

    // Jika request dari AJAX (Live Search)
    if ($request->ajax()) {
        return view('admin.table_peminjaman_rows', compact('semuaPeminjaman'))->render();
    }

    return view('partials.pinjam_data', compact('semuaPeminjaman'));
}
    

    public function create($id = null)
    {
        if (!$id) {
            return redirect()->route('katalog');
        }
        $buku = Buku::findOrFail($id);
        $totalBukuAktif = DB::table('peminjaman')
            ->where('id', auth()->id())
            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat', 'menunggu'])
            ->count(); // Gunakan count() karena kolom total_pinjam sudah tidak ada

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
            'status' => 'ajukan_kembali' // Ubah dari 'proses' ke 'ajukan_kembali' agar sinkron
        ]);

        return redirect()->back()->with('success', 'Pengembalian diajukan ke admin.');
    }

    public function konfirmasi_kembali(Request $request, $id)
    {
        // 1. Cari data peminjaman berdasarkan ID
        $peminjaman = \App\Models\Peminjaman::findOrFail($id);

        // 2. Update status menjadi 'kembali' (atau sesuai status di database kamu)
        $peminjaman->update([
            'status' => 'kembali', // Ubah ke status final setelah buku diterima admin
            'tgl_kembali_aktual' => now(), // Opsional: jika ada kolom tanggal kembali asli
        ]);

        // 3. Update stok buku (tambah 1 karena buku sudah balik)
        // Pastikan relasi 'book' sudah benar di Model Peminjaman
        if ($peminjaman->book) {
            $peminjaman->book->increment('stok');
        }

        // 4. Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Buku berhasil dikonfirmasi dan stok telah diperbarui.');
    }

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

    public function destroy($id)
    {
        $peminjaman = Peminjaman::where('id_pinjam', $id)->firstOrFail();

        if ($peminjaman->status == 'menunggu') {
            DB::transaction(function () use ($peminjaman) {
                if ($peminjaman->buku) {
                    $peminjaman->buku->increment('jumlah'); // Kembalikan stok karena batal
                }
                $peminjaman->delete(); // Atau update status ke 'dibatalkan'
            });
            return redirect()->back()->with('success', 'Peminjaman dibatalkan.');
        }
        return redirect()->back()->with('error', 'Tidak bisa membatalkan buku yang sudah dipinjam.');
    }




    public function storeMasal(Request $request)
    {
        $request->validate([
            'id_buku'       => 'required|array',
            'tgl_kembali'   => 'required|array',
            'tgl_kembali.*' => 'required|date',
        ]);

        $limitMaksimal = 6;
        $userId = Auth::id();

        // 1. Hitung buku yang sudah dipinjam sebelumnya
        $totalBukuAktif = Peminjaman::where('id', $userId)
            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat', 'menunggu', 'ajukan_kembali'])
            ->count();

        // 2. Hitung berapa banyak yang mau dipinjam sekarang
        $jumlahBaru = count($request->id_buku);

        // 3. Validasi: Jika total lama + baru melebihi limit
        if (($totalBukuAktif + $jumlahBaru) > $limitMaksimal) {
            $sisaSlot = $limitMaksimal - $totalBukuAktif;
            return back()->with('error', "Gagal! Sisa kuota pinjam kamu hanya $sisaSlot buku lagi.");
        }

        try {
            DB::beginTransaction();
            foreach ($request->id_buku as $key => $id) {
                // ... kode simpan peminjaman Anda ...
                Peminjaman::create([
                    'id'              => $userId,
                    'id_buku'         => $id,
                    'tgl_pinjam'      => now(),
                    'tgl_jatuh_tempo' => $request->tgl_kembali[$key],
                    'status'          => 'menunggu',
                    'denda'           => 0,
                ]);

                $buku = Buku::where('id_buku', $id)->first();
                if ($buku) {
                    $buku->decrement('jumlah');
                }
                Wishlist::where('id', $userId)->where('id_buku', $id)->delete();
            }
            DB::commit();
            return redirect()->route('mypinjaman')->with('success', 'Berhasil mengajukan pinjaman!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
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
        $pinjam = Peminjaman::where('id_pinjam', $id)->firstOrFail();
        $pinjam->update([
            'status'     => 'dipinjam',
            'tgl_pinjam' => now(),
        ]);

        return redirect()->back()->with('success', 'Peminjaman disetujui!');
    }

    public function halamanLaporan(Request $request)
{
    $query = Peminjaman::with(['user', 'buku']);
    $tgl_mulai = $request->tgl_mulai;
    $tgl_selesai = $request->tgl_selesai;
    $jenis = $request->get('jenis', 'peminjaman');

    // Filter Tanggal
    if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
        $query->whereBetween('tgl_pinjam', [$tgl_mulai, $tgl_selesai]);
    } else {
        // Default: Data Bulan Ini
        $query->whereMonth('tgl_pinjam', now()->month)->whereYear('tgl_pinjam', now()->year);
    }

    if ($jenis == 'pengembalian') {
        $query->where('status', 'kembali');
    }

    $semuaPeminjaman = $query->latest()->paginate(20)->withQueryString();

    return view('admin.laporan', compact('semuaPeminjaman', 'tgl_mulai', 'tgl_selesai', 'jenis'));
}

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
}
