<div class="max-w-screen-7xl mx-auto py-12 relative group">
    <div
        id="scroll-container"
        class="flex flex-row items-center overflow-x-auto pb-10 scrollbar-hide scroll-smooth"
    >
        <div class="flex flex-row items-center space-x-4 pr-10">
            @foreach ($dataBuku as $buku)
                @php
        $isOutOfStock = $buku->jumlah <= 0;
        $isGuest = !Auth::check();
        
        // Logika URL dan Onclick sama seperti sebelumnya
        if ($isOutOfStock) {
            $url = 'javascript:void(0)';
            $onclick = "Swal.fire({icon: 'error', title: 'Stok Habis', text: 'Buku tidak tersedia.', confirmButtonColor: '#2563eb'})";
        } elseif ($isGuest) {
            $url = 'javascript:void(0)';
            $onclick = "Swal.fire({icon: 'info', title: 'Login dulu', text: 'Silakan login untuk meminjam.', showCancelButton: true, confirmButtonText: 'Login'}).then((r) => { if(r.isConfirmed) window.location.href='".route('login')."'; })";
        } else {
            $url = route('peminjaman', $buku->id_buku);
            $onclick = "";
        }
    @endphp
                <div
                    class="flex-shrink-0 w-[190px] bg-[#1e1e1e] rounded-2xl overflow-hidden shadow-lg border border-white/5 transition-transform hover:-translate-y-2"
                >
                    {{-- Area Gambar --}}
                    <div class="relative h-[240px] w-full">
                        <img
                            src="{{ asset('storage/' . $buku->gambar) }}"
                            alt="{{ $buku->judul }}"
                            class="w-full h-full object-cover {{ $isOutOfStock ? 'grayscale opacity-50' : '' }}"
                        />
                        @if (!$isGuest && !$isOutOfStock)
                            <button
                                onclick="tambahWishlist(event, {{ $buku->id_buku }}, '{{ addslashes($buku->judul) }}')"
                                {{-- Gunakan group untuk mendeteksi hover pada button --}}
                                class="absolute top-3 right-3 z-30 transition-all duration-300 group/wish"
                                title="Tambah ke Wishlist"
                            >
                                {{-- 1. Ikon Garis Tepi (Default: Abu-abu, Hilang saat Hover) --}}
                                <i
                                    class="bi bi-bookmark text-2xl text-gray-400 group-hover/wish:hidden"
                                ></i>

                                {{-- 2. Ikon Terisi Penuh (Default: Tersembunyi, Muncul Putih saat Hover) --}}
                                <i
                                    class="bi bi-bookmark-fill text-2xl text-white hidden group-hover/wish:inline-block"
                                ></i>
                            </button>
                        @endif

                        @if ($isOutOfStock)
                            <div
                                class="absolute inset-0 flex items-center justify-center bg-black/40"
                            >
                                <span
                                    class="bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded"
                                    >HABIS</span
                                >
                            </div>
                        @endif
                    </div>

                    {{-- Area Konten (Bawah Gambar) --}}
                    <div class="p-4 flex flex-col h-[140px] justify-between">
                        <div>
                            <h3
                                class="text-blue-400 font-bold text-sm line-clamp-1 mb-1 uppercase tracking-tight"
                            >
                                {{ $buku->judul }}
                            </h3>
                            <p class="text-gray-400 text-[11px] italic line-clamp-1">
                                {{ $buku->penulis }}
                            </p>
                            <p class="text-gray-500 text-[10px] mb-2">
                                Stok:
                                <span
                                    class="{{ $isOutOfStock ? 'text-red-500' : 'text-green-500' }}"
                                    >{{ $buku->jumlah }}</span
                                >
                            </p>
                        </div>

                        {{-- Tombol Pinjam --}}
                        <a
                            href="{{ $url }}"
                            onclick="{{ $onclick }}"
                            class="w-full py-2 rounded-lg text-center text-xs font-bold transition-all {{ $isOutOfStock ? 'bg-gray-700 text-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-blue-900/20 shadow-lg' }}"
                        >
                            {{ $isOutOfStock ? 'TIDAK TERSEDIA' : 'PINJAM SEKARANG' }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
</div>
<script>
    function tambahWishlist(event, idBuku, judulBuku) {
        event.preventDefault();

        axios
            .post('{{ route("wishlist.store") }}', {
                id_buku: idBuku,
            })
            .then((response) => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: judulBuku + ' ditambahkan ke wishlist.',
                    showCancelButton: true,
                    confirmButtonText: 'Lihat Wishlist',
                    cancelButtonText: 'Lanjut Cari Buku',
                    confirmButtonColor: '#467599',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("wishlist.index") }}';
                    }
                });
            })
            .catch((error) => {
                // Cek jika error datang dari validasi (buku sudah ada)
                if (error.response && error.response.status === 422) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Sudah Ada',
                        text:
                            'Buku "' +
                            judulBuku +
                            '" sudah masuk di wishlist kamu.',
                        confirmButtonColor: '#467599',
                    });
                } else {
                    // Error lainnya (masalah koneksi, dll)
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menambahkan buku.',
                        confirmButtonColor: '#ef4444',
                    });
                }
            });
    }
</script>
