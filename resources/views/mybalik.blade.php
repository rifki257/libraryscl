<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div
            class="d-flex flex-wrap justify-content-between align-items-center gap-3"
        >
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Riwayat Pengembalian') }}
            </h2>

            <div
                class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end"
            >
                <div class="input-group" style="max-width: 350px">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input
                        type="text"
                        id="searchRiwayat"
                        class="form-control border-start-0 border-end-0 ps-0 shadow-none"
                        placeholder="Cari judul buku..."
                    />
                    <button
                        class="btn bg-white border border-start-0 d-none"
                        type="button"
                        id="resetSearchRiwayat"
                    >
                        <i class="bi bi-x-circle-fill text-danger"></i>
                    </button>
                </div>

                <div class="dropdown">
                    <button
                        class="btn btn-outline-dark dropdown-toggle"
                        type="button"
                        id="filterDropdown"
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
                                    class="form-check-input filter-riwayat"
                                    type="radio"
                                    name="filterStatus"
                                    value="status-tepat"
                                    id="ft1"
                                />
                                <label
                                    class="form-check-label text-success fw-bold"
                                    for="ft1"
                                    >Tepat Waktu</label
                                >
                            </div>
                        </li>
                        <li>
                            <div class="form-check mb-2">
                                <input
                                    class="form-check-input filter-riwayat"
                                    type="radio"
                                    name="filterStatus"
                                    value="status-denda"
                                    id="ft2"
                                />
                                <label
                                    class="form-check-label text-danger fw-bold"
                                    for="ft2"
                                    >Ada Denda</label
                                >
                            </div>
                        </li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <button
                                class="btn btn-sm btn-light w-100"
                                type="button"
                                id="btnResetFilter"
                            >
                                <i class="bi bi-arrow-counterclockwise"></i>
                                Reset Filter
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
                            <div id="tabel-mybalik" class="mt-4">
                                @include ('partials.kembalikan')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchRiwayat');
            const btnResetSearch =
                document.getElementById('resetSearchRiwayat');
            const btnResetFilter = document.getElementById('btnResetFilter');
            const filterRadios = document.querySelectorAll('.filter-riwayat');
            const rows = document.querySelectorAll('.item-peminjaman');

            function applyFilters() {
                const searchTerm = searchInput.value.toLowerCase();
                const activeFilter = Array.from(filterRadios).find(
                    (r) => r.checked
                )?.value;

                if (searchTerm.length > 0) {
                    btnResetSearch.classList.remove('d-none');
                } else {
                    btnResetSearch.classList.add('d-none');
                }

                rows.forEach((row) => {
                    const text = row.innerText.toLowerCase();
                    const matchesSearch = text.includes(searchTerm);

                    const matchesFilter =
                        !activeFilter || row.classList.contains(activeFilter);

                    if (matchesSearch && matchesFilter) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            searchInput.addEventListener('input', applyFilters);

            btnResetSearch.addEventListener('click', function () {
                searchInput.value = '';
                applyFilters();
            });

            filterRadios.forEach((radio) => {
                radio.addEventListener('change', function () {
                    applyFilters();

                    const dropdownElement =
                        document.getElementById('filterDropdown');
                    const dropdownInstance =
                        bootstrap.Dropdown.getInstance(dropdownElement) ||
                        new bootstrap.Dropdown(dropdownElement);
                    if (dropdownInstance) dropdownInstance.hide();
                });
            });

            btnResetFilter.addEventListener('click', function () {
                filterRadios.forEach((r) => (r.checked = false));
                applyFilters();

                const dropdownElement =
                    document.getElementById('filterDropdown');
                const dropdownInstance =
                    bootstrap.Dropdown.getInstance(dropdownElement) ||
                    new bootstrap.Dropdown(dropdownElement);
                if (dropdownInstance) dropdownInstance.hide();
            });
        });
    </script>
</x-app-layout>
