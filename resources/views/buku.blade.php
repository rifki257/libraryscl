<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Data Buku') }}
            </h2>

            <div class="d-flex align-items-center gap-2">
                <div class="input-group" style="max-width: 350px">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input
                        type="text"
                        id="search-input"
                        name="search"
                        class="form-control border-start-0 border-end-0 ps-0 shadow-none"
                        placeholder="Cari judul, penulis..."
                        value="{{ request('search') }}"
                        autocomplete="off"
                    />
                    <button
                        class="btn bg-white border border-start-0 {{ request('search') ? '' : 'd-none' }} d-flex align-items-center gap-1"
                        type="button"
                        id="reset-search"
                        style="z-index: 5"
                    >
                        <i class="bi bi-x-circle-fill text-danger"></i>
                        <span
                            style="font-size: 0.8rem"
                            class="text-muted fw-bold"
                            >Reset</span
                        >
                    </button>
                </div>

                <a
                    href="{{ route('buku.create') }}"
                    class="btn btn-success d-flex align-items-center gap-1"
                >
                    <i class="bi bi-plus-lg"></i> Tambah
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-sm sm:rounded-lg" style="background-color: rgb(235, 235, 235)">
                <div class="p-6 text-gray-900">
                    <div class="container">
                        <div class="card-body">
                            <div id="tabel-buku" class="mt-4">
                                @include ('partials.tabel_isi')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let timeout = null;
        const searchInput = document.getElementById('search-input');
        const resetBtn = document.getElementById('reset-search');

        function refreshTable() {
            let query = searchInput.value;
            let url = '{{ route("buku.search") }}';

            // Tampilkan/Sembunyikan tombol reset secara otomatis
            if (query.length > 0) {
                resetBtn.classList.remove('d-none');
            } else {
                resetBtn.classList.add('d-none');
            }

            fetch(`${url}?search=${query}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then((response) => response.text())
                .then((html) => {
                    document.getElementById('tabel-buku').innerHTML = html;
                })
                .catch((error) => console.error('Error auto-refresh:', error));
        }

        // Event saat mengetik
        searchInput.addEventListener('keyup', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                refreshTable();
            }, 300);
        });

        // Event Klik tombol Reset
        resetBtn.addEventListener('click', function () {
            searchInput.value = '';
            refreshTable();
            searchInput.focus();
        });

        // Auto refresh setiap 10 detik
        setInterval(() => {
            refreshTable();
        }, 10000);
    </script>
</x-app-layout>
