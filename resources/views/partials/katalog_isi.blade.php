<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .book-card {
        position: relative;
        overflow: hidden;
        border-radius: 0.75rem;
        background-color: #1a1a1a;
        aspect-ratio: 3/4;
    }

    .book-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition:
            transform 0.5s ease,
            opacity 0.5s ease;
    }

    .book-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.75);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        text-align: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 20;
    }

    .book-overlay h3,
    .book-overlay p,
    .book-overlay span {
        color: #ffffff !important;
        margin: 0.25rem 0;
        pointer-events: none;
    }

    .book-card:hover .book-overlay {
        opacity: 1;
    }

    .book-card:hover .book-image {
        transform: scale(1.1);
        opacity: 0.3;
    }

    .sticky-banner {
        position: sticky;
        left: 0;
        z-index: 1;
        flex-shrink: 0;
        transition: all 0.4s ease;
    }

    .hidden-banner {
        opacity: 0;
        pointer-events: none;
    }
</style>

<div class="max-w-screen-7xl mx-auto py-12 relative group">
    <div
        id="scroll-container"
        class="flex flex-row items-center overflow-x-auto pb-10 scrollbar-hide scroll-smooth"
    >
        {{-- Banner / Card Utama --}}
        <div
            id="banner"
            class="sticky-banner w-[215px] h-[330px] overflow-hidden rounded-[1.3rem]"
        >
            <img
                src="{{ asset('images/coba.jpg') }}"
                class="h-full object-cover rounded-[1.3rem]"
            />
        </div>

        {{-- List Buku --}}
        <div
            class="flex flex-row items-center space-x-4 pr-10"
            style="margin-left: -85px; z-index: 10"
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
                    class="book-card flex-shrink-0 w-[170px] h-[250px] block transition-transform hover:-translate-y-2 {{ $isOutOfStock ? 'opacity-75 grayscale cursor-not-allowed' : '' }}"
                >
                    <img
                        src="{{ asset('storage/' . $buku->gambar) }}"
                        alt="{{ $buku->judul }}"
                        class="book-image w-full h-full object-cover rounded-xl"
                    />

                    <div class="book-overlay">
                        <h3
                            style="
                                font-size: 1rem;
                                font-weight: 800;
                                line-height: 1.1;
                                color: #60a5fa !important;
                                margin-bottom: 0.5rem;
                                text-transform: capitalize;
                            "
                        >
                            {{ $buku->judul }}
                        </h3>

                        <p
                            style="
                                font-size: 0.7rem;
                                font-style: italic;
                                color: #d1d5db !important;
                            "
                        >
                            Penulis: {{ $buku->penulis }}
                        </p>

                        <p
                            style="
                                font-size: 0.7rem;
                                font-style: italic;
                                color: #d1d5db !important;
                            "
                        >
                            Penerbit: {{ $buku->penerbit }}
                        </p>

                        <div
                            style="
                                margin-top: 0.6rem;
                                padding: 0.25rem 0.75rem;
                                background: #2563eb;
                                border-radius: 0.375rem;
                                font-size: 0.7rem;
                                font-weight: 600;
                            "
                        >
                            Stok: {{ $buku->jumlah }}
                        </div>

                        <p
                            style="
                                margin-top: 0.3rem;
                                font-size: 0.7rem;
                                font-weight: bold;
                                border-bottom: 1px solid white;
                            "
                        >
                            {{ $isOutOfStock ? 'STOK HABIS' : 'KLIK UNTUK PINJAM' }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
<script>
    const container = document.getElementById('scroll-container');
    const banner = document.getElementById('banner');

    function updateUI() {
        if (!container || !banner) return;

        const scrollLeft = container.scrollLeft;

        if (scrollLeft > 5) {
            banner.classList.add('hidden-banner');
        } else {
            banner.classList.remove('hidden-banner');
        }
    }

    container.addEventListener('scroll', updateUI, { passive: true });
    window.addEventListener('load', updateUI);
</script>
