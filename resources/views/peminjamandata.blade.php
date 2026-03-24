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
                                            <th>Jatuh Tempo</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($dipinjam as $data)
                                            @php
                    $tglJatuhTempo = \Carbon\Carbon::parse($data->tgl_jatuh_tempo)->startOfDay();
                    $hariIni = \Carbon\Carbon::now()->startOfDay();
                    $isTelat = $hariIni > $tglJatuhTempo;
                    $jmlHari = $isTelat ? $hariIni->diffInDays($tglJatuhTempo) : 0;
                    $totalDenda = $jmlHari * 150000;
                    $sudahDiajukan = ($data->status === 'proses');
                @endphp
                                            <tr
                                                class="item-peminjaman {{ $isTelat ? 'status-terlambat' : 'status-aman' }}"
                                            >
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
                                                                            <p class="mb-1">Peminjam: <strong>{{ $data->user->name }}</strong></p>
                                                                            <p class="mb-1">Judul: <strong>{{ $data->buku->judul }}</strong></p>
                                                                            <hr
                                                                                class="my-2"
                                                                            />
                                                                            <p class="mb-1 text-danger">Terlambat: <strong>{{ $jmlHari }} Hari</strong></p>
                                                                            <p class="mb-0 text-danger fw-bold">Total Denda: Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
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
                const activeFilters = Array.from(checkboxes)
                    .filter((i) => i.checked)
                    .map((i) => i.value);

                if (searchText.length > 0) {
                    btnResetSearch.classList.remove('d-none');
                } else {
                    btnResetSearch.classList.add('d-none');
                }

                rows.forEach((row) => {
                    const textContent = row.innerText.toLowerCase();
                    const textMatch = textContent.includes(searchText);
                    const filterMatch =
                        activeFilters.length === 0 ||
                        activeFilters.some((f) => row.classList.contains(f));

                    row.style.display = textMatch && filterMatch ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', applyAllFilters);

            checkboxes.forEach((box) => {
                box.addEventListener('change', applyAllFilters);
            });

            btnResetSearch.addEventListener('click', function () {
                searchInput.value = '';
                applyAllFilters();
                searchInput.focus();
            });

            btnResetFilter.addEventListener('click', function () {
                checkboxes.forEach((cb) => (cb.checked = false));
                applyAllFilters();
            });
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
    </script>
</x-app-layout>
