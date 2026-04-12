@forelse ($semuaPeminjaman as $data)
    @php
        $tglJatuhTempo = \Carbon\Carbon::parse($data->tgl_jatuh_tempo)->startOfDay();
        $tglSekarang = \Carbon\Carbon::now()->startOfDay();
        $selisih = $tglSekarang->diffInDays($tglJatuhTempo, false);
        $hariTerlambat = $selisih < 0 ? abs($selisih) : 0;
        $totalDenda = $hariTerlambat * 50000;
    @endphp
    <tr class="text-capitalize text-center align-middle">
        <td>{{ $data->id_pinjam }}</td>
        <td>
            <div
                class="fw-bold {{ $hariTerlambat > 0 && $data->status == 'dipinjam' ? 'text-danger' : '' }}"
            >
                {{ $data->user->name }}
            </div>
        </td>
        <td>{{ $data->buku->judul }}</td>
        <td>
            {{ $data->tgl_pinjam ? \Carbon\Carbon::parse($data->tgl_pinjam)->format('d M Y') : '-' }}
        </td>
        <td>
            {{ \Carbon\Carbon::parse($data->tgl_jatuh_tempo)->format('d M Y') }}
        </td>
        <td>
            @if ($data->status == 'dipinjam')
                <span class="badge bg-primary p-2">Dipinjam</span>
                @if ($hariTerlambat > 0)
                    <br
                    /><small class="text-danger fw-bold"
                        >Denda: Rp {{ number_format($totalDenda, 0, ',', '.') }}</small
                    >
                @endif
            @elseif ($data->status == 'ajukan_kembali')
                <span class="badge bg-success p-2 text-white">
                    Konfir Pengembalian
                </span>

            @elseif ($data->status == 'kembali')
                {{-- Sudah selesai dikembalikan --}}
                <span class="badge bg-secondary p-2"> Selesai </span>

            @else
                {{-- Status lainnya --}}
                <span class="badge bg-warning p-2"> {{ $data->status }} </span>
            @endif
        </td>
        <td>
            @if ($hariTerlambat > 0)
                <button
                    type="button"
                    class="btn btn-danger btn-sm fw-bold"
                    data-bs-toggle="modal"
                    data-bs-target="#modalKembali"
                    data-id="{{ $data->id_pinjam }}"
                    data-nama="{{ $data->user->name }}"
                    data-email="{{ $data->user->email }}"
                    data-hp="{{ $data->user->no_hp }}"
                    data-kelas="{{ $data->user->kelas }}"
                    data-nis="{{ $data->user->nis }}"
                    data-buku="{{ $data->buku->judul }}"
                    data-tgl-tempo="{{ $data->tgl_jatuh_tempo }}"
                    data-total-denda="{{ number_format($totalDenda, 0, ',', '.') }}"
                    data-hari-telat="{{ $hariTerlambat }}"
                >
                    Check Denda
                </button>
            @else
                <span class="btn btn-primary btn-sm"> Tidak ada denda </span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4 text-muted">
            Data tidak ditemukan.
        </td>
    </tr>
@endforelse
