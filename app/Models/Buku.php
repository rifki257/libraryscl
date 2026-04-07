<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';
    protected $primaryKey = 'id_buku';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'id_buku',
        'judul',
        'penerbit',
        'penulis',
        'jumlah',
        'gambar',
        'id_kategori',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }
    
    protected static function booted()
    {
        static::creating(function ($buku) {
            do {
                $randomId = rand(1000, 9999);
            } while (static::where('id_buku', $randomId)->exists());

            $buku->id_buku = $randomId;
        });
    }
}
