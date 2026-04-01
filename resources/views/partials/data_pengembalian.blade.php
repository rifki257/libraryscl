<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr class="text-capitalize text-center">
                <th>ID Pinjam</th>
                <th>Peminjam</th>
                <th>Judul Buku</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Status / Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dataHistory as $data)
                @php
                    // Logika denda untuk tampilan modal
                    $jatuhTempo = \Carbon\Carbon::parse($data->tgl_jatuh_tempo)->startOfDay();
                    $tglKembali = \Carbon\Carbon::parse($data->tgl_kembali ?? $data->updated_at)->startOfDay();
                    $selisihHari = $tglKembali->gt($jatuhTempo) ? $tglKembali->diffInDays($jatuhTempo) : 0;
                    $classStatus = ($data->denda > 0) ? 'table-danger' : '';
                @endphp
                <tr
                    class="text-capitalize text-center align-middle {{ $classStatus }}"
                >
                    <td><code>#{{ $data->id_pinjam }}</code></td>
                    <td class="fw-bold">{{ $data->user->name ?? 'User' }}</td>
                    <td>{{ $data->buku->judul ?? 'Buku Dihapus' }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($data->tgl_pinjam)->format('d/m/Y') }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($data->tgl_kembali)->format('d/m/Y') }}
                    </td>
                    <td>
                        @if ($data->denda > 0)
                            <span
                                class="badge bg-danger-subtle text-danger px-3 py-2"
                            >
                                <i
                                    class="bi bi-exclamation-triangle-fill me-1"
                                ></i>
                                Denda Rp {{ number_format($data->denda, 0, ',', '.') }}
                            </span>
                            {{-- <button
                                type="button"
                                class="btn btn-outline-danger btn-sm fw-bold"
                                data-bs-toggle="modal"
                                data-bs-target="#modalDenda{{ $data->id_pinjam }}"
                            >
                                <i
                                    class="bi bi-exclamation-triangle-fill me-1"
                                ></i>
                                Denda Rp {{ number_format($data->denda, 0, ',', '.') }}
                            </button>
                            <div
                                class="modal fade"
                                id="modalDenda{{ $data->id_pinjam }}"
                                tabindex="-1"
                                aria-hidden="true"
                            >
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div
                                            class="modal-header bg-danger text-white"
                                        >
                                            <h5 class="modal-title">
                                                Rincian Denda
                                            </h5>
                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"
                                                aria-label="Close"
                                            ></button>
                                        </div>
                                        <div class="modal-body text-dark">
                                            <p class="mb-1"><strong>Nama:</strong> {{ $data->user->name ?? 'User' }}</p>
                                            <hr />
                                            <p class="mb-1 small">Tgl Pinjam: {{ \Carbon\Carbon::parse($data->tgl_pinjam)->format('d/m/Y') }}</p>
                                            <p class="mb-1 small text-danger">Jatuh Tempo: {{ \Carbon\Carbon::parse($data->tgl_jatuh_tempo)->format('d/m/Y') }}</p>
                                            <p class="mb-1 small text-success">Tgl Kembali: {{ \Carbon\Carbon::parse($data->tgl_kembali)->format('d/m/Y') }}</p>
                                            <hr />
                                            @php
                    $jt = \Carbon\Carbon::parse($data->tgl_jatuh_tempo)->startOfDay();
                    $kb = \Carbon\Carbon::parse($data->tgl_kembali)->startOfDay();
                    $selisih = $kb->gt($jt) ? $kb->diffInDays($jt) : 0;
                @endphp
                                            <p class="mb-1"><strong>Total Terlambat:</strong> {{ $selisih }} Hari</p>
                                            <p class="fs-5">
                                                <strong>Total Denda:</strong>
                                                <br />
                                                <span
                                                    class="text-danger fw-bold"
                                                    >Rp {{ number_format($data->denda, 0, ',', '.') }}</span
                                                >
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                        @else
                            <span
                                class="badge bg-success-subtle text-success px-3 py-2"
                            >
                                <i class="bi bi-check-circle-fill me-1"></i>
                                Tepat Waktu
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        Belum ada riwayat pengembalian.
                    </td>
                </tr>
            @endforelse
            {{-- PERHATIKAN: @endforelse sekarang berada DI BAWAH modal --}}
        </tbody>
    </table>
</div>
