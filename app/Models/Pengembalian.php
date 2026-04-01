<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    protected $table = 'pengembalian';
    protected $primaryKey = 'id_kembali';
    protected $fillable = [
        'id_pinjam',
        'tgl_kembali',
        'tgl_jatuh_tempo',
        'id_buku',        
        'tgl_pinjam',     
        'id',           
        'denda'
    ];
    protected $casts = [
        'tgl_kembali' => 'date',
        'tgl_pinjam'  => 'date',
        'tgl_jatuh_tempo' => 'date',
    ];
    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'id_pinjam', 'id_pinjam');
    }
}
