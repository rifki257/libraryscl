<div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="overflow-hidden shadow-sm sm:rounded-lg"
                style="background-color: rgb(235, 235, 235)"
            >
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
                                            <th>Tgl Kembali</th>
                                            <th>Denda</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($dikembalikan as $data)
                                            @php
        $classStatus = ($data->denda > 0) ? 'status-terlambat' : 'status-aman';
    @endphp
                                            <tr
                                                class="item-peminjaman {{ $classStatus }}"
                                            >
                                                <td>
                                                    <code
                                                        >{{ $data->id_pinjam }}</code
                                                    >
                                                </td>
                                                <td>{{ $data->user->name }}</td>
                                                <td>
                                                    {{ $data->buku->judul }}
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($data->tgl_pinjam)->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($data->updated_at)->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    @if ($data->denda > 0)
                                                        <span
                                                            class="text-danger fw-bold"
                                                            >Rp {{ number_format($data->denda, 0, ',', '.') }}</span
                                                        >
                                                    @else
                                                        <span
                                                            class="text-success"
                                                            >Tidak Ada</span
                                                        >
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-4">
                                                    Belum ada riwayat
                                                    pengembalian.
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