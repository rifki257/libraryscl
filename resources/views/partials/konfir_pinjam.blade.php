@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
<table class="table table-bordered">
    <thead class="table-dark">
        <tr class="text-capitalize text-center">
            <th>Peminjam</th>
            <th>Buku</th>
            <th>Tgl Pinjam</th>
            <th>Tgl jatuh tempo</th>
            <th>Persetujuan</th>
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
                        <form
                            action="{{ route('admin.setujui', $item->id_pinjam) }}"
                            method="POST"
                        >
                            @csrf
                            @method ('PUT')
                            <button type="submit" class="btn btn-success">
                                Konfirmasi
                            </button>
                        </form>

                        <form
                            id="form-tolak-{{ $item->id_pinjam }}"
                            action="{{ route('admin.tolak', $item->id_pinjam) }}"
                            method="POST"
                        >
                            @csrf
                            @method ('PATCH')
                            <input
                                type="hidden"
                                name="alasan"
                                id="alasan-{{ $item->id_pinjam }}"
                            />

                            <button
                                type="button"
                                class="btn btn-danger"
                                onclick="tolakPeminjaman('{{ $item->id_pinjam }}', '{{ $item->buku->judul }}')"
                            >
                                Tolak
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">
                    Tidak ada permintaan baru.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
<div>{{ $semuaPeminjaman->links() }}</div>
<script>
    function tolakPeminjaman(id, judulBuku) {
        Swal.fire({
            title: 'Tolak Peminjaman?',
            text: `Memberikan alasan penolakan untuk buku: ${judulBuku}`,
            icon: 'warning',
            input: 'textarea',
            inputPlaceholder:
                'Contoh: Maaf, stok buku saat ini sedang habis...',
            inputValue: 'Maaf, stok habis.', // Alasan default
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Kirim & Tolak',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value) {
                    return 'Alasan harus diisi agar user tidak bingung!';
                }
            },
        }).then((result) => {
            if (result.isConfirmed) {
                // Isi input hidden dengan alasan dari SweetAlert
                document.getElementById(`alasan-${id}`).value = result.value;

                // Tampilkan loading
                Swal.fire({
                    title: 'Mengirim Penolakan...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                // Submit form
                document.getElementById(`form-tolak-${id}`).submit();
            }
        });
    }
</script>
