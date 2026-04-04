<table class="table table-striped">
    <thead>
        <tr class="text-capitalize text-center">
            <th id="th-checkbox" class="checkbox-column text-center d-none">
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
        $totalDenda = $jmlHari * 50000;
        $sudahDiajukan = ($data->status === 'proses');
        $isFilteringTerlambat = request('status') == 'terlambat' || request('filter') == 'denda';
    @endphp
            <tr
                class="text-capitalize text-center align-middle item-peminjaman {{ $isTelat ? 'status-terlambat' : 'status-aman' }}"
            >
                <td class="checkbox-column text-center d-none">
                    <input
                        type="checkbox"
                        class="form-check-input select-peminjaman"
                        value="{{ $data->id }}"
                    />
                </td>
                <td>
                    <code>#{{ $data->id_pinjam }}</code>
                </td>

                <td class="fw-bold {{ $isTelat ? 'text-danger' : '' }}">
                    {{ $data->user->name }}
                </td>

                <td>{{ $data->buku->judul ?? 'Buku Tidak Ditemukan' }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($data->tgl_pinjam)->format('d/m/Y') }}
                </td>
                <td>{{ $tglJatuhTempo->format('d/m/Y') }}</td>

                <td class="text-center">
                    @if ($isTelat)
                        <span class="badge rounded-pill bg-danger"
                            >Terlambat</span
                        >
                    @elseif ($sudahDiajukan)
                        <span class="badge rounded-pill bg-warning text-dark"
                            >Menunggu Konfirmasi</span
                        >
                    @else
                        <span class="badge rounded-pill bg-success"
                            >Sedang Dipinjam</span
                        >
                    @endif
                </td>

                <td class="text-center align-middle">
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
                                <div class="modal-content border-danger shadow">
                                    <div
                                        class="modal-header bg-danger text-white py-2"
                                    >
                                        <h6 class="modal-title">
                                            Rincian Denda
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
                                            style="font-size: 0.85rem"
                                        >
                                            <p class="mb-1">Peminjam: <strong class="text-capitalize">{{ $data->user->name }}</strong></p>
                                            <p class="mb-1">Judul: <strong class="text-capitalize">{{ $data->buku->judul }}</strong></p>
                                            <p class="mb-1">No HP: <strong>{{ $data->user->no_hp ?? '-' }}</strong></p>

                                            <hr class="my-2" />

                                            <p class="mb-1 text-danger">Terlambat: <strong>{{ abs($jmlHari) }} Hari</strong></p>
                                            <p class="mb-2 text-danger fw-bold">Total Denda: Rp {{ number_format(abs($totalDenda), 0, ',', '.') }}</p>
                                        </div>
                                        <div class="modal-footer py-1">
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
                        >
                            @csrf
                            @method ('PUT')
                            <button
                                type="submit"
                                class="btn btn-primary btn-sm"
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
                <td colspan="7" class="text-center py-4">
                    Tidak ada data peminjaman aktif.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
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
</script>
