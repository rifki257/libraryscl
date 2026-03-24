<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id('id_kembali'); // Primary Key

            // ID Pinjam menggunakan unsignedBigInteger karena kita menggunakan ID acak 5 digit
            $table->unsignedBigInteger('id_pinjam');

            // Kolom pendukung sesuai struktur gambar
            $table->integer('id_buku'); // Relasi ke buku
            $table->unsignedBigInteger('id'); // Relasi ke user (peminjam)

            $table->date('tgl_kembali'); // Tanggal saat admin menyetujui
            $table->integer('denda')->default(0); // Mencatat denda akhir

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
