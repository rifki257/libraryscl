<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pengembalian') }}
            </h2>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group" style="max-width: 350px">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input
                        type="text"
                        id="search-input"
                        class="form-control border-start-0 border-end-0 ps-0 shadow-none"
                        placeholder="Cari judul, peminjam..."
                        autocomplete="off"
                    />
                    <button
                        class="btn bg-white border border-start-0 d-none d-flex align-items-center gap-1"
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

                <div class="dropdown">
                    <button
                        class="btn btn-outline-dark dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                    >
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <ul
                        class="dropdown-menu dropdown-menu-end p-3"
                        style="min-width: 200px"
                    >
                        <li>
                            <div class="form-check mb-2">
                                <input
                                    class="form-check-input filter-checkbox"
                                    type="radio"
                                    name="status-filter"
                                    value="status-terlambat"
                                    id="fTelat"
                                />
                                <label
                                    class="form-check-label text-danger fw-bold"
                                    for="fTelat"
                                >
                                    Denda
                                </label>
                            </div>
                        </li>
                        <li>
                            <div class="form-check mb-2">
                                <input
                                    class="form-check-input filter-checkbox"
                                    type="radio"
                                    name="status-filter"
                                    value="status-aman"
                                    id="fAman"
                                />
                                <label
                                    class="form-check-label text-success fw-bold"
                                    for="fAman"
                                >
                                    Tepat Waktu
                                </label>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <button
                                class="btn btn-sm btn-light w-100"
                                id="btn-reset-filter"
                            >
                                <i class="bi bi-arrow-counterclockwise"></i>
                                Reset
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container">
                        <div class="card-body">
                            <div id="tabel-buku" class="mt-4">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>ID Pinjam</th>
                                            <th>Peminjam</th>
                                            <th>Judul Buku</th>
                                            <th>Tgl Pinjam</th>
                                            <th>Tgl Kembali</th>
                                            <th>Denda</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($dikembalikan as $data)
                                            @php
        $classStatus = ($data->denda > 0) ? 'status-terlambat' : 'status-aman';
    @endphp
                                            <tr
                                                class="item-peminjaman {{ $classStatus }}"
                                            >
                                                <td>
                                                    <code
                                                        >{{ $data->id_pinjam }}</code
                                                    >
                                                </td>
                                                <td>{{ $data->user->name }}</td>
                                                <td>
                                                    {{ $data->buku->judul }}
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($data->tgl_pinjam)->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($data->updated_at)->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    @if ($data->denda > 0)
                                                        <span
                                                            class="text-danger fw-bold"
                                                            >Rp {{ number_format($data->denda, 0, ',', '.') }}</span
                                                        >
                                                    @else
                                                        <span
                                                            class="text-success"
                                                            >Tidak Ada</span
                                                        >
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-4">
                                                    Belum ada riwayat
                                                    pengembalian.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-input');
            const btnResetSearch = document.getElementById('reset-search');
            const btnResetFilter = document.getElementById('btn-reset-filter');
            const filters = document.querySelectorAll('.filter-checkbox'); // Ini adalah radio buttons
            const rows = document.querySelectorAll('.item-peminjaman');
            const dropdownToggle = document.querySelector(
                '[data-bs-toggle="dropdown"]'
            );

            function applyAllFilters() {
                const searchText = searchInput.value.toLowerCase();

                // Mencari radio button mana yang aktif
                let activeFilterValue = null;
                filters.forEach((f) => {
                    if (f.checked) activeFilterValue = f.value;
                });

                // Toggle tombol reset search
                if (searchText.length > 0) {
                    btnResetSearch.classList.remove('d-none');
                } else {
                    btnResetSearch.classList.add('d-none');
                }

                rows.forEach((row) => {
                    const textContent = row.innerText.toLowerCase();
                    const textMatch = textContent.includes(searchText);

                    // Logika Filter: Jika tidak ada filter aktif, tampilkan semua yang cocok dengan text
                    // Jika ada filter aktif, cek apakah row punya class tersebut
                    const filterMatch =
                        !activeFilterValue ||
                        row.classList.contains(activeFilterValue);

                    if (textMatch && filterMatch) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Fungsi untuk menutup dropdown secara manual jika bootstrap instance gagal dipanggil
            function closeDropdown() {
                if (window.bootstrap && bootstrap.Dropdown) {
                    const instance =
                        bootstrap.Dropdown.getOrCreateInstance(dropdownToggle);
                    instance.hide();
                } else {
                    // Cara fallback jika bootstrap JS belum siap
                    dropdownToggle.click();
                }
            }

            searchInput.addEventListener('input', applyAllFilters);

            filters.forEach((radio) => {
                radio.addEventListener('change', function () {
                    applyAllFilters();
                    closeDropdown();
                });
            });

            btnResetSearch.addEventListener('click', function () {
                searchInput.value = '';
                applyAllFilters();
                searchInput.focus();
            });

            btnResetFilter.addEventListener('click', function () {
                filters.forEach((f) => (f.checked = false));
                applyAllFilters();
                closeDropdown();
            });
        });
    </script>
</x-app-layout>
