<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Manajemen Peminjaman Aktif') }}
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
                                    type="checkbox"
                                    value="status-terlambat"
                                    id="fTelat"
                                />
                                <label
                                    class="form-check-label text-danger fw-bold"
                                    for="fTelat"
                                    >Terlambat</label
                                >
                            </div>
                        </li>
                        <li>
                            <div class="form-check mb-2">
                                <input
                                    class="form-check-input filter-checkbox"
                                    type="checkbox"
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
            <div class="overflow-hidden shadow-sm sm:rounded-lg" style="background-color: rgb(235, 235, 235)">
                <div class="p-6 text-gray-900">
                    <div class="container">
                        <div class="card-body">
                            <div id="tabel-buku" class="mt-4">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th
                                                id="th-checkbox"
                                                class="checkbox-column text-center d-none"
                                            >
                                                <input
                                                    type="checkbox"
                                                    id="check-all"
                                                    class="form-check-input"
                                                />
                                            </th>
                                            <th>ID Pinjam</th>
                                            <th>Peminjam</th>
                                            <th>Judul Buku</th>
                                            <th>Tgl Pinjam</th>
                                            <th>Jatuh Tempo</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($dipinjam as $data)
                                            @php
                                            $statusAktif = request('status');
                                            $isFilteringTerlambat = ($statusAktif == 'fTelat');
        $tglJatuhTempo = \Carbon\Carbon::parse($data->tgl_jatuh_tempo)->startOfDay();
        $hariIni = \Carbon\Carbon::now()->startOfDay();
        $isTelat = $hariIni > $tglJatuhTempo;
        $jmlHari = $isTelat ? $hariIni->diffInDays($tglJatuhTempo) : 0;
        $totalDenda = $jmlHari * 150000;
        $sudahDiajukan = ($data->status === 'proses');
        $isFilteringTerlambat = request('status') == 'terlambat' || request('filter') == 'denda';
    @endphp
                                            <tr
                                                class="item-peminjaman {{ $isTelat ? 'status-terlambat' : 'status-aman' }}"
                                            >
                                                <td
                                                    class="checkbox-column text-center d-none"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input select-peminjaman"
                                                        value="{{ $data->id }}"
                                                    />
                                                </td>
                                                <td>
                                                    <code
                                                        >#{{ $data->id_pinjam }}</code
                                                    >
                                                </td>

                                                <td
                                                    class="fw-bold {{ $isTelat ? 'text-danger' : '' }}"
                                                >
                                                    {{ $data->user->name }}
                                                </td>

                                                <td>
                                                    {{ $data->buku->judul ?? 'Buku Tidak Ditemukan' }}
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($data->tgl_pinjam)->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    {{ $tglJatuhTempo->format('d/m/Y') }}
                                                </td>

                                                <td class="text-center">
                                                    @if ($isTelat)
                                                        <span
                                                            class="badge rounded-pill bg-danger"
                                                            >Terlambat</span
                                                        >
                                                    @elseif ($sudahDiajukan)
                                                        <span
                                                            class="badge rounded-pill bg-warning text-dark"
                                                            >Menunggu
                                                            Konfirmasi</span
                                                        >
                                                    @else
                                                        <span
                                                            class="badge rounded-pill bg-success"
                                                            >Sedang
                                                            Dipinjam</span
                                                        >
                                                    @endif
                                                </td>

                                                <td
                                                    class="text-center align-middle"
                                                >
                                                    @if ($isTelat)
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalTelat{{ $data->id_pinjam }}"
                                                        >
                                                            Konfirmasi
                                                        </button>
                                                        <div
                                                            class="modal fade"
                                                            id="modalTelat{{ $data->id_pinjam }}"
                                                            tabindex="-1"
                                                            aria-hidden="true"
                                                        >
                                                            <div
                                                                class="modal-dialog modal-sm modal-dialog-centered"
                                                            >
                                                                <div
                                                                    class="modal-content border-danger shadow"
                                                                >
                                                                    <div
                                                                        class="modal-header bg-danger text-white py-2"
                                                                    >
                                                                        <h6
                                                                            class="modal-title"
                                                                        >
                                                                            Rincian
                                                                            Denda
                                                                        </h6>
                                                                        <button
                                                                            type="button"
                                                                            class="btn-close btn-close-white"
                                                                            data-bs-dismiss="modal"
                                                                        ></button>
                                                                    </div>
                                                                    <form
                                                                        action="{{ route('admin.konfirmasi_kembali', $data->id_pinjam) }}"
                                                                        method="POST"
                                                                    >
                                                                        @csrf
                                                                        @method ('PUT')
                                                                        <div
                                                                            class="modal-body text-start"
                                                                            style="
                                                                                font-size: 0.85rem;
                                                                            "
                                                                        >
                                                                            <p class="mb-1">Peminjam: <strong class="text-capitalize">{{ $data->user->name }}</strong></p>
                                                                            <p class="mb-1">Judul: <strong class="text-capitalize">{{ $data->buku->judul }}</strong></p>
                                                                            <p class="mb-1">No HP: <strong>{{ $data->user->no_hp ?? '-' }}</strong></p>

                                                                            <hr
                                                                                class="my-2"
                                                                            />

                                                                            <p class="mb-1 text-danger">Terlambat: <strong>{{ abs($jmlHari) }} Hari</strong></p>
                                                                            <p class="mb-2 text-danger fw-bold">Total Denda: Rp {{ number_format(abs($totalDenda), 0, ',', '.') }}</p>

                                                                            @if ($data->user->no_hp)
                                                                                <button
                                                                                    type="button"
                                                                                    class="btn btn-sm btn-success w-100 mb-2 d-flex align-items-center justify-content-center gap-2"
                                                                                    onclick="kirimNotifWA('{{ $data->user->no_hp }}', '{{ $data->user->name }}', '{{ $data->buku->judul }}', '{{ abs($jmlHari) }}', '{{ number_format(abs($totalDenda), 0, ',', '.') }}')"
                                                                                >
                                                                                    <i
                                                                                        class="bi bi-whatsapp"
                                                                                    ></i>
                                                                                    Kirim
                                                                                    Tagihan
                                                                                    via
                                                                                    WA
                                                                                </button>
                                                                            @else
                                                                                <div
                                                                                    class="alert alert-warning py-1 px-2 mb-2"
                                                                                    style="
                                                                                        font-size: 0.75rem;
                                                                                    "
                                                                                >
                                                                                    No.
                                                                                    HP
                                                                                    tidak
                                                                                    tersedia
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div
                                                                            class="modal-footer py-1"
                                                                        >
                                                                            <button
                                                                                type="button"
                                                                                class="btn btn-xs btn-secondary"
                                                                                data-bs-dismiss="modal"
                                                                            >
                                                                                Batal
                                                                            </button>
                                                                            <button
                                                                                type="submit"
                                                                                class="btn btn-danger"
                                                                            >
                                                                                Konfirmasi
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @elseif ($sudahDiajukan)
                                                        <form
                                                            action="{{ route('admin.konfirmasi_kembali', $data->id_pinjam) }}"
                                                            method="POST"
                                                            style="
                                                                display: contents;
                                                            "
                                                        >
                                                            @csrf
                                                            @method ('PUT')
                                                            <button
                                                                type="submit"
                                                                class="btn btn-sm btn-primary"
                                                                onclick="
                                                                    return confirm(
                                                                        'Apakah Anda yakin ingin mengonfirmasi pengembalian buku ini?'
                                                                    );
                                                                "
                                                            >
                                                                Konfirmasi
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button
                                                            class="btn btn-sm btn-secondary opacity-50"
                                                            disabled
                                                        >
                                                            Belum Diajukan
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td
                                                    colspan="7"
                                                    class="text-center py-4"
                                                >
                                                    Tidak ada data peminjaman
                                                    aktif.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div
                                    id="bulk-action-bar"
                                    class="fixed-bottom bg-white border-top p-3 shadow-lg d-none"
                                    style="z-index: 1030"
                                >
                                    <div
                                        class="container d-flex justify-content-between align-items-center"
                                    >
                                        <div>
                                            <span
                                                id="selected-count"
                                                class="fw-bold text-primary"
                                                >0</span
                                            >
                                            Data Terpilih
                                        </div>
                                        <button
                                            type="button"
                                            class="btn btn-success d-flex align-items-center gap-2"
                                            onclick="openBulkModal()"
                                        >
                                            <i class="bi bi-whatsapp"></i> Kirim
                                            Notif Massal (<span id="btn-count"
                                                >0</span
                                            >)
                                        </button>
                                    </div>
                                </div>

                                <div
                                    class="modal fade"
                                    id="modalBulkWA"
                                    tabindex="-1"
                                    aria-hidden="true"
                                >
                                    <div
                                        class="modal-dialog modal-md modal-dialog-centered"
                                    >
                                        <div class="modal-content">
                                            <div
                                                class="modal-header bg-success text-white"
                                            >
                                                <h6 class="modal-title">
                                                    Daftar Antrean Kirim WA
                                                </h6>
                                                <button
                                                    type="button"
                                                    class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"
                                                ></button>
                                            </div>
                                            <div class="modal-body">
                                                <div
                                                    id="bulk-list"
                                                    class="list-group list-group-flush mb-3"
                                                    style="
                                                        max-height: 300px;
                                                        overflow-y: auto;
                                                    "
                                                ></div>
                                                <div
                                                    class="alert alert-info py-2 small"
                                                >
                                                    <i
                                                        class="bi bi-info-circle"
                                                    ></i>
                                                    WhatsApp akan terbuka satu
                                                    per satu setelah Anda
                                                    menekan tombol kirim di
                                                    masing-masing nama.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
            const checkboxes = document.querySelectorAll('.filter-checkbox');
            const rows = document.querySelectorAll('.item-peminjaman');

            function applyAllFilters() {
                const searchText = searchInput.value.toLowerCase();
                const activeFilter = Array.from(checkboxes).find(
                    (i) => i.checked
                )?.value;

                if (searchText.length > 0) {
                    btnResetSearch?.classList.remove('d-none');
                } else {
                    btnResetSearch?.classList.add('d-none');
                }

                rows.forEach((row) => {
                    const textContent = row.innerText.toLowerCase();
                    const textMatch = textContent.includes(searchText);

                    const filterMatch =
                        !activeFilter || row.classList.contains(activeFilter);

                    row.style.display = textMatch && filterMatch ? '' : 'none';
                });
            }

            checkboxes.forEach((box) => {
                box.addEventListener('change', function () {
                    if (this.checked) {
                        checkboxes.forEach((otherBox) => {
                            if (otherBox !== this) otherBox.checked = false;
                        });
                    }
                    applyAllFilters();
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', applyAllFilters);
            }

            if (btnResetSearch) {
                btnResetSearch.addEventListener('click', function () {
                    searchInput.value = '';
                    applyAllFilters();
                    searchInput.focus();
                });
            }

            if (btnResetFilter) {
                btnResetFilter.addEventListener('click', function () {
                    checkboxes.forEach((cb) => (cb.checked = false));
                    applyAllFilters();
                });
            }

            @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000,
                iconColor: '#0d6efd',
            });
            @endif

            @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
            });
            @endif
        });

        function kirimNotifWA(noHp, nama, judulBuku, hari, denda) {
            let formattedHp = noHp.toString().replace(/^0/, '62');
            const pesan = `Halo Kak ${nama},\n\nKami dari Perpustakaan ingin menginformasikan bahwa peminjaman buku:\nJudul: ${judulBuku}\n\nTelah melewati batas waktu selama ${hari} hari.\nTotal denda saat ini sebesar: Rp ${denda}.\n\nMohon segera melakukan pengembalian buku dan penyelesaian denda. Terima kasih. 🙏`;
            const url = `https://wa.me/${formattedHp}?text=${encodeURIComponent(pesan)}`;
            window.open(url, '_blank');
        }
        $('.form-check-input').on('change', function () {
            if ($('#filter-terlambat').is(':checked')) {
                $('.checkbox-column').removeClass('d-none');
            } else {
                $('.checkbox-column').addClass('d-none');
            }
        });
    </script>
</x-app-layout>
