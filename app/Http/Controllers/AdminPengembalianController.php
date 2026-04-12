<?php

namespace App\Http\Controllers;
use App\Models\Denda;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminPengembalianController extends Controller
{
    public function index(Request $request)
{
    $query = Peminjaman::with(['user', 'buku'])
        ->whereIn('status', ['dipinjam', 'dikembalikan', 'ajukan_kembali', 'Dikembalikan']);

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('user', function($u) use ($search) {
                $u->where('name', 'like', '%' . $search . '%');
            })->orWhereHas('buku', function($b) use ($search) {
                $b->where('judul', 'like', '%' . $search . '%');
            });
        });
        // Hilangkan pagination saat search
        $semuaPeminjaman = $query->latest()->get();
    } else {
        // Gunakan pagination saat normal
        $semuaPeminjaman = $query->latest()->paginate(1);
    }

    if ($request->ajax()) {
        return view('admin.table_pengembalian_rows', compact('semuaPeminjaman'))->render();
    }

    return view('pengembalian', compact('semuaPeminjaman'));
}

    public function konfirmasi(Request $request, $id)
{
    // Ambil data peminjaman berdasarkan ID
    $pinjam = Peminjaman::where('id_pinjam', $id)->firstOrFail();

    // 1. Hitung Denda dengan Logika yang Benar
    // Kita paksa ke startOfDay agar selisih hari dihitung per tanggal, bukan per jam
    $jt = \Carbon\Carbon::parse($pinjam->tgl_jatuh_tempo)->startOfDay();
    $kb = now()->startOfDay(); 
    
    $denda = 0;

    // Cek jika hari ini (kb) sudah melewati jatuh tempo (jt)
    if ($kb->gt($jt)) {
        $selisih = $kb->diffInDays($jt);
        // abs() memastikan angka tidak akan pernah minus meskipun ada kesalahan urutan tanggal
        $denda = abs($selisih) * 50000;
    }

    // 2. Bungkus dalam Transaksi
    DB::transaction(function () use ($pinjam, $denda) {
        
        // Simpan ke Tabel Pengembalian (Riwayat)
        Pengembalian::create([
            'id_pinjam'       => $pinjam->id_pinjam,
            'id_buku'         => $pinjam->id_buku,
            'id'              => $pinjam->id,
            'tgl_pinjam'      => $pinjam->tgl_pinjam,
            'tgl_jatuh_tempo' => $pinjam->tgl_jatuh_tempo,
            'tgl_kembali'     => now(),
            'denda'           => $denda // Nilai positif akan masuk di sini
        ]);

        // 3. Simpan ke Tabel Denda (Hanya jika denda > 0)
        if ($denda > 0) {
            \App\Models\Denda::create([
                'jumlah_denda' => (string)$denda, // Cast ke string agar sesuai varchar(225)
                'id'           => $pinjam->id,      // User ID
                'id_buku'      => $pinjam->id_buku,
            ]);
        }

        // 4. Update status di tabel peminjaman
        $pinjam->update(['status' => 'kembali']);

        // 5. Kembalikan stok buku
        if ($pinjam->buku) {
            $pinjam->buku->increment('jumlah');
        }
    });

    return redirect()->route('pengembalian')->with('success', 'Buku berhasil dikonfirmasi dan denda otomatis dicatat!');
}

    public function history(Request $request)
{
    $query = \App\Models\Peminjaman::with(['user', 'buku'])
        ->where('status', 'kembali');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('user', function($u) use ($search) {
                $u->where('name', 'like', '%' . $search . '%');
            })->orWhereHas('buku', function($b) use ($search) {
                $b->where('judul', 'like', '%' . $search . '%');
            });
        });
        $semuaPeminjaman = $query->latest()->get(); // Ambil semua saat search
    } else {
        $semuaPeminjaman = $query->latest()->paginate(2); // Paginate saat normal
    }

    if ($request->ajax()) {
        return view('admin.table_history_rows', compact('semuaPeminjaman'))->render();
    }

    return view('datapengembalian', compact('semuaPeminjaman'));
}


}
