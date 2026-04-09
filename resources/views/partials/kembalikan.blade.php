    <div class="table-responsive">
    <table class="table table-striped text-center align-middle">
        <thead>
            <tr>
                <th>Id Buku</th>
                <th>Judul Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody id="body-riwayat">
            @forelse ($riwayatSelesai as $history)
                <tr
                    class="item-peminjaman {{ ($history->denda > 0) ? 'status-denda' : 'status-tepat' }}"
                >
                    <td class="align-middle">{{ $history->buku->id_buku }}</td>
                    <td class="align-middle">{{ $history->buku->judul }}</td>
                    <td class="align-middle">
                        {{ \Carbon\Carbon::parse($history->tgl_pinjam)->format('d M Y') }}
                    </td>
                    <td class="align-middle">
                        {{ \Carbon\Carbon::parse($history->tgl_kembali)->format('d M Y') }}
                    </td>
                    <td class="align-middle">
                        @if ($history->denda > 0)
                            <span class="badge bg-warning text-dark">
                                Denda: Rp {{ number_format($history->denda, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="badge bg-success">Tepat Waktu</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        Belum ada riwayat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
