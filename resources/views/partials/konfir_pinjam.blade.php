@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-striped">
    <thead>
        <tr class="text-capitalize text-center">
            <th>Nama Peminjam</th>
            <th>Judul Buku</th>
            <th>Tgl Pengajuan</th>
            <th>Rencana Kembali</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($peminjamanPending as $item)
            <tr class="text-capitalize text-center align-middle">
                <td>{{ $item->user->name }}</td>
                <td>{{ $item->buku->judul }}</td>
                <td>{{ $item->created_at->format('d M Y') }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($item->tgl_jatuh_tempo)->format('d M Y') }}
                </td>
                <td>
                    <form
                        action="{{ route('admin.setujui', $data->id_peminjaman) }}"
                        method="POST"
                    >
                        @csrf
                        @method ('PUT')
                        <button type="submit" class="btn btn-primary btn-sm">
                            Setujui
                        </button>
                    </form>

                    <form
                        action="{{ route('admin.tolak', ['id' => $item->id_pinjam]) }}"
                        method="POST"
                        style="display: inline"
                    >
                        @csrf
                        @method ('PATCH')
                        <button
                            type="submit"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Tolak peminjaman ini?');"
                        >
                            Tolak
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">
                    Tidak ada permintaan peminjaman baru.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
