<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div
            class="d-flex align-items-center justify-content-between flex-wrap gap-3"
        >
            <div class="d-flex align-items-center gap-4 flex-grow-1">
                <h2
                    class="font-semibold text-xl text-gray-800 leading-tight mb-0"
                >
                    {{ __('Pengembalian') }}
                </h2>

                <ul
                    class="nav nav-tabs border-bottom-0"
                    id="returnTab"
                    role="tablist"
                >
                    <li class="nav-item" role="presentation">
                        <button
    class="nav-link active fw-bold text-gray-600"
    id="confirmation-tab"
    data-bs-toggle="tab"
    data-bs-target="#confirmation-pane"
    type="button"
    role="tab"
>
    <i class="bi bi-check2-circle me-1"></i> Konfirmasi Pengembalian
    
    <span class="badge bg-danger ms-1" id="pending-count">
        {{ $totalKonfirmasi }}
    </span>
</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link fw-bold text-gray-600"
                            id="all-data-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#all-data-pane"
                            type="button"
                            role="tab"
                        >
                            <i class="bi bi-collection-play me-1"></i> Semua
                            Data
                        </button>
                    </li>
                </ul>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="input-group" style="max-width: 300px">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input
                        type="text"
                        id="search-input"
                        class="form-control border-start-0 border-end-0 ps-0 shadow-none"
                        placeholder="Cari..."
                        autocomplete="off"
                    />
                    <button
                        class="btn bg-white border border-start-0 d-none d-flex align-items-center gap-1"
                        type="button"
                        id="reset-search"
                    >
                        <i class="bi bi-x-circle-fill text-danger"></i>
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
                                    >Denda</label
                                >
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
                                    >Tepat Waktu</label
                                >
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
            <div class="tab-content" id="returnTabContent">
                <div
                    class="tab-pane fade show active"
                    id="confirmation-pane"
                    role="tabpanel"
                    tabindex="0"
                >
                    @include ('partials.konfirmasi_pengembalian')
                </div>

                <div
                    class="tab-pane fade"
                    id="all-data-pane"
                    role="tabpanel"
                    tabindex="0"
                >
                    @include ('partials.data_pengembalian')
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-input');
            const btnResetSearch = document.getElementById('reset-search');
            const btnResetFilter = document.getElementById('btn-reset-filter');
            const filters = document.querySelectorAll('.filter-checkbox');
            const rows = document.querySelectorAll('.item-peminjaman');
            const dropdownToggle = document.querySelector(
                '[data-bs-toggle="dropdown"]'
            );

            function applyAllFilters() {
                const searchText = searchInput.value.toLowerCase();

                let activeFilterValue = null;
                filters.forEach((f) => {
                    if (f.checked) activeFilterValue = f.value;
                });

                if (searchText.length > 0) {
                    btnResetSearch.classList.remove('d-none');
                } else {
                    btnResetSearch.classList.add('d-none');
                }

                rows.forEach((row) => {
                    const textContent = row.innerText.toLowerCase();
                    const textMatch = textContent.includes(searchText);

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

            function closeDropdown() {
                if (window.bootstrap && bootstrap.Dropdown) {
                    const instance =
                        bootstrap.Dropdown.getOrCreateInstance(dropdownToggle);
                    instance.hide();
                } else {
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
