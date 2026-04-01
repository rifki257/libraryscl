<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';
    protected $primaryKey = 'id_pinjam';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_pinjam',
        'id_buku',
        'id',
        'tgl_pinjam',
        'tgl_jatuh_tempo',
        'status',
        'denda'
    ];

    protected static function booted()
    {
        static::creating(function ($pinjam) {
            if (empty($pinjam->id_pinjam)) {
                do {
                    $randomId = rand(10000, 99999);
                } while (static::where('id_pinjam', $randomId)->exists());

                $pinjam->id_pinjam = $randomId;
            }
        });
    }
    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'id_pinjam', 'id_pinjam');
    }
}
