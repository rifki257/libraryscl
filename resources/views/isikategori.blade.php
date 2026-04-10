<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Buku Kategori: {{ $kategori->nama_kategori }}
            </h2>

            <a href="{{ route('katalog') }}" class="btn btn-success">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Menggunakan flex-wrap agar elemen pindah ke bawah, dan justify-center agar rapi di tengah --}}
            <div class="flex flex-wrap justify-start gap-6">
                @foreach ($dataBuku as $buku)
                    @php
                    $isOutOfStock = $buku->jumlah <= 0;
                    $isGuest = !Auth::check();
                    
                    // Hitung kuota pinjam
                    $totalPinjam = 0;
                    if (!$isGuest) {
                        $totalPinjam = \App\Models\Peminjaman::where('id', auth()->id())
                            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat', 'menunggu', 'ajukan_kembali'])
                            ->count();
                    }
                    $isLimit = $totalPinjam >= 6;

                    // Logika URL dan Swal
                    if ($isOutOfStock) {
                        $url = 'javascript:void(0)';
                        $onclick = "Swal.fire({icon: 'error', title: 'Stok Habis', text: 'Buku tidak tersedia.', confirmButtonColor: '#ef4444'})";
                    } elseif ($isGuest) {
                        $url = 'javascript:void(0)';
                        $onclick = "Swal.fire({icon: 'info', title: 'Login dulu', text: 'Silakan login untuk meminjam.', showCancelButton: true, confirmButtonText: 'Login'}).then((r) => { if(r.isConfirmed) window.location.href='".route('login')."'; })";
                    } elseif ($isLimit) {
                        $url = 'javascript:void(0)';
                        $onclick = "Swal.fire({icon: 'warning', title: 'Limit Tercapai', text: 'Kamu sudah meminjam/mengajukan 6 buku.', confirmButtonColor: '#6366F1'})";
                    } else {
                        $url = route('peminjaman.beda', ['id' => $buku->id_buku]);
                        $onclick = "";
                    }
                @endphp
                    {{-- Card Item --}}
                    <div
                        class="w-[190px] bg-[#1e1e1e] rounded-2xl overflow-hidden shadow border border-white/5 transition-transform hover:-translate-y-2"
                    >
                        {{-- Area Gambar --}}
                        <div class="relative h-[240px] w-full">
                            <img
                                src="{{ asset('storage/' . $buku->gambar) }}"
                                alt="{{ $buku->judul }}"
                                class="w-full h-full object-cover {{ $isOutOfStock ? 'grayscale opacity-50' : '' }}"
                            />

                            {{-- Tombol Wishlist --}}
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

                        {{-- Area Konten --}}
                        <div
                            class="p-4 flex flex-col h-[150px] justify-between"
                        >
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

                            <a
                                href="{{ $url }}"
                                onclick="{!! $onclick !!}"
                                class="w-full py-2 rounded-lg text-center text-[10px] font-bold transition-all no-underline
                            {{ $isOutOfStock || ($isLimit && !$isGuest)
                                ? 'bg-gray-700 text-gray-400 cursor-not-allowed' 
                                : 'bg-blue-600 hover:bg-blue-700 text-white shadow-blue-900/20 shadow-lg' 
                            }}"
                            >
                                @if ($isOutOfStock)
                                    TIDAK TERSEDIA
                                @elseif ($isLimit && !$isGuest)
                                    KUOTA PENUH
                                @else
                                    PINJAM SEKARANG
                                @endif
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <button
        onclick="scrollToTop()"
        id="btnScrollTop"
        class="fixed bottom-8 right-8 z-50 flex items-center justify-center w-14 h-14 bg-blue-600 text-white rounded-full shadow-2xl transition-all duration-300 hover:bg-blue-700 hover:scale-110 group focus:outline-none opacity-0 invisible"
        title="Kembali ke Atas"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 transition-transform group-hover:-translate-y-1"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>

        <span
            class="absolute right-16 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none"
        >
            Kembali ke Atas
        </span>
    </button>
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
        const btnScrollTop = document.getElementById('btnScrollTop');

        // Fungsi untuk memantau scroll
        window.onscroll = function () {
            // Jika user scroll ke bawah lebih dari 300px, munculkan tombol
            if (
                document.body.scrollTop > 300 ||
                document.documentElement.scrollTop > 300
            ) {
                btnScrollTop.classList.remove('opacity-0', 'invisible');
                btnScrollTop.classList.add('opacity-100', 'visible');
            } else {
                btnScrollTop.classList.add('opacity-0', 'invisible');
                btnScrollTop.classList.remove('opacity-100', 'visible');
            }
        };

        // Fungsi untuk eksekusi scroll ke atas
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth', // Efek scroll halus, bukan loncat tiba-tiba
            });
        }
    </script>
</x-app-layout>
