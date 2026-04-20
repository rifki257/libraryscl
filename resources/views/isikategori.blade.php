<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
/>
<x-app-layout>
    <x-slot name="header">
        <div
            class="flex flex-col md:flex-row items-center justify-between gap-4"
        >
            <h2
                class="font-semibold text-xl text-gray-800 leading-tight shrink-0"
            >
                {{ $kategori->nama_kategori }}
            </h2>

            <div class="flex-grow max-w-md w-full">
                <form
                    action="{{ url()->current() }}"
                    method="GET"
                    class="relative group"
                >
                    <div class="relative">
                        <span
                            class="absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <i class="bi bi-search text-gray-400"></i>
                        </span>

                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari buku {{ $kategori->nama_kategori }}..."
                            class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-full focus:ring-blue-500 focus:border-blue-500 text-sm transition-all"
                        />

                        @if (request('search'))
                            <a
                                href="{{ url()->current() }}"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-red-500 transition-colors"
                            >
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <a href="{{ route('katalog') }}" class="btn btn-success shrink-0">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($dataBuku->count() > 0)
                <div class="flex flex-wrap justify-center gap-6">
                    @foreach ($dataBuku as $buku)
                        @php
                    $isOutOfStock = $buku->jumlah <= 0;
                    $isGuest = !Auth::check();

                    $totalPinjam = 0;
                    if (!$isGuest) {
                        $totalPinjam = \App\Models\Peminjaman::where('id', auth()->id())
                            ->whereIn('status', ['pending', 'dipinjam', 'proses', 'terlambat', 'menunggu', 'ajukan_kembali'])
                            ->count();
                    }
                    $isLimit = $totalPinjam >= 6;

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
                        <div
                            class="w-[150px] bg-[#1e1e1e] rounded-2xl overflow-hidden shadow border border-white/5 transition-transform hover:-translate-y-2"
                        >
                            <div class="relative h-[200px] w-full">
                                <img
                                    src="{{ asset('storage/' . $buku->gambar) }}"
                                    alt="{{ $buku->judul }}"
                                    class="w-full h-full object-cover {{ $isOutOfStock ? 'grayscale opacity-50' : '' }}"
                                />

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
                                class="p-4 flex flex-col h-[130px] justify-between"
                            >
                                <div>
                                    <h3
                                        class="text-blue-400 font-bold text-sm line-clamp-1 uppercase tracking-tight"
                                    >
                                        {{ $buku->judul }}
                                    </h3>
                                    <p class="text-white text-[13px] italic line-clamp-1">
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
            @else
                <div class="flex flex-col items-center justify-center py-20">
                    <div class="bg-gray-800/50 rounded-full p-6 mb-4">
                        <i class="bi bi-search text-5xl text-gray-500"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-white">
                        Buku tidak ditemukan
                    </h3>
                    <p class="text-gray-400 mt-2">Maaf, buku dengan judul atau penulis <span class="text-blue-400">"{{ request('search') }}"</span> tidak tersedia di kategori ini.</p>
                </div>
            @endif
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
