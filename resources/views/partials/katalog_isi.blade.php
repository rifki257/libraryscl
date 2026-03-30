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
    .banner-item {
        transition: opacity 0.4s ease, transform 0.4s ease;
    }

    .hidden-banner {
        opacity: 0 !important;
        transform: scale(0.9);
        pointer-events: none;
    }
/* Style untuk tombol agar melayang */
    .nav-btn {
        position: absolute;
        top: 45%; /* Posisi vertikal di tengah */
        transform: translateY(-50%);
        z-index: 50; /* Pastikan di atas banner dan kartu */
        background-color: rgba(255, 255, 255, 0.9); /* Warna background tombol */
        color: #1a1a1a;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: none; /* Default sembunyi, muncul via JS class is-available */
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .nav-btn:hover {
        background-color: #ffffff;
        transform: translateY(-50%) scale(1.1);
    }

    /* Posisi spesifik kiri dan kanan */
    .btn-prev {
        left: 20px; /* Jarak dari sisi kiri (akan menimpa banner) */
    }

    .btn-next {
        right: 20px; /* Jarak dari sisi kanan */
    }

    /* Class untuk menampilkan tombol (dipanggil JS) */
    .nav-btn.is-available {
        display: flex !important;
    }
</style>
<div class="max-w-screen-2xl mx-auto py-12 relative group">
    <button id="prevBtn" class="nav-btn btn-prev" onclick="scrollMove('left')">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
    </button>
    
    <button id="nextBtn" class="nav-btn btn-next" onclick="scrollMove('right')">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    </button>
    <div
    id="scroll-container"
        class="flex flex-row items-center space-x-0 overflow-x-auto pb-10 scrollbar-hide scroll-smooth"
    >
        {{-- card --}}
        <div
            id="banner" class="banner-item relative flex-shrink-0 w-[230px] h-[330px] overflow-hidden rounded-[1.3rem] "
        >
            <img
                src="{{ asset('images/coba.jpg') }}"
                class="h-full object-cover rounded-[1.3rem]"
            />
        </div>
        {{-- isi buku --}}
        <div
            class="flex flex-row items-center space-x-4"
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
    // Deklarasikan variabel cukup SATU kali saja di paling atas
    const container = document.getElementById('scroll-container');
    const banner = document.getElementById('banner');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    // 1. Fungsi untuk menggerakkan scroll (dipanggil oleh onclick di HTML)
    function scrollMove(direction) {
        const scrollAmount = 500; 
        if(direction === 'left') {
            container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        } else {
            container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    }

    // 2. Fungsi utama untuk update semua UI (Tombol & Banner)
    function updateUI() {
        const scrollLeft = container.scrollLeft;
        const maxScroll = container.scrollWidth - container.clientWidth;

        // Logika Tombol Kiri: Muncul jika sudah mulai geser
        if (scrollLeft > 10) {
            prevBtn.classList.add('is-available');
        } else {
            prevBtn.classList.remove('is-available');
        }

        // Logika Tombol Kanan: Hilang jika sudah mentok kanan
        if (scrollLeft >= maxScroll - 15) { // Toleransi 15px
            nextBtn.classList.remove('is-available');
        } else {
            nextBtn.classList.add('is-available');
        }

        // Logika Banner: Hilang jika di-scroll
        if (scrollLeft > 20) {
            banner.classList.add('hidden-banner');
        } else {
            banner.classList.remove('hidden-banner');
        }
    }

    // 3. Event Listeners
    container.addEventListener('scroll', updateUI);
    window.addEventListener('resize', updateUI);
    
    // PENTING: Jalankan fungsi sekali saat halaman baru terbuka
    // agar tombol kanan langsung muncul otomatis
    document.addEventListener('DOMContentLoaded', updateUI);
    window.onload = updateUI;
</script>