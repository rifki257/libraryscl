<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('akun user') }}
            </h2>
            <div class="gap-2">
                <button
                    id="btn-bulk-edit"
                    class="btn btn-warning d-none"
                    onclick="bulkEditKelas()"
                >
                    Edit Kelas (<span id="count-selected">0</span>)
                </button>
                <button
                    id="btn-bulk-delete"
                    class="btn btn-danger d-none"
                    onclick="bulkDeleteUser()"
                >
                    <i class="bi bi-trash"></i> Hapus Akun (<span
                        id="count-selected-delete"
                        >0</span
                    >)
                </button>
            </div>

            <ul class="nav nav-tabs" id="kelasTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link active"
                        id="x10-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#x10"
                        type="button"
                        role="tab"
                    >
                        Kelas X
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link"
                        id="xi11-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#xi11"
                        type="button"
                        role="tab"
                    >
                        Kelas XI
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link"
                        id="xii12-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#xii12"
                        type="button"
                        role="tab"
                    >
                        Kelas XII
                    </button>
                </li>
            </ul>

            <div class="flex items-center gap-2">
                <div class="input-group" style="width: 300px">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input
                        type="text"
                        id="search-input-user"
                        class="form-control border-start-0 border-end-0 ps-0 shadow-none"
                        placeholder="Cari..."
                        onkeyup="searchTableUser()"
                        autocomplete="off"
                    />
                    <button
                        class="btn bg-white border border-start-0 d-none"
                        type="button"
                        id="reset-search-user"
                        onclick="resetTableUser()"
                    >
                        <i class="bi bi-x-circle-fill text-danger"></i>
                    </button>
                </div>

                <div class="dropdown">
                    <button
                        class="btn btn-outline-dark dropdown-toggle d-flex align-items-center gap-2"
                        type="button"
                        data-bs-toggle="dropdown"
                        style="height: 38px"
                    >
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <ul
                        class="dropdown-menu dropdown-menu-end p-3"
                        id="filter-wrapper"
                        style="
                            min-width: 250px;
                            max-height: 400px;
                            overflow-y: auto;
                        "
                    >
                        @php
        $jurusans = [
            'PPLG' => 3,
            'APHP' => 3,
            'APAT' => 3,
            'TO'   => 6,
            'AKL'  => 3
        ];
        $tingkatans = ['X' => 'group-x', 'XI' => 'group-xi', 'XII' => 'group-xii'];
    @endphp

                        @foreach ($tingkatans as $romawi => $classGroup)
                            <div
                                class="filter-group {{ $classGroup }} {{ $romawi != 'X' ? 'd-none' : '' }}"
                            >
                                <li>
                                    <h5
                                        class="dropdown-header ps-0 text-dark fw-bold"
                                        style="font-size: 1.1rem"
                                    >
                                        KELAS {{ $romawi }}
                                    </h5>
                                </li>

                                @foreach ($jurusans as $jur => $jumlah)
                                    <li><hr class="dropdown-divider" /></li>
                                    <li>
                                        <h6
                                            class="dropdown-header ps-0 text-primary fw-bold"
                                        >
                                            {{ $jur }}
                                        </h6>
                                    </li>
                                    @for ($i = 1; $i <= $jumlah; $i++)
                                        <li>
                                            <div class="form-check mb-1">
                                                <input
                                                    class="form-check-input filter-kelas-checkbox"
                                                    type="checkbox"
                                                    value="{{ $romawi }} {{ $jur }} {{ $i }}"
                                                    id="chk{{ $romawi }}{{ $jur }}{{ $i }}"
                                                    onchange="
                                                        filterTableByKelas()
                                                    "
                                                />
                                                <label
                                                    class="form-check-label"
                                                    for="chk{{ $romawi }}{{ $jur }}{{ $i }}"
                                                >
                                                    {{ $romawi }} {{ $jur }} {{ $i }}
                                                </label>
                                            </div>
                                        </li>
                                    @endfor
                                @endforeach
                            </div>
                        @endforeach

                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <button
                                class="btn btn-sm btn-danger w-100"
                                onclick="resetFilterKelas()"
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
            <div
                class="overflow-hidden shadow-sm sm:rounded-lg"
                style="background-color: rgb(235, 235, 235)"
            >
                <div class="p-6 text-gray-900">
                    <div class="tab-content" id="kelasTabContent">
                        <div
                            class="tab-pane fade show active"
                            id="x10"
                            role="tabpanel"
                        >
                            @include ('partials.x10')
                        </div>
                        <div class="tab-pane fade" id="xi11" role="tabpanel">
                            @include ('partials.xi11')
                        </div>
                        <div class="tab-pane fade" id="xii12" role="tabpanel">
                            @include ('partials.xii12')
                        </div>
                    </div>

                    <form
                        id="bulk-update-form"
                        action="{{ route('user.bulkUpdateKelas') }}"
                        method="POST"
                        style="display: none"
                    >
                        @csrf
                        @method ('PUT')
                        <input type="hidden" name="ids" id="bulk-ids" />
                        <input type="hidden" name="kelas" id="bulk-kelas" />
                    </form>
                    <form
                        id="bulk-delete-form"
                        action="{{ route('user.bulkDestroy') }}"
                        method="POST"
                        style="display: none"
                    >
                        @csrf
                        @method ('DELETE')
                        <input type="hidden" name="ids" id="bulk-delete-ids" />
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        /* -------------------------------------------------------------------------- */
        /* 1. NOTIFIKASI & AKSI SINGLE                        */
        /* -------------------------------------------------------------------------- */

        @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2000,
        });
        @endif

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Akun?',
                text: 'Data ini tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        function editPassword(id, name) {
            Swal.fire({
                title: 'Ganti Password',
                text: 'Masukkan password baru untuk ' + name,
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocorrect: 'off',
                },
                showCancelButton: true,
                confirmButtonText: 'Update Password',
                confirmButtonColor: '#3b82f6',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) return 'Password tidak boleh kosong!';
                    if (value.length < 8) return 'Password minimal 8 karakter!';
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('pw-input-' + id).value = result.value;
                    document.getElementById('update-pw-form-' + id).submit();
                }
            });
        }

        /* -------------------------------------------------------------------------- */
        /* 2. PENCARIAN (SEARCH TABLE)                        */
        /* -------------------------------------------------------------------------- */

        function searchTableUser() {
            let input = document.getElementById('search-input-user');
            let filter = input.value.toLowerCase();
            let btnReset = document.getElementById('reset-search-user');

            if (filter.length > 0) {
                btnReset.classList.remove('d-none');
                btnReset.classList.add('d-flex');
            } else {
                btnReset.classList.add('d-none');
                btnReset.classList.remove('d-flex');
            }
            let rows = document.querySelectorAll('.tab-content tbody tr');

            rows.forEach((row) => {
                let tds = row.getElementsByTagName('td');
                if (tds.length > 0) {
                    let combinedText = Array.from(tds)
                        .slice(1, 6)
                        .map((td) => td.textContent || td.innerText)
                        .join(' ')
                        .toLowerCase();

                    if (combinedText.indexOf(filter) > -1) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }
        function resetTableUser() {
            let input = document.getElementById('search-input-user');
            input.value = '';
            searchTableUser();
            input.focus();
        }

        /* -------------------------------------------------------------------------- */
        /* 3. FILTER & SELEKSI MASSAL                         */
        /* -------------------------------------------------------------------------- */

        const selectAll = document.getElementById('select-all');
        const btnBulkEdit = document.getElementById('btn-bulk-edit');
        const countSpan = document.getElementById('count-selected');

        function updateBulkButton() {
            const btnBulkEdit = document.getElementById('btn-bulk-edit');
            const btnBulkDelete = document.getElementById('btn-bulk-delete');
            const countEdit = document.getElementById('count-selected');
            const countDelete = document.getElementById('count-selected-delete');

            // Ambil tab aktif
            const activeTab = document
                .querySelector('.nav-link.active')
                .getAttribute('data-bs-target');

            // Ambil semua checkbox yang dicentang
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            const checkedCount = checkedBoxes.length;

            if (checkedCount > 0) {
                countEdit.innerText = checkedCount;
                countDelete.innerText = checkedCount;

                btnBulkEdit.classList.remove('d-none');

                if (activeTab === '#xii12') {
                    btnBulkDelete.classList.remove('d-none');
                    btnBulkDelete.classList.add('d-inline-block');
                } else {
                    btnBulkDelete.classList.add('d-none');
                }
            } else {
                btnBulkEdit.classList.add('d-none');
                btnBulkDelete.classList.add('d-none');
            }
        }
        function bulkDeleteUser() {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            const ids = Array.from(checkedBoxes).map((cb) => cb.value);

            if (ids.length === 0) return;

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan menghapus ${ids.length} akun terpilih secara permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('bulk-delete-ids').value =
                        JSON.stringify(ids);
                    document.getElementById('bulk-delete-form').submit();
                }
            });
        }

        function toggleSelectAll() {
            const activeTab = document.querySelector('.tab-pane.active');
            const selectAllCb = activeTab.querySelector('#select-all');
            const tr = activeTab.querySelectorAll('tbody tr');
            tr.forEach((row) => {
                if (row.style.display !== 'none') {
                    const checkbox = row.querySelector('.user-checkbox');
                    if (checkbox) checkbox.checked = selectAllCb.checked;
                }
            });
            updateBulkButton();
        }
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('user-checkbox')) {
                updateBulkButton();
            }
        });

        function filterTableByKelas() {
            let selectedKelas = Array.from(
                document.querySelectorAll('.filter-kelas-checkbox:checked')
            ).map((cb) => cb.value.toLowerCase());

            let tr = document.querySelectorAll('table tbody tr');

            tr.forEach((row) => {
                let tdKelas = row.getElementsByTagName('td')[3];
                if (tdKelas) {
                    let textValue = (tdKelas.textContent || tdKelas.innerText)
                        .toLowerCase()
                        .trim();

                    if (
                        selectedKelas.length === 0 ||
                        selectedKelas.includes(textValue)
                    ) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });

            document.getElementById('select-all').checked = false;
            document
                .querySelectorAll('.user-checkbox')
                .forEach((cb) => (cb.checked = false));
            updateBulkButton();
        }

        function resetFilterKelas() {
            document
                .querySelectorAll('.filter-kelas-checkbox')
                .forEach((cb) => (cb.checked = false));
            filterTableByKelas();
        }

        function bulkEditKelas() {
            const selectedCheckboxes = Array.from(
                document.querySelectorAll('.user-checkbox:checked')
            );

            if (selectedCheckboxes.length === 0) return;

            const kelasMap = {};
            selectedCheckboxes.forEach((cb) => {
                const row = cb.closest('tr');
                const namaKelas = row.getElementsByTagName('td')[3].innerText.trim();

                if (!kelasMap[namaKelas]) {
                    kelasMap[namaKelas] = [];
                }
                kelasMap[namaKelas].push(cb.value);
            });

            let formHtml = '<div style="text-align: left; font-size: 0.9rem;">';
            Object.keys(kelasMap).forEach((namaKelas, index) => {
                formHtml += `
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Ubah Kelas: <b>${namaKelas}</b> (${kelasMap[namaKelas].length} murid)</label>
                                                                                <input type="text" id="bulk-input-${index}" class="swal2-input mt-1"
                                                                                        value="${namaKelas}" placeholder="Masukkan nama kelas baru...">
                                                                                <input type="hidden" id="ids-${index}" value='${JSON.stringify(kelasMap[namaKelas])}'>
                                                                            </div>
                                                                        `;
            });
            formHtml += '</div>';

            Swal.fire({
                title: 'Update Nama Kelas',
                html: formHtml,
                showCancelButton: true,
                confirmButtonText: 'Update Semua',
                confirmButtonColor: '#3b82f6',
                preConfirm: () => {
                    const results = [];
                    Object.keys(kelasMap).forEach((_, index) => {
                        results.push({
                            ids: JSON.parse(
                                document.getElementById(`ids-${index}`).value
                            ),
                            new_kelas: document.getElementById(`bulk-input-${index}`)
                                .value,
                        });
                    });
                    return results;
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('bulk-ids').value = JSON.stringify(
                        result.value
                    );
                    document.getElementById('bulk-update-form').submit();
                }
            });
        }
        /* -------------------------------------------------------------------------- */
        /* 4. FIX: CEGAH DROPDOWN FILTER MENUTUP SAAT KLIK CHECKBOX                    */
        /* -------------------------------------------------------------------------- */

        document.querySelectorAll('.dropdown-menu').forEach(function (element) {
            element.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        });
        function filterByTingkat(tingkat) {
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach((row) => {
                const kelasText = row.cells[3].innerText.trim();

                if (tingkat === 'all') {
                    row.style.display = '';
                } else {
                    if (kelasText.startsWith(tingkat)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });

            document.getElementById('select-all').checked = false;
            toggleSelectAll();
        }
        // filter
        document.addEventListener('DOMContentLoaded', function () {
            const tabLinks = document.querySelectorAll('button[data-bs-toggle="tab"]');

            tabLinks.forEach((tab) => {
                tab.addEventListener('shown.bs.tab', function (event) {
                    const targetId = event.target.getAttribute('data-bs-target');

                    document.querySelectorAll('.filter-group').forEach((group) => {
                        group.classList.add('d-none');
                    });

                    if (targetId === '#x10') {
                        document.querySelector('.group-x').classList.remove('d-none');
                    } else if (targetId === '#xi11') {
                        document.querySelector('.group-xi').classList.remove('d-none');
                    } else if (targetId === '#xii12') {
                        document.querySelector('.group-xii').classList.remove('d-none');
                    }

                    resetFilterKelas();
                });
            });
        });
    </script>
</x-app-layout>
