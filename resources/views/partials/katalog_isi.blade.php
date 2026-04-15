@foreach ($allKategori as $kat)
    {{-- Container per baris kategori --}}
    <div class="max-w-screen-7xl mx-auto py-8 px-4">
        {{-- Header: Judul Kategori & See All --}}
        <div class="flex justify-between items-center mb-6">
            <div class="flex flex-col">
                <h2
                    class="text-2xl font-bold text-dark uppercase tracking-tight"
                >
                    {{ $kat->nama_kategori }}
                </h2>
                <div class="h-1 w-12 bg-blue-600 rounded-full mt-1"></div>
            </div>

            <a
                href="{{ route('isikategori', $kat->id_kategori) }}"
                class="group text-blue-400 hover:text-blue-300 transition-all flex items-center text-sm font-semibold"
            >
                See All
                <i
                    class="bi bi-arrow-right-short text-xl ml-1 group-hover:translate-x-1 transition-transform"
                ></i>
            </a>
        </div>

        {{-- Scroll Container Buku --}}
        <div class="relative group">
            <div
                class="flex flex-row justify-center items-start overflow-x-auto pb-4 scrollbar-hide scroll-smooth space-x-5"
            >
                @foreach ($kat->buku as $buku)
                    @php
                        $isOutOfStock = $buku->jumlah <= 0;
                        $isGuest = !Auth::check();
                        
                        if ($isOutOfStock) {
                            $url = 'javascript:void(0)';
                            $onclick = "Swal.fire({icon:'error', title:'Stok Habis', text:'Buku tidak tersedia.'})";
                        } elseif ($isGuest) {
                            $url = 'javascript:void(0)';
                            $onclick = "Swal.fire({icon:'info', title:'Login dulu', text:'Silakan login untuk meminjam.', showCancelButton:true}).then((r)=>{if(r.isConfirmed) window.location.href='".route('login')."';})";
                        } else {
                            $url = route('peminjaman.beda', ['id' => $buku->id_buku]);
                            $onclick = "";
                        }
                    @endphp
                    {{-- Card Buku --}}
                    <div
                        class="flex-shrink-0 w-[190px] bg-[#1e1e1e] rounded-2xl overflow-hidden shadow border border-white/5 transition-all hover:-translate-y-2 hover:border-blue-500/30"
                    >
                        {{-- Area Gambar --}}
                        <div class="relative h-[240px] w-full">
                            <img
                                src="{{ asset('storage/' . $buku->gambar) }}"
                                class="w-full h-full object-cover {{ $isOutOfStock ? 'grayscale opacity-50' : '' }}"
                            />

                            {{-- TOMBOL WISHLIST (Kembali Ditambahkan) --}}
                            @if (!$isGuest && !$isOutOfStock)
                                <button
                                    onclick="tambahWishlist(event, {{ $buku->id_buku }}, '{{ addslashes($buku->judul) }}')"
                                    class="absolute top-3 right-3 z-30 transition-all duration-300 group/wish"
                                    title="Tambah ke Wishlist"
                                >
                                    <i
                                        class="bi bi-bookmark text-2xl text-gray-400 group-hover/wish:hidden"
                                    ></i>
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

                        {{-- Area Detail --}}
                        <div
                            class="p-4 flex flex-col h-[140px] justify-between"
                        >
                            <div>
                                <h3
                                    class="text-blue-400 font-bold text-sm line-clamp-1 uppercase"
                                >
                                    {{ $buku->judul }}
                                </h3>
                                <p class="text-white text-[12px] line-clamp-1 italic">{{ $buku->penulis }}</p>
                                <p class="text-gray-500 text-[12px]">Stok: <span class="{{ $isOutOfStock ? 'text-red-500' : 'text-green-500' }}">{{ $buku->jumlah }}</span></p>
                            </div>

                            <a
                                href="{{ $url }}"
                                onclick="{!! $onclick !!}"
                                class="w-full py-2 rounded-lg text-center text-xs font-bold {{ $isOutOfStock ? 'bg-gray-700 text-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-900/20' }} transition-all"
                            >
                                {{ $isOutOfStock ? 'TIDAK TERSEDIA' : 'PINJAM SEKARANG' }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endforeach
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
