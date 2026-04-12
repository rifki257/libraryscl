<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Data Peminjam') }}
            </h2>

            <div class="position-relative shadow-sm" style="width: 300px">
                <span
                    class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"
                    style="z-index: 5"
                >
                    <i class="bi bi-search"></i>
                </span>

                <input
                    type="text"
                    id="liveSearch"
                    class="form-control ps-5 pe-5"
                    placeholder="Cari peminjam atau buku..."
                    autocomplete="off"
                    style="
                        border-radius: 8px;
                        border: 1px solid #ddd;
                    "
                />

                <button
                    id="clearSearch"
                    type="button"
                    class="btn position-absolute top-50 end-0 translate-middle-y me-2 text-muted"
                    style="
                        display: none;
                        border: none;
                        background: transparent;
                        z-index: 5;
                    "
                >
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </div>
        </div>
    </x-slot>
    <div class="py-3">
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
                <tbody id="peminjamanTableBody">
                    @include ('admin.table_peminjaman_rows')
                </tbody>
            </table>
            <div>{{ $semuaPeminjaman->links() }}</div>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        $(document).ready(function () {
            // Fungsi utama pencarian AJAX
            function doSearch(query) {
                $.ajax({
                    url: '{{ route('persetujuan.data') }}', // Sesuaikan nama route Anda
                    type: 'GET',
                    data: { search: query },
                    beforeSend: function () {
                        $('#peminjamanTableBody').css('opacity', '0.5');
                    },
                    success: function (data) {
                        $('#peminjamanTableBody').html(data);
                        $('#peminjamanTableBody').css('opacity', '1');
                    },
                    error: function () {
                        $('#peminjamanTableBody').css('opacity', '1');
                    },
                });
            }

            // 1. Logika saat mengetik (Live Search)
            $('#liveSearch').on('keyup', function () {
                let val = $(this).val();

                // Tampilkan/Sembunyikan tombol X
                if (val.length > 0) {
                    $('#clearSearch').fadeIn(200); // Muncul perlahan
                } else {
                    $('#clearSearch').fadeOut(200); // Hilang perlahan
                }

                doSearch(val);
            });

            // 2. Logika saat tombol Reset (X) diklik
            $('#clearSearch').on('click', function () {
                $('#liveSearch').val(''); // Kosongkan input kotak search
                $(this).hide(); // Sembunyikan dirinya sendiri (tombol X)
                doSearch(''); // Panggil AJAX dengan string kosong untuk reset data
                $('#liveSearch').focus(); // Kembalikan kursor ke kotak search
            });
        });
    </script>
</x-app-layout>
