<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    // Paksa Laravel menggunakan nama tabel ini
    protected $table = 'wishlist';

    // Paksa Laravel menggunakan kolom ini sebagai ID (bukan 'id')
    protected $primaryKey = 'id_wishlist';

    protected $fillable = [
        'id',      // Kolom user (sesuai permintaanmu tadi)
        'id_buku',
    ];

    // Relasi ke buku agar bisa dipanggil di halaman wishlist
    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }
}
