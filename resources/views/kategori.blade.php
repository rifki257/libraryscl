<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            {{-- Sisi Kiri: Tombol Kembali --}}
            <a
                href="{{ route('buku') }}"
                class="btn btn-secondary d-flex align-items-center justify-content-center"
                style="width: 80px;"
            >
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            {{-- Sisi Kanan: Search & Tambah (Digabung dalam satu div flex) --}}
            <div class="d-flex align-items-center gap-2">
                {{-- Container Search --}}
                <div class="position-relative" style="width: 300px">
                    <span
                        class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"
                    >
                        <i class="bi bi-search"></i>
                    </span>
                    <input
                        type="text"
                        id="liveSearch"
                        class="form-control ps-5 pe-5"
                        placeholder="Cari kategori..."
                        style="
                            border-radius: 8px;
                            border: 1px solid #ddd;
                        "
                        autocomplete="off"
                    />
                    {{-- Tombol X (Reset) --}}
                    <button
                        id="clearSearch"
                        class="btn position-absolute top-50 end-0 translate-middle-y me-2 text-muted"
                        style="
                            display: none;
                            border: none;
                            background: transparent;
                        "
                        type="button"
                    >
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                </div>

                {{-- Tombol Tambah --}}
                <button
                    x-data=""
                    x-on:click.prevent="
                        $dispatch('open-modal', 'tambah-kategori')
                    "
                    class="btn btn-success d-flex align-items-center gap-2 px-3"
                    style="border-radius: 8px;"
                >
                    <i class="bi bi-plus-lg"></i>
                    <span class="fw-bold text-nowrap">Kategori</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr class="text-capitalize text-center">
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Kategori</th>
                        <th>Total buku</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="kategori-table-body">
                    @include ('admin.table_rows')
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-10">{{ $kategoris->links() }}</div>
    {{-- halaman buat --}}
    <x-modal name="tambah-kategori" focusable>
        <form
            method="post"
            action="{{ route('kategori.store') }}"
            class="p-6"
            enctype="multipart/form-data"
        >
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                Tambah Kategori Baru
            </h2>

            <div class="mt-6">
                <x-input-label for="nama_kategori" value="Nama Kategori" />
                <x-text-input
                    id="nama_kategori"
                    name="nama_kategori"
                    type="text"
                    class="form-control"
                    placeholder="Nama Kategori"
                    required
                />
            </div>

            <div class="mt-4">
                <x-input-label for="gambar" value="Gambar Kategori" />
                <input
                    type="file"
                    id="gambar"
                    name="gambar"
                    class="form-control"
                    accept="image/*"
                    required
                />
                @error ('gambar')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>
                <x-primary-button class="ms-3"> Simpan </x-primary-button>
            </div>
        </form>
    </x-modal>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('liveSearch');
            const clearBtn = document.getElementById('clearSearch');
            const tableBody = document.getElementById('kategori-table-body'); // Pastikan <tbody> punya ID ini

            // Fungsi utama pencarian
            function performSearch(query) {
                fetch('{{ route('kategori.buku') }}?search=' + query, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                })
                    .then((response) => response.json())
                    .then((data) => {
                        tableBody.innerHTML = data.html;
                    })
                    .catch((error) => console.error('Error:', error));
            }

            // Event saat mengetik
            searchInput.addEventListener('keyup', function () {
                let value = this.value;

                // Munculkan/Sembunyikan tombol X
                if (value.length > 0) {
                    clearBtn.style.display = 'block';
                } else {
                    clearBtn.style.display = 'none';
                }

                performSearch(value);
            });

            // Event saat tombol X diklik
            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                this.style.display = 'none';
                searchInput.focus();
                performSearch(''); // Reset tabel ke data awal
            });
        });
    </script>
</x-app-layout>
