<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#1f2937',
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#ef4444',
                });
            });
        </script>
    @endif
    <div class="max-w-7xl mx-auto px-4 py-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Kategori Buku</h2>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <a
                href="#"
                class="group relative overflow-hidden rounded-xl h-40 shadow-md"
            >
                <img
                    src="{{ asset('images/jurusan yg ini.png') }}"
                    alt="Komik"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                />

                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"
                ></div>

                <div class="absolute inset-0 flex items-end p-4">
                    <span
                        class="text-white font-bold text-sm leading-tight drop-shadow-lg"
                    >
                        Keahlian Kejuruan
                    </span>
                </div>
            </a>

            <a
                href="#"
                class="group relative overflow-hidden rounded-xl h-40 shadow-md"
            >
                <img
                    src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=500"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                />
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent"
                ></div>
                <div class="absolute inset-0 flex items-end p-4">
                    <span class="text-white font-bold text-sm"
                        >Kategori Cerpen & Prosa</span
                    >
                </div>
            </a>

            <a
                href="#"
                class="group relative overflow-hidden rounded-xl h-40 shadow-md"
            >
                <img
                    src="{{ asset('images/buku in lah .png') }}"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                />
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent"
                ></div>
                <div class="absolute inset-0 flex items-end p-4">
                    <span class="text-white font-bold text-sm">Pengetahuan Umum</span>
                </div>
            </a>
            
            <a
                href="#"
                class="group relative overflow-hidden rounded-xl h-40 shadow-md"
            >
                <img
                    src="https://images.unsplash.com/photo-1519074063912-ad2fe3f5198e?q=80&w=500"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                />
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent"
                ></div>
                <div class="absolute inset-0 flex items-end p-4">
                    <span class="text-white font-bold text-sm">Literacy & Languages</span>
                </div>
            </a>
            
            <a
                href="#"
                class="group relative overflow-hidden rounded-xl h-40 shadow-md"
            >
                <img
                    src="https://images.unsplash.com/photo-1519074063912-ad2fe3f5198e?q=80&w=500"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                />
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent"
                ></div>
                <div class="absolute inset-0 flex items-end p-4">
                    <span class="text-white font-bold text-sm">Kategori Komik</span>
                </div>
            </a>
        </div>
    </div>
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    <style>
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
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div id="katalog-kontainer">
                    @include ('partials.katalog_isi')
                </div>
            </div>

            @if ($dataBuku->isEmpty())
                <div class="text-center py-20 text-gray-500">
                    Belum ada koleksi buku.
                </div>
            @endif
        </div>
    </div>
    <script>
        function refreshKatalog() {
            let url = '{{ route("katalog") }}';

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then((response) => response.text())
                .then((html) => {
                    document.getElementById('katalog-kontainer').innerHTML = html;
                    console.log('Katalog diperbarui otomatis...');
                })
                .catch((error) => console.error('Error auto-refresh katalog:', error));
        }
        setInterval(refreshKatalog, 10000);
        document.getElementById('katalog-kontainer').addEventListener(
            'mouseover',
            (e) => {
                const row = e.target.closest('tr');
                if (row) {
                    const judul = row.cells[2] ? row.cells[2].innerText : '';
                    if (judul) {
                        console.log('Kursor masuk ke buku:', judul.trim());
                    }
                }
            },
            true
        );
    </script>
</x-app-layout>
