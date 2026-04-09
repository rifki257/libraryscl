<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denda extends Model
{
    protected $table = 'denda'; // Sesuaikan dengan nama tabel di gambar
    protected $primaryKey = 'id_denda';
    protected $fillable = ['jumlah_denda', 'id', 'id_buku'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku');
    }
}