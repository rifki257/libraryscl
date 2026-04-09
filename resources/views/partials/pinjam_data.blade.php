<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr class="text-capitalize text-center">
                        <th>Id peminjaman</th>
                        <th>Peminjam</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
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
                                    <span class="badge bg-primary p-2"
                                        >Dipinjam</span
                                    >
                                    @if ($hariTerlambat > 0)
                                        <br
                                        /><small class="text-danger fw-bold"
                                            >Denda: Rp {{ number_format($totalDenda, 0, ',', '.') }}</small
                                        >
                                    @endif
                                @elseif ($data->status == 'ajukan_kembali')
                                    <span
                                        class="badge bg-success p-2 text-white"
                                    >
                                        Konfir Pengembalian
                                    </span>

                                @elseif ($data->status == 'kembali')
                                    {{-- Sudah selesai dikembalikan --}}
                                    <span class="badge bg-secondary p-2">
                                        Selesai
                                    </span>

                                @else
                                    {{-- Status lainnya --}}
                                    <span class="badge bg-warning p-2">
                                        {{ $data->status }}
                                    </span>
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
                                    <span class="btn btn-primary btn-sm">
                                        Tidak ada denda
                                    </span>
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
                </tbody>
            </table>
            <div
                class="modal fade"
                id="modalKembali"
                tabindex="-1"
                aria-hidden="true"
            >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title">Detail Peminjam</h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>Nama</td>
                                    <td class="fw-bold">
                                        : <span id="m-nama"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>: <span id="m-email"></span></td>
                                </tr>
                                <tr>
                                    <td>No. HP</td>
                                    <td>: <span id="m-hp"></span></td>
                                </tr>
                                <tr>
                                    <td>Kelas / NIS</td>
                                    <td>
                                        : <span id="m-kelas"></span> /
                                        <span id="m-nis"></span>
                                    </td>
                                </tr>
                                <hr />
                                <tr>
                                    <td>Keterlambatan</td>
                                    <td class="text-danger fw-bold">
                                        :
                                        <span id="m-telat"></span> Hari
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total Denda</td>
                                    <td class="text-danger fw-bold">
                                        : Rp <span id="m-denda"></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                            >
                                Tutup
                            </button>

                            <a
                                href="#"
                                id="btn-wa"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn btn-success d-flex align-items-center gap-2"
                            >
                                <i class="bi bi-whatsapp"></i> Kirim WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalKembali = document.getElementById('modalKembali');

            modalKembali.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;

                // 1. Ambil Data
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                const email = button.getAttribute('data-email');
                const hp = button.getAttribute('data-hp') || '';
                const kelas = button.getAttribute('data-kelas');
                const nis = button.getAttribute('data-nis');
                const judulBuku = button.getAttribute('data-buku');
                const tglTempoStr = button.getAttribute('data-tgl-tempo');

                const tglTempo = new Date(tglTempoStr);
                const tglSekarang = new Date();

                tglSekarang.setHours(0, 0, 0, 0);
                tglTempo.setHours(0, 0, 0, 0);

                let selisih = Math.floor(
                    (tglSekarang - tglTempo) / (1000 * 60 * 60 * 24)
                );
                let hariTelat = selisih > 0 ? selisih : 0;
                let totalDenda = hariTelat * 50000;
                let formatDenda = totalDenda.toLocaleString('id-ID');

                if (document.getElementById('m-nama'))
                    document.getElementById('m-nama').innerText = nama;
                if (document.getElementById('m-email'))
                    document.getElementById('m-email').innerText = email;
                if (document.getElementById('m-hp'))
                    document.getElementById('m-hp').innerText = hp;
                if (document.getElementById('m-kelas'))
                    document.getElementById('m-kelas').innerText = kelas;
                if (document.getElementById('m-nis'))
                    document.getElementById('m-nis').innerText = nis;
                if (document.getElementById('m-telat'))
                    document.getElementById('m-telat').innerText = hariTelat;
                if (document.getElementById('m-denda'))
                    document.getElementById('m-denda').innerText = formatDenda;

                const formKembali = document.getElementById('form-kembali');
                if (formKembali) {
                    formKembali.action = `/admin/peminjaman/${id}/kembali`;
                }

                let pesanWA = '';
                if (hariTelat > 0) {
                    pesanWA =
                        `PEMBERITAHUAN PENGEMBALIAN BUKU\n\n` +
                        `Kepada ${nama},\n\n` +
                        `- Nis: ${nis}\n` +
                        `- Email: ${email}\n` +
                        `- Kelas: ${kelas}\n` +
                        `Kami menginformasikan bahwa status peminjaman buku anda telah melewati tanggal jatuh tempo yang anda tentukan:\n` +
                        `- Buku: ${judulBuku}\n` +
                        `- Keterlambatan: ${hariTelat} Hari\n` +
                        `- Total Denda: Rp ${formatDenda}\n\n` +
                        `Mohon segera ke Perpustakaan untuk menyelesaikan pengembalian buku dan pembayaran denda.\n\n` +
                        `Terima kasih.`;
                }

                let cleanHP = hp.replace(/\D/g, '');
                if (cleanHP.startsWith('0')) {
                    cleanHP = '62' + cleanHP.substring(1);
                }

                const btnWA = document.getElementById('btn-wa');
                if (btnWA) {
                    if (hariTelat > 0) {
                        // Jika ada denda, kirim dengan teks otomatis
                        btnWA.href = `https://api.whatsapp.com/send?phone=${cleanHP}&text=${encodeURIComponent(pesanWA)}`;
                    } else {
                        btnWA.href = `https://api.whatsapp.com/send?phone=${cleanHP}`;
                    }
                }
            });
        });
    </script>
</x-app-layout>
