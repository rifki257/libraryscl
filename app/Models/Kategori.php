<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategoris'; // Nama tabel manual kamu
    protected $fillable = ['nama_kategori']; // Nama kolom di database
}
