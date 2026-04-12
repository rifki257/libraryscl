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
    // data konfir pengembalian
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
        $semuaPeminjaman = $query->latest()->get();
    } else {
        $semuaPeminjaman = $query->latest()->paginate(1);
    }

    if ($request->ajax()) {
        return view('admin.table_pengembalian_rows', compact('semuaPeminjaman'))->render();
    }

    return view('pengembalian', compact('semuaPeminjaman'));
    }

    // konfirmasi pengembalian
    public function konfirmasi(Request $request, $id)
    {
    $pinjam = Peminjaman::where('id_pinjam', $id)->firstOrFail();
    $jt = \Carbon\Carbon::parse($pinjam->tgl_jatuh_tempo)->startOfDay();
    $kb = now()->startOfDay(); 
    $denda = 0;

    if ($kb->gt($jt)) {
        $selisih = $kb->diffInDays($jt);
        $denda = abs($selisih) * 50000;
    }

    DB::transaction(function () use ($pinjam, $denda) {
        Pengembalian::create([
            'id_pinjam'       => $pinjam->id_pinjam,
            'id_buku'         => $pinjam->id_buku,
            'id'              => $pinjam->id,
            'tgl_pinjam'      => $pinjam->tgl_pinjam,
            'tgl_jatuh_tempo' => $pinjam->tgl_jatuh_tempo,
            'tgl_kembali'     => now(),
            'denda'           => $denda
        ]);

        if ($denda > 0) {
            \App\Models\Denda::create([
                'jumlah_denda' => (string)$denda, 
                'id'           => $pinjam->id,     
                'id_buku'      => $pinjam->id_buku,
            ]);
        }

        $pinjam->update(['status' => 'kembali']);

        if ($pinjam->buku) {
            $pinjam->buku->increment('jumlah');
        }
    });

    return redirect()->route('pengembalian')->with('success', 'Buku berhasil dikonfirmasi dan denda otomatis dicatat!');
    }

    // data pengembalian
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
        $semuaPeminjaman = $query->latest()->get(); 
    } else {
        $semuaPeminjaman = $query->latest()->paginate(2); 
    }

    if ($request->ajax()) {
        return view('admin.table_history_rows', compact('semuaPeminjaman'))->render();
    }

    return view('datapengembalian', compact('semuaPeminjaman'));
    }
}
