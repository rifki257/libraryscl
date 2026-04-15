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
                        id="btn-bulk-edit"
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
                    <button
                        id="btn-make-alumni"
                        type="button"
                        onclick="makeAlumni()"
                        class="btn btn-outline-dark shadow-sm"
                    >
                        <i class="fas fa-graduation-cap"></i> Set Alumni (Kelas
                        12)
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
                        <div class="input-group input-group-sm mb-3"
                        <span
                            class="input-group-text bg-light border-end-0"
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
                        <h2
                            class="dropdown-header border-bottom bg-dark text-white fw-bold"
                        >
                            STATUS KHUSUS
                        </h2>
                        <button
                            class="dropdown-item filter-opt text-danger fw-bold"
                            data-value="alumni"
                            type="button"
                        >
                            <i class="fas fa-graduation-cap me-2"></i> DATA
                            ALUMNI
                        </button>
                        <hr class="dropdown-divider" />
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
                    <div
                        class="modal fade"
                        id="bulkEditModal"
                        tabindex="-1"
                        aria-hidden="true"
                    >
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Edit Kelas Secara Masal
                                    </h5>
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="Close"
                                    ></button>
                                </div>
                                <div class="modal-body">
                                    <p>Ubah kelas untuk <span id="modal-selected-count" class="fw-bold">0</span> siswa yang dipilih.</p>
                                    <div class="form-group">
                                        <label
                                            for="new_kelas"
                                            class="form-label"
                                            >Pilih Kelas Baru</label
                                        >
                                        <select
                                            id="new_kelas"
                                            class="form-select"
                                        >
                                            <option value="">
                                                -- Pilih Kelas --
                                            </option>
                                            @foreach ($jurusanConfig as $namaJurusan => $config)
                                                @foreach ($config['levels'] as $tkt)
                                                    @for ($i = 1; $i <= $config['count']; $i++)
                                                        <option
                                                            value="{{ "$tkt $namaJurusan $i" }}"
                                                        >
                                                            {{ "$tkt $namaJurusan $i" }}
                                                        </option>
                                                    @endfor
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        type="button"
                                        onclick="submitBulkEdit()"
                                        class="btn btn-primary"
                                    >
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </tbody>
            </table>
            <div class="mt-4">{{ $users->links() }}</div>
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
                            $('#select-all').prop('checked', false);
                            $('#bulk-edit-wrapper').hide();

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

                    // Update UI Label
                    if (currentKelas === 'alumni') {
                        $('#selectedKelasLabel').html(
                            '<span class="text-danger fw-bold">ALUMNI</span>'
                        );
                    } else if (currentKelas === '') {
                        $('#selectedKelasLabel').text('Pilih Kelas');
                    } else {
                        $('#selectedKelasLabel').text(currentKelas);
                    }

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
            // --- FITUR HAPUS SISWA ---
            $(document).on('click', '.btn-delete-siswa', function () {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Data siswa ini akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            // SESUAIKAN: admin/siswa/{id} sesuai route:list kamu
                            url: `/admin/siswa/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (response) {
                                // Cek apakah response punya success atau error
                                if (response.success) {
                                    Swal.fire('Terhapus!', response.success, 'success');
                                    fetchSiswa(); // Refresh tabel
                                } else {
                                    Swal.fire('Gagal!', response.error, 'error');
                                }
                            },
                            error: function (xhr) {
                                // Intip di F12 -> Console untuk liat error aslinya
                                console.error('Status: ' + xhr.status);
                                console.error('Response: ' + xhr.responseText);
                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan sistem. Cek Console (F12).',
                                    'error'
                                );
                            },
                        });
                    }
                });
            });

            // --- FITUR RESET PASSWORD SISWA ---
            $(document).on('click', '.btn-reset-pw', function () {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Reset Password?',
                    text: 'Password akan diubah menjadi default: 12345678',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            // HAPUS kata '/users', sesuaikan dengan php artisan route:list kamu
                            url: `/admin/siswa/reset-password/${id}`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (response) {
                                Swal.fire('Berhasil!', response.success, 'success');
                            },
                            error: function (xhr) {
                                console.error(xhr.responseText); // Intip error aslinya di F12 Console
                                Swal.fire('Error!', 'Gagal mereset password.', 'error');
                            },
                        });
                    }
                });
            });
            // Fungsi untuk Check/Uncheck Semua
            window.toggleSelectAll = function () {
                let isChecked = $('#select-all').prop('checked');
                // Cari semua checkbox di dalam tbody yang memiliki class .siswa-checkbox
                $('.siswa-checkbox').prop('checked', isChecked);
                updateBulkEditButton();
            };

            // Fungsi untuk update tampilan tombol Edit Kelas
            window.updateBulkEditButton = function () {
                let selectedCheckboxes = $('.siswa-checkbox:checked');
                let selectedCount = selectedCheckboxes.length;

                if (selectedCount === 0) {
                    $('#bulk-edit-wrapper').fadeOut();
                    return;
                }

                let hasAlumni = false;
                let allIsKelas12 = true;

                selectedCheckboxes.each(function () {
                    let status = $(this).data('status');
                    let kelas = $(this).data('kelas').toString().toUpperCase(); // Ambil teks kelas (ex: "XII PPLG 1")

                    // Cek jika ada yang sudah alumni
                    if (status === 'alumni') {
                        hasAlumni = true;
                    }

                    // Cek jika ada yang BUKAN kelas 12
                    // Logika: Jika string kelas tidak dimulai dengan "XII", berarti bukan kelas 12
                    if (!kelas.startsWith('XII')) {
                        allIsKelas12 = false;
                    }
                });

                // Tampilkan Wrapper Utama
                $('#bulk-edit-wrapper').fadeIn();
                $('#selected-count').text(selectedCount);

                // KONDISI 1: Tombol Edit Kelas
                // Hanya muncul jika TIDAK ADA alumni yang terpilih
                if (!hasAlumni) {
                    $('#btn-bulk-edit').show();
                } else {
                    $('#btn-bulk-edit').hide();
                }

                // KONDISI 2: Tombol Set Alumni
                // Hanya muncul jika SEMUA yang dipilih adalah Kelas 12 dan BELUM alumni
                if (allIsKelas12 && !hasAlumni) {
                    $('#btn-make-alumni').show();
                } else {
                    $('#btn-make-alumni').hide();
                }

                // Jika kedua tombol akhirnya hidden karena seleksi campuran, sembunyikan wrappernya
                if (
                    $('#btn-bulk-edit').is(':hidden') &&
                    $('#btn-make-alumni').is(':hidden')
                ) {
                    $('#bulk-edit-wrapper').hide();
                }
            };

            // Event listener untuk checkbox individual (menggunakan delegasi karena data di-load via AJAX)
            $(document).on('change', '.siswa-checkbox', function () {
                updateBulkEditButton();

                // Jika satu checkbox di-uncheck, uncheck juga master "Select All"
                if (!$(this).prop('checked')) {
                    $('#select-all').prop('checked', false);
                }

                // Jika semua checkbox terpilih, check master "Select All"
                if (
                    $('.siswa-checkbox:checked').length === $('.siswa-checkbox').length &&
                    $('.siswa-checkbox').length > 0
                ) {
                    $('#select-all').prop('checked', true);
                }
            });

            window.openBulkEditModal = function () {
                let selectedCount = $('.siswa-checkbox:checked').length;
                $('#modal-selected-count').text(selectedCount);
                $('#bulkEditModal').modal('show');
            };

            window.submitBulkEdit = function () {
                let selectedIds = [];
                $('.siswa-checkbox:checked').each(function () {
                    selectedIds.push($(this).val());
                });

                let newKelas = $('#new_kelas').val();

                if (!newKelas) {
                    Swal.fire(
                        'Peringatan',
                        'Silakan pilih kelas baru terlebih dahulu!',
                        'warning'
                    );
                    return;
                }

                $.ajax({
                    url: '{{ route("users.bulkUpdateKelas") }}', // Pastikan Route ini sudah dibuat
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: selectedIds,
                        kelas: newKelas,
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#bulkEditModal').modal('hide');
                            Swal.fire('Berhasil!', response.success, 'success');
                            fetchSiswa(); // Refresh tabel
                            $('#bulk-edit-wrapper').hide();
                            $('#select-all').prop('checked', false);
                        }
                    },
                    error: function () {
                        Swal.fire(
                            'Error!',
                            'Terjadi kesalahan saat memperbarui data.',
                            'error'
                        );
                    },
                });
            };
            window.makeAlumni = function () {
                let selectedIds = [];
                $('.siswa-checkbox:checked').each(function () {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    Swal.fire(
                        'Peringatan',
                        'Pilih minimal satu siswa terlebih dahulu!',
                        'warning'
                    );
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Alumni',
                    text: `Apakah Anda yakin ingin membekukan ${selectedIds.length} akun siswa terpilih menjadi alumni?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Proses!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tampilkan loading saat proses
                        Swal.fire({
                            title: 'Sedang Memproses...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });

                        $.ajax({
                            url: '{{ route("users.bulkAlumni") }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ids: selectedIds,
                            },
                            success: function (response) {
                                Swal.close(); // Tutup loading

                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.success,
                                        icon: 'success',
                                    }).then(() => {
                                        // Refresh tabel dan sembunyikan tombol
                                        location.reload(); // Atau panggil fetchSiswa() jika pakai AJAX
                                    });
                                }
                            },
                            error: function (xhr) {
                                Swal.close();
                                let errorMsg = 'Terjadi kesalahan pada server.';
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    errorMsg = xhr.responseJSON.error;
                                }
                                Swal.fire('Gagal!', errorMsg, 'error');
                                console.error(xhr.responseText); // Cek detail error di Inspect Element -> Console
                            },
                        });
                    }
                });
            };
        </script></x-app-layout
>
