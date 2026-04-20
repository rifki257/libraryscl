@foreach ($allKategori as $kat)
    <div class="max-w-screen-7xl mx-auto py-8 px-4">
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
                style="justify-content: flex-start;"
            >
                @foreach ($kat->buku as $buku)
                    @php
        $isOutOfStock = $buku->jumlah <= 0;
        $isGuest = !Auth::check();
        $urlPeminjaman = route('peminjaman.store.masal');
        
        $isLimitReached = false;
        if (!$isGuest) {
            $jumlahDipinjam = \App\Models\Peminjaman::where('id', Auth::id())
                ->whereIn('status', ['menunggu', 'dipinjam'])
                ->count();
            $isLimitReached = ($jumlahDipinjam >= 6);
        }

        if ($isOutOfStock) {
            $actionClick = "Swal.fire({icon:'error', title:'Stok Habis', text:'Buku tidak tersedia.'})";
        } elseif ($isGuest) {
            $actionClick = "confirmLogin()";
        } elseif ($isLimitReached) {
            $actionClick = "Swal.fire({icon:'warning', title:'Peminjaman Penuh', text:'Anda sudah mencapai batas maksimal 6 buku.'})";
        } else {
            $actionClick = "konfirmasiPinjam('$urlPeminjaman', '{$buku->id_buku}', '" . addslashes($buku->judul) . "')";
        }
    @endphp
                    <div
                        onclick="handleCardClick(event, '{{ $isGuest ? 1 : 0 }}', '{{ $isOutOfStock ? 1 : 0 }}', '{{ $isLimitReached ? 1 : 0 }}', '{!! $actionClick !!}')"
                        class="flex-shrink-0 w-[190px] bg-[#1e1e1e] rounded-2xl cursor-pointer group/card"
                    >
                        <div class="relative h-[240px] w-full">
                            <img
                                src="{{ asset('storage/' . $buku->gambar) }}"
                                class="w-full h-full object-cover {{ $isOutOfStock ? 'grayscale opacity-50' : '' }}"
                            />

                            {{-- Tombol Wishlist --}}
                            @if (!$isGuest && !$isOutOfStock)
                                <button
                                    onclick="event.stopPropagation(); tambahWishlist(event, {{ $buku->id_buku }}, '{{ addslashes($buku->judul) }}')"
                                    class="absolute top-3 right-3 z-30 transition-all duration-300 group/wish"
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

                        <div
                            class="p-4 flex flex-col h-[140px] justify-between"
                        >
                            <div>
                                <h3
                                    class="text-blue-400 font-bold text-sm line-clamp-1 uppercase group-hover/card:text-blue-300"
                                >
                                    {{ $buku->judul }}
                                </h3>
                                <p class="text-white text-[12px] line-clamp-1 italic">{{ $buku->penulis }}</p>
                                <p class="text-gray-500 text-[12px]">Stok: <span class="{{ $isOutOfStock ? 'text-red-500' : 'text-green-500' }}">{{ $buku->jumlah }}</span></p>
                            </div>

                            @auth
                                @if ($isLimitReached)
                                    <div
                                        class="w-full bg-gray-500 text-white py-2 rounded-lg text-center text-xs font-semibold flex items-center justify-center gap-2 cursor-not-allowed opacity-75"
                                    >
                                        <i class="fas fa-lock"></i>
                                        6 Peminjaman
                                    </div>
                                @else
                                    <div
                                        onclick="event.stopPropagation(); {!! $actionClick !!}"
                                        class="w-full bg-blue-600 text-white py-2 rounded-lg text-center text-xs font-semibold flex items-center justify-center gap-2 hover:bg-blue-700 transition-all cursor-pointer"
                                    >
                                        <i class="fas fa-book-reader"></i>
                                        Pinjam Sekarang
                                    </div>
                                @endif
                            @endauth

                            @auth
                                @guest
                                    <div
                                        onclick="
                                            event.stopPropagation();
                                            confirmLogin();
                                        "
                                        class="w-full bg-blue-600 text-white py-2 rounded-lg text-center text-xs font-semibold flex items-center justify-center gap-2 hover:bg-blue-700 cursor-pointer"
                                    >
                                        <i class="fas fa-sign-in-alt"></i>
                                        Pinjam Sekarang
                                    </div>
                                @endguest
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endforeach
<form id="peminjaman-form-global" action="" method="POST" style="display: none">
    @csrf
    <input type="hidden" name="id_buku[]" id="global-id-buku" />
</form>
<script>
    function confirmLogin() {
        Swal.fire({
            icon: 'info',
            title: 'Login dulu',
            text: 'Silakan login untuk meminjam buku.',
            showCancelButton: true,
            confirmButtonText: 'Login Sekarang',
            confirmButtonColor: '#2563eb',
        }).then((r) => {
            if (r.isConfirmed)
                window.location.href = '{{ route("login") }}';
        });
    }

    // Fungsi Konfirmasi Pinjam dengan Notifikasi
    function konfirmasiPinjam(url, idBuku, judulBuku) {
        Swal.fire({
            title: 'Pinjam Buku?',
            text: "Pinjam '" + judulBuku + "' selama 30 hari?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Ya, Pinjam!',
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Sedang Memproses',
                    html: 'Mohon tunggu sebentar...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                axios
                    .post(url, {
                        id_buku: [idBuku],
                        _token: '{{ csrf_token() }}',
                    })
                    .then((response) => {
                        // Berhasil
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Dipinjam!',
                            text: 'Silakan cek menu Peminjaman Saya.',
                            confirmButtonText: 'Lihat Peminjaman',
                            showCancelButton: true,
                            cancelButtonText: 'Tetap di Sini',
                        }).then((res) => {
                            if (res.isConfirmed) {
                                window.location.href =
                                    '{{ route("mypinjaman") }}';
                            } else {
                                location.reload();
                            }
                        });
                    })
                    .catch((error) => {
                        let msg = 'Terjadi kesalahan pada server.';
                        if (error.response && error.response.data.message) {
                            msg = error.response.data.message;
                        }
                        Swal.fire('Gagal', msg, 'error');
                    });
            }
        });
    }

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
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menambahkan buku.',
                        confirmButtonColor: '#ef4444',
                    });
                }
            });
    }

    function handleCardClick(
        event,
        isGuest,
        isOutOfStock,
        isLimitReached,
        url,
        idBuku,
        judul
    ) {
        // 1. Jika Stok Habis
        if (isOutOfStock === '1') {
            Swal.fire({
                icon: 'error',
                title: 'Stok Habis',
                text: 'Buku tidak tersedia.',
            });
            return;
        }

        // 2. Jika Guest (Belum Login) -> Arahkan ke Login
        if (isGuest === '1') {
            confirmLogin();
            return;
        }

        // 3. Jika Sudah Login tapi Kuota Penuh (6 Buku)
        if (isLimitReached === '1') {
            Swal.fire({
                icon: 'warning',
                title: 'Peminjaman Penuh',
                text: 'Anda sudah mencapai batas maksimal 6 buku.',
                confirmButtonColor: '#3b82f6',
            });
            return;
        }

        // 4. Jika Oke Semua -> Jalankan Konfirmasi Pinjam
        konfirmasiPinjam(url, idBuku, judul);
    }
</script>
