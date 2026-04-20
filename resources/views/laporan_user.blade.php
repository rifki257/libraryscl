<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2
                class="font-semibold text-xl text-gray-800 leading-tight mb-0 text-capitalize"
            >
                Laporan Peminjaman
            </h2>
            <a href="{{ route('mypinjaman') }}" class="btn btn-secondary btn-sm"
                >Kembali</a
            >
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <form
                        action="{{ route('laporan_user') }}"
                        method="GET"
                        class="row g-3"
                    >
                        <div class="col-md-4">
                            <label class="form-label small fw-bold"
                                >Filter Status & Denda</label
                            >
                            <select
                                name="status"
                                class="form-select"
                                onchange="this.form.submit()"
                            >
                                <option value="">-- Semua Riwayat --</option>
                                <option
                                    value="menunggu"
                                    {{ request('status') == 'menunggu' ? 'selected' : '' }}
                                    >Menunggu Konfirmasi
                                </option>
                                <option
                                    value="dipinjam"
                                    {{ request('status') == 'dipinjam' ? 'selected' : '' }}
                                    >Sedang Dipinjam
                                </option>
                                <option
                                    value="kembali"
                                    {{ request('status') == 'kembali' ? 'selected' : '' }}
                                    >kembali
                                </option>
                                <option
                                    value="ditolak"
                                    {{ request('status') == 'ditolak' ? 'selected' : '' }}
                                    >Ditolak
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <a
                                href="{{ route('laporan_user') }}"
                                class="btn btn-outline-danger w-100"
                                >Reset</a
                            >
                        </div>
                    </form>
                </div>
            </div>

            <table class="table table-bordered bg-white shadow-sm">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali/Batas</th>
                        <th>Status</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $item)
                        @php
                            $denda = 0;
                            $tarifDenda = 50000;
                            $tglJatuhTempo = \Carbon\Carbon::parse($item->tgl_jatuh_tempo)->startOfDay();
                            $tglAkhir = $item->tgl_kembali 
                                        ? \Carbon\Carbon::parse($item->tgl_kembali)->startOfDay() 
                                        : \Carbon\Carbon::now()->startOfDay();

                            $selisihHari = $tglAkhir->diffInDays($tglJatuhTempo, false);

                            if ($selisihHari < 0) {
                                $denda = abs($selisihHari) * $tarifDenda;
                            }
                        @endphp
                        <tr class="text-center">
                            <td><strong>{{ $item->buku->judul }}</strong></td>
                            <td>
                                {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d/m/Y') }}
                            </td>
                            <td>{{ $tglJatuhTempo->format('d/m/Y') }}</td>
                            <td>
                                @if ($item->status == 'kembali')
                                    <span class="badge bg-success"
                                        >kembali</span
                                    >
                                @elseif ($item->status == 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span
                                        class="badge bg-primary"
                                        >{{ $item->status }}</span
                                    >
                                @endif
                            </td>
                            <td
                                class="{{ $denda > 0 ? 'text-danger fw-bold' : 'text-muted' }}"
                            >
                                {{ $denda > 0 ? 'Rp ' . number_format($denda, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                Tidak ada data yang ditemukan untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-2">{{ $riwayat->links() }}</div>
        </div>
    </div>
</x-app-layout>
