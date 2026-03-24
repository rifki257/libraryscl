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
        'id_buku',
        'tgl_pinjam',
        'id',
        'denda'
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}
