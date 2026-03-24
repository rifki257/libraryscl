<div
    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"
>
    @foreach ($dataBuku as $buku)
        @php
            $url = 'javascript:void(0)';
            $onclick = '';
            $isOutOfStock = $buku->jumlah <= 0;
            $isGuest = !Auth::check();

            if ($isOutOfStock) {
                $onclick = "Swal.fire({
                    icon: 'error',
                    title: 'Stok Habis',
                    text: 'Maaf, buku " . addslashes($buku->judul) . " tidak tersedia saat ini.',
                    confirmButtonColor: '#2563eb'
                })";
            } elseif ($isGuest) {
                $onclick = "Swal.fire({
                    icon: 'info',
                    title: 'Ingin Meminjam?',
                    text: 'Silakan login terlebih dahulu untuk meminjam buku',
                    showCancelButton: true,
                    confirmButtonText: 'Login Sekarang',
                    cancelButtonText: 'Nanti Saja',
                    confirmButtonColor: '#2563eb'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '" . route('login') . "';
                    }
                })";
            } else {
                $url = route('peminjaman', $buku->id_buku);
            }
        @endphp
        <a
            href="{{ $url }}"
            onclick="{{ $onclick }}"
            class="book-card shadow-lg block transition-transform hover:-translate-y-2 {{ $isOutOfStock ? 'opacity-75 grayscale cursor-not-allowed' : '' }}"
        >
            <img
                src="{{ asset('storage/' . $buku->gambar) }}"
                alt="{{ $buku->judul }}"
                class="book-image"
            />

            <div class="book-overlay">
                <h3
                    style="
                        font-size: 1.3rem;
                        font-weight: 800;
                        line-height: 1.1;
                        color: #60a5fa !important;
                        margin-bottom: 0.5rem;
                        text-transform: capitalize;
                    "
                >
                    {{ $buku->judul }}
                </h3>

                <p style="
                        font-size: 0.875rem;
                        font-style: italic;
                        color: #d1d5db !important;
                    ">
                    Penulis: {{ $buku->penulis }}
                </p>

                <p style="
                        font-size: 0.9rem;
                        font-style: italic;
                        color: #d1d5db !important;
                    ">
                    Penerbit: {{ $buku->penerbit }}
                </p>

                <div
                    style="
                        margin-top: 1rem;
                        padding: 0.25rem 0.75rem;
                        background: #2563eb;
                        border-radius: 0.375rem;
                        font-size: 0.75rem;
                        font-weight: 600;
                    "
                >
                    Stok: {{ $buku->jumlah }}
                </div>

                <p style="
                        margin-top: 1.5rem;
                        font-size: 0.75rem;
                        font-weight: bold;
                        border-bottom: 1px solid white;
                    ">
                    {{ $isOutOfStock ? 'STOK HABIS' : 'KLIK UNTUK PINJAM' }}
                </p>
            </div>
        </a>
    @endforeach
</div>