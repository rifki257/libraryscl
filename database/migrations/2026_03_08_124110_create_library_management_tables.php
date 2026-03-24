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
        // Tempel kodenya di sini
        Schema::create('buku', function (Blueprint $table) {
            $table->id('id_buku');
            $table->string('judul', 225);
            $table->string('penerbit', 225);
            $table->string('penulis', 225);
            $table->string('jumlah', 225);
            $table->string('gambar', 225)->nullable();
            $table->timestamps();
        });

        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id('id_pinjam');
            $table->foreignId('id_buku');
            $table->foreignId('id_user');
            $table->date('tgl_pinjam');
            $table->date('tgl_jatuh_tempo');
            $table->timestamps();
        });

        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id('id_kembali');
            $table->date('tgl_kembali');
            $table->foreignId('id_buku');
            $table->foreignId('id_user');
            $table->timestamps();
        });

        Schema::create('denda', function (Blueprint $table) {
            $table->id('id_denda');
            $table->enum('denda_anggota', ['iya', 'tidak']);
            $table->string('jumlah_denda', 225);
            $table->foreignId('id_user');
            $table->foreignId('id_buku');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denda');
        Schema::dropIfExists('pengembalian');
        Schema::dropIfExists('peminjaman');
        Schema::dropIfExists('buku');
    }
};