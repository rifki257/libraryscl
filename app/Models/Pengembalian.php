<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    protected $table = 'pengembalian';
    protected $primaryKey = 'id_kembali';
    protected $fillable = ['id_pinjam', 'id_buku', 'id', 'tgl_kembali', 'tgl_pinjam', 'tgl_jatuh_tempo', 'denda'];

    public function book()
    {
        return $this->belongsTo(Buku::class, 'id_buku');
    }

    // Relasi ke User (menggunakan id sesuai gambar)
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    // Relasi ke Peminjaman (menggunakan id_pinjam sesuai gambar)
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'id_pinjam');
    }
}
