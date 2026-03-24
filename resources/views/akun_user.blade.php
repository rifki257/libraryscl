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
                        style="min-width: 200px"
                    >
                        <li>
                            <div class="form-check mb-2">
                                <input
                                    class="form-check-input filter-kelas-checkbox"
                                    type="checkbox"
                                    value="X PPLG 1"
                                    id="kelas1"
                                    onchange="filterTableByKelas()"
                                />
                                <label class="form-check-label" for="kelas1"
                                    >X PPLG 1</label
                                >
                            </div>
                        </li>
                        <li>
                            <div class="form-check mb-2">
                                <input
                                    class="form-check-input filter-kelas-checkbox"
                                    type="checkbox"
                                    value="X PPLG 2"
                                    id="kelas2"
                                    onchange="filterTableByKelas()"
                                />
                                <label class="form-check-label" for="kelas2"
                                    >X PPLG 2</label
                                >
                            </div>
                        </li>
                        <li>
                            <div class="form-check mb-2">
                                <input
                                    class="form-check-input filter-kelas-checkbox"
                                    type="checkbox"
                                    value="X PPLG 3"
                                    id="kelas3"
                                    onchange="filterTableByKelas()"
                                />
                                <label class="form-check-label" for="kelas3"
                                    >X PPLG 3</label
                                >
                            </div>
                        </li>

                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <button
                                class="btn btn-sm btn-light w-100"
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container">
                        <div class="card-body">
                            <div id="tabel-akun" class="mt-4">
                                <div class="flex justify-between mb-4">
                                    <button
                                        id="btn-bulk-edit"
                                        class="btn btn-warning d-none"
                                        onclick="bulkEditKelas()"
                                    >
                                        Edit Kelas Terpilih (<span
                                            id="count-selected"
                                            >0</span
                                        >)
                                    </button>
                                </div>
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>
                                                <input
                                                    type="checkbox"
                                                    id="select-all"
                                                    onclick="toggleSelectAll()"
                                                />
                                            </th>
                                            <th>NIS</th>
                                            <th>Nama</th>
                                            <th>Kelas</th>
                                            <th>Email</th>
                                            <th>No HP</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @foreach ($users as $user)
                                            <tr
                                                class="align-middle border-b hover:bg-gray-50"
                                            >
                                                <td>
                                                    <input
                                                        type="checkbox"
                                                        class="user-checkbox"
                                                        value="{{ $user->id }}"
                                                    />
                                                </td>
                                                <td>{{ $user->nis ?? '-' }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>
                                                    {{ $user->kelas ?? '-' }}
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    {{ $user->no_hp ?? '-' }}
                                                </td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        onclick="editPassword({{ $user->id }}, '{{ $user->name }}')"
                                                        class="btn btn-primary text-white"
                                                    >
                                                        Password
                                                    </button>
                                                    <form
                                                        id="delete-form-{{ $user->id }}"
                                                        action="{{ route('user.destroy', $user->id) }}"
                                                        method="POST"
                                                        class="inline-block m-0"
                                                    >
                                                        @csrf
                                                        @method ('DELETE')
                                                        <button
                                                            type="button"
                                                            onclick="confirmDelete({{ $user->id }})"
                                                            class="btn btn-danger"
                                                        >
                                                            Hapus
                                                        </button>
                                                    </form>
                                                    <form
                                                        id="update-pw-form-{{ $user->id }}"
                                                        action="{{ route('user.updatePassword', $user->id) }}"
                                                        method="POST"
                                                        style="display: none"
                                                    >
                                                        @csrf
                                                        @method ('PUT')
                                                        <input
                                                            type="hidden"
                                                            name="password"
                                                            id="pw-input-{{ $user->id }}"
                                                        />
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <form
                                    id="bulk-update-form"
                                    action="{{ route('user.bulkUpdateKelas') }}"
                                    method="POST"
                                    style="display: none"
                                >
                                    @csrf
                                    @method ('PUT')
                                    <input
                                        type="hidden"
                                        name="ids"
                                        id="bulk-ids"
                                    />
                                    <input
                                        type="hidden"
                                        name="kelas"
                                        id="bulk-kelas"
                                    />
                                </form>
                            </div>
                        </div>
                    </div>
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
            let table = document.querySelector('table');
            let tr = table.getElementsByTagName('tr');
            let btnReset = document.getElementById('reset-search-user');

            // Toggle tombol reset
            if (filter.length > 0) {
                btnReset.classList.remove('d-none');
                btnReset.classList.add('d-flex');
            } else {
                btnReset.classList.add('d-none');
                btnReset.classList.remove('d-flex');
            }

            for (let i = 1; i < tr.length; i++) {
                let tds = tr[i].getElementsByTagName('td');
                if (tds.length > 0) {
                    let combinedText = Array.from(tds)
                        .slice(0, 5)
                        .map((td) => td.textContent || td.innerText)
                        .join(' ')
                        .toLowerCase();

                    tr[i].style.display =
                        combinedText.indexOf(filter) > -1 ? '' : 'none';
                }
            }
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
            const checkedCount = document.querySelectorAll(
                '.user-checkbox:checked'
            ).length;
            if (checkedCount > 0) {
                btnBulkEdit.classList.remove('d-none');
                if (countSpan) countSpan.innerText = checkedCount;
            } else {
                btnBulkEdit.classList.add('d-none');
            }
        }

        function toggleSelectAll() {
            const selectAllCb = document.getElementById('select-all');
            const tr = document.querySelectorAll('table tbody tr');

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
                // Menghentikan event klik agar tidak memicu penutupan dropdown otomatis
                e.stopPropagation();
            });
        });
    </script>
</x-app-layout>
