<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengembalian;

class UserpengembalianController extends Controller
{

    public function history()
    {
        $userId = auth()->id();

        $riwayatSelesai = Pengembalian::with(['buku'])
            ->where('id', $userId)
            ->latest()
            ->get();

        return view('mybalik', compact('riwayatSelesai'));
    }

}
