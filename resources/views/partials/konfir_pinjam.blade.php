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
        @forelse ($semuaPeminjaman as $item)
            <tr class="text-capitalize text-center align-middle">
                <td>{{ $item->user->name }}</td>
                <td>{{ $item->buku->judul }}</td>
                <td>{{ $item->created_at->format('d M Y') }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($item->tgl_jatuh_tempo)->format('d M Y') }}
                </td>
                <td class="align-middle">
                    <div
                        class="d-flex justify-content-center align-items-center gap-1"
                    >
                        {{-- KONDISI 1: JIKA STATUSNYA PENDING / MENUNGGU (Minta Pinjam) --}}
                        @if ($item->status == 'pending' || $item->status == 'menunggu')
                            <form
                                action="{{ route('admin.setujui', $item->id_pinjam) }}"
                                method="POST"
                            >
                                @csrf
                                @method ('PUT')
                                <button
                                    type="submit"
                                    class="btn btn-success btn-sm"
                                >
                                    Setujui Pinjam
                                </button>
                            </form>
                            <form
                                action="{{ route('admin.tolak', ['id' => $item->id_pinjam]) }}"
                                method="POST"
                            >
                                @csrf
                                @method ('PATCH')
                                <button
                                    type="submit"
                                    class="btn btn-danger btn-sm"
                                    onclick="
                                        return confirm('Tolak peminjaman ini?');
                                    "
                                >
                                    Tolak
                                </button>
                            </form>
                            {{-- KONDISI 2: JIKA STATUSNYA AJUKAN_KEMBALI (Minta Balikin Buku) --}}
                        @elseif ($item->status == 'ajukan_kembali')
                            <form
                                action="{{ route('admin.peminjaman.konfirmasi', $item->id_pinjam) }}"
                                method="POST"
                            >
                                @csrf
                                @method ('PUT')
                                <button
                                    type="submit"
                                    class="btn btn-primary btn-sm"
                                >
                                    <i class="bi bi-check-all"></i> Konfirmasi
                                    Pengembalian
                                </button>
                            </form>
                            {{-- KONDISI 3: JIKA SUDAH DIPINJAM (Hanya Label) --}}
                        @elseif ($item->status == 'dipinjam')
                            <span class="badge bg-info text-dark"
                                >Sedang Dipinjam</span
                            >
                        @endif
                    </div>
                </td>
            </tr>
        @empty
        @endforelse
    </tbody>
</table>
