<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Daftar Siswa') }}
            </h2>

            <div class="d-flex align-items-center gap-2">
                <div id="bulk-edit-wrapper" class="mb-0" style="display: none">
                    <button
                        type="button"
                        onclick="openBulkEditModal()"
                        class="btn btn-warning font-bold shadow-sm"
                    >
                        <i class="fas fa-edit"></i> Edit Kelas (<span
                            id="selected-count"
                            >0</span
                        >
                        Data)
                    </button>
                </div>
                <div class="position-relative shadow-sm" style="width: 300px">
                    <span
                        class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"
                        style="z-index: 5"
                    >
                        <i class="bi bi-search"></i>
                    </span>
                    <input
                        type="text"
                        id="liveSearch"
                        class="form-control ps-5 pe-5"
                        placeholder="Cari peminjam atau buku..."
                        autocomplete="off"
                        style="border-radius: 8px; border: 1px solid #ddd"
                    />
                    <button
                        id="clearSearch"
                        class="btn position-absolute top-50 end-0 translate-middle-y me-2"
                        style="
                            display: none;
                            border: none;
                            background: transparent;
                        "
                    >
                        <i class="bi bi-x-circle-fill text-muted"></i>
                    </button>
                </div>
                <div class="dropdown" id="filterKelasContainer">
                    <button
                        class="btn btn-white border shadow-sm dropdown-toggle"
                        type="button"
                        id="dropdownFilterKelas"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        style="min-width: 100px; text-align: left"
                    >
                        <i
                            class="fas fa-chalkboard-teacher me-2 text-primary"
                        ></i>
                        <span id="selectedKelasLabel">Pilih Kelas</span>
                    </button>
                    <div
                        class="dropdown-menu p-3 shadow-lg border-0"
                        aria-labelledby="dropdownFilterKelas"
                        style="width: 300px; border-radius: 12px"
                    >
                        <div class="input-group input-group-sm mb-3">
                            <span class="input-group-text bg-light border-end-0"
                                ><i class="fas fa-search text-muted"></i
                            ></span>
                            <input
                                type="text"
                                id="inputSearchKelas"
                                class="form-control bg-light border-start-0"
                                placeholder="Cari kelas (contoh: XII PPLG)..."
                                autocomplete="off"
                            />
                        </div>

                        <div
                            id="listKelasScroll"
                            style="
                                max-height: 300px;
                                overflow-y: auto;
                                scrollbar-width: thin;
                            "
                        >
                            @php
        $jurusanConfig = [
            'PPLG' => ['levels' => ['X', 'XI', 'XII'], 'count' => 3],
            'APHP' => ['levels' => ['X', 'XI', 'XII'], 'count' => 3],
            'AKL'  => ['levels' => ['X', 'XI', 'XII'], 'count' => 3],
            'APAT' => ['levels' => ['X', 'XI', 'XII'], 'count' => 3],
            'TO'   => ['levels' => ['X'],           'count' => 6], // TO hanya kelas 10
            'TSM'  => ['levels' => ['XI', 'XII'],   'count' => 3], // TSM mulai kelas 11
            'TKR'  => ['levels' => ['XI', 'XII'],   'count' => 3], // TKR mulai kelas 11
        ];
    @endphp

                            @foreach ($jurusanConfig as $namaJurusan => $config)
                                <h6
                                    class="dropdown-header border-bottom mt-2 bg-light text-dark fw-bold"
                                >
                                    {{ $namaJurusan }}
                                </h6>
                                @foreach ($config['levels'] as $tkt)
                                    @for ($i = 1; $i <= $config['count']; $i++)
                                        @php $namaKelas = "$tkt $namaJurusan $i"; @endphp
                                        <button
                                            class="dropdown-item filter-opt"
                                            data-value="{{ $namaKelas }}"
                                            type="button"
                                        >
                                            {{ $namaKelas }}
                                        </button>
                                    @endfor
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    id="btnResetFilter"
                    class="btn btn-outline-danger shadow-sm"
                    style="display: none"
                    onclick="resetFilter()"
                >
                    <i class="fas fa-sync-alt"></i> Reset
                </button>
            </div>
        </div>
    </x-slot>
    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <table class="table table-bordered">
                <thead class="table-dark">
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
                <tbody id="siswaTableBody" class="text-center">
                    @include ('admin.table_siswa_rows')
                </tbody>
            </table>
            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // 1. Deklarasikan State secara Global di dalam ready agar bisa diakses semua fungsi
            let currentKelas = '';
            let currentSearch = '';

            // 2. Fungsi Utama AJAX (Satu pintu)
            function fetchSiswa() {
                $.ajax({
                    url: '{{ route('users.siswa') }}',
                    type: 'GET',
                    data: {
                        search: currentSearch,
                        filter_kelas: currentKelas,
                    },
                    beforeSend: function () {
                        $('#siswaTableBody').html(
                            '<tr><td colspan="7" class="text-center">Memuat data...</td></tr>'
                        );
                    },
                    success: function (data) {
                        $('#siswaTableBody').html(data);
                    },
                    error: function (xhr) {
                        console.error('Error Fetch:', xhr.responseText);
                    },
                });
            }

            // 3. EVENT: Live Search (Nama, NIS, Email, No HP)
            $('#liveSearch').on('keyup', function () {
                currentSearch = $(this).val();

                // Tampilkan/Sembunyikan tombol X (clear)
                if (currentSearch.length > 0) {
                    $('#clearSearch').fadeIn();
                } else {
                    $('#clearSearch').fadeOut();
                }

                fetchSiswa();
            });

            // 4. EVENT: Tombol Silang (Clear Search)
            $('#clearSearch').on('click', function () {
                currentSearch = '';
                $('#liveSearch').val('');
                $(this).fadeOut();
                fetchSiswa();
            });

            // 5. EVENT: Klik Pilihan Kelas di Dropdown
            $(document).on('click', '.filter-opt', function (e) {
                e.preventDefault();
                currentKelas = $(this).data('value');

                // Update UI
                $('#selectedKelasLabel').text(currentKelas);
                $('#btnResetFilter').fadeIn();

                fetchSiswa();
            });

            // 6. EVENT: Search DI DALAM Dropdown (Hanya visual filter list)
            $('#inputSearchKelas').on('keyup', function () {
                let value = $(this).val().toLowerCase();
                $('#listKelasScroll .filter-opt').each(function () {
                    let text = $(this).text().toLowerCase();
                    $(this).toggle(text.indexOf(value) > -1);
                });

                $('.dropdown-header').each(function () {
                    let nextItems = $(this).nextUntil(
                        '.dropdown-header',
                        '.filter-opt:visible'
                    );
                    $(this).toggle(nextItems.length > 0);
                });
            });

            // 7. EVENT: Reset Semua Filter
            window.resetFilter = function () {
                currentKelas = '';
                currentSearch = '';
                $('#liveSearch').val('');
                $('#inputSearchKelas').val('');
                $('#selectedKelasLabel').text('Pilih Kelas');
                $('#btnResetFilter').fadeOut();
                $('#clearSearch').fadeOut();
                $('.filter-opt, .dropdown-header').show();

                fetchSiswa();
            };
        });
    </script>
</x-app-layout>
