<table class="table table-striped">
    <thead>
        <tr class="text-capitalize text-center">
            <th>id buku</th>
            <th>gambar</th>
            <th>judul</th>
            <th>stok</th>
            <th>aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataBuku as $buku)
            <tr class="text-capitalize text-center align-middle">
                <td>{{ $buku->id_buku }}</td>

                <td>
                    @if ($buku->gambar)
                        <img
                            src="{{ asset('storage/' . $buku->gambar) }}"
                            alt="cover"
                            style="width: 50px; height: auto"
                            class="rounded shadow-sm mx-auto"
                        />
                    @else
                        <span class="text-muted">No Image</span>
                    @endif
                </td>

                {{-- Kolom Judul --}}
                <td class="{{ $buku->jumlah <= 0 ? 'text-danger fw-bold' : '' }}">
                    {{ $buku->judul }}
                </td>

                {{-- Kolom Jumlah/Stok --}}
                <td>
                    @if ($buku->jumlah <= 0)
                        <span class="badge bg-danger">Stok Habis</span>
                    @else
                        {{ $buku->jumlah }}
                    @endif
                </td>

                <td class="text-center">
                    <div class="d-flex justify-content-center align-items-center gap-1">
                        <a
                            href="{{ route('buku.edit', $buku->id_buku) }}"
                            class="btn btn-warning text-white"
                            style="width: 70px"
                            >Edit</a
                        >

                        <a
                            href="{{ route('buku.detail', $buku->id_buku) }}"
                            class="btn btn-secondary"
                            style="width: 70px"
                            >Detail</a
                        >

                        <form
                            action="{{ route('buku.destroy', $buku->id_buku) }}"
                            method="POST"
                            class="m-0"
                        >
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="btn btn-danger"
                                style="width: 70px"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?');"
                            >
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>