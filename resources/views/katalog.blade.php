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
    {{-- kategori buku --}}
    @include ('partials.kategori_buku')
    {{-- akhir kategori buku --}}

    {{-- card buku --}}
    <div class="py-12">
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
    {{-- akhir card buku --}}
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
