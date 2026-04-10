<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-[#ebebeb] overflow-hidden shadow-sm sm:rounded-lg p-4 p-md-5"
            >
            <a href="">Lihat laporan</a>
                <div class="row g-4">
                    @forelse ($sedangDipinjam as $item)
                        @php
        $tglJatuhTempo = \Carbon\Carbon::parse($item->tgl_jatuh_tempo)->startOfDay();
        $hariIni = \Carbon\Carbon::now()->startOfDay();
        
        // Hitung keterlambatan
        $isTerlambat = $hariIni->gt($tglJatuhTempo);
        $jumlahHariTerlambat = $isTerlambat ? $hariIni->diffInDays($tglJatuhTempo) : 0;
        $totalDenda = $jumlahHariTerlambat * 50000; // Misal denda 1000 per hari

        // Status-status
        $isPending = ($item->status == 'menunggu' || $item->status == 'pending'); 
        $isWaitingAdmin = $isPending;
        $isWaitingReturn = ($item->status == 'ajukan_kembali' || $item->status == 'proses'); 

        $gambarBuku = $item->buku->gambar 
            ? asset('storage/' . $item->buku->gambar) 
            : asset('images/default-book.png');
            
        $isProcessing = ($isPending || $isWaitingReturn);
    @endphp
                        <div class="col-12 col-md-6 col-lg-4">
                            <div
                                class="card shadow border-0 h-100 text-white position-relative overflow-hidden"
                                style="border-radius: 15px; min-height: 380px; {{ $isPending ? 'filter: grayscale(1); opacity: 0.8;' : '' }}"
                            >
                                <div
                                    class="position-absolute inset-0 w-100 h-100"
                                    style="background-image: linear-gradient(to top, rgba(0,0,0,0.9) 20%, rgba(0,0,0,0.4) 60%), url('{{ $gambarBuku }}'); 
                background-size: cover; background-position: center; z-index: 1;"
                                ></div>
                                @php
    $isProcessing = ($isPending || $item->status == 'diajukan_kembali');
@endphp
                                <div
                                    class="card-body d-flex flex-column justify-content-end p-4 position-relative"
                                    style="z-index: 2"
                                >
                                    <div class="mb-3">
                                        @if ($isWaitingAdmin)
                                            <span
                                                class="badge bg-secondary px-3 py-2 shadow-sm animate-bounce"
                                                style="cursor: pointer"
                                            >
                                                <i
                                                    class="bi bi-hourglass-split me-1"
                                                ></i>
                                                Menunggu Konfirmasi Pinjam
                                            </span>
                                        @elseif ($isWaitingReturn)
                                            <span
                                                class="badge bg-info px-3 py-2 shadow-sm animate-bounce"
                                                style="cursor: pointer"
                                            >
                                                <i
                                                    class="bi bi-arrow-repeat me-1"
                                                ></i>
                                                Menunggu Konfirmasi Kembali
                                            </span>
                                        @elseif ($isTerlambat)
                                            <span
                                                class="badge bg-danger px-3 py-2 shadow-sm animate-bounce"
                                                style="cursor: pointer"
                                            >
                                                <i
                                                    class="bi bi-exclamation-triangle-fill me-1"
                                                ></i>
                                                Segera Kembalikan Buku!
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-primary px-3 py-2 shadow-sm animate-bounce"
                                                style="cursor: pointer"
                                            >
                                                <i class="bi bi-book me-1"></i>
                                                Sedang Dipinjam
                                            </span>
                                        @endif
                                    </div>

                                    <h5
                                        class="card-title fw-bold text-white mb-2 fs-4 capitalize"
                                    >
                                        {{ $item->buku->judul }}
                                    </h5>

                                    <div class="text-white-50 small mb-3">
                                        <div
                                            class="d-flex align-items-center mb-1"
                                        >
                                            <i
                                                class="bi bi-calendar-check me-2"
                                            ></i>
                                            <span
                                                >Pinjam: {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</span
                                            >
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i
                                                class="bi bi-calendar-x me-2"
                                            ></i>
                                            <span
                                                >Batas: {{ $tglJatuhTempo->format('d M Y') }}</span
                                            >
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        @if ($isWaitingAdmin)
                                            {{-- 1. Pastikan Form ini ADA di dalam loop dan ID-nya unik --}}
                                            <form
                                                id="form-cancel-{{ $item->id_pinjam }}"
                                                {{-- !! ID harus pakai id_pinjam --}}
                                                action="{{ route('peminjaman.cancel', $item->id_pinjam) }}"
                                                method="POST"
                                                style="display: none"
                                            >
                                                @csrf
                                                @method ('DELETE')
                                            </form>
                                            {{-- 2. Pastikan onClick mengirim id_pinjam yang sama --}}
                                            <button
                                                type="button"
                                                class="btn btn-outline-light w-100 py-2 fw-bold"
                                                onclick="konfirmasiBatal('{{ $item->id_pinjam }}')"
                                                {{-- !! Harus kirim id_pinjam --}}
                                            >
                                                <i
                                                    class="bi bi-x-circle me-1"
                                                ></i>
                                                Batalkan Peminjaman
                                            </button>
                                        @elseif ($isWaitingReturn)
                                            <button
                                                type="button"
                                                class="btn btn-secondary w-100 py-2 fw-bold shadow-sm"
                                                disabled
                                            >
                                                Sedang Diproses Admin
                                            </button>

                                        @elseif ($isTerlambat)
                                            <button
                                                type="button"
                                                onclick="showDendaDetail('{{ $item->buku->judul }}', {{ $jumlahHariTerlambat }}, {{ $totalDenda }})"
                                                class="btn btn-danger w-100 py-2 fw-bold shadow-sm"
                                            >
                                                Cek Detail Denda
                                            </button>

                                        @else
                                            {{-- Kondisi Normal: Tombol Kuning muncul --}}
                                            <form
                                                id="form-kembali-{{ $item->id_pinjam }}"
                                                action="{{ route('peminjaman.ajukan_kembali', $item->id_pinjam) }}"
                                                method="POST"
                                                style="display: none"
                                            >
                                                @csrf
                                                @method ('PUT')
                                            </form>
                                            <button
                                                type="button"
                                                onclick="konfirmasiKembali('{{ $item->id_pinjam }}')"
                                                class="btn btn-warning w-100 py-2 fw-bold text-dark shadow"
                                            >
                                                Ajukan Pengembalian
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                    <div
                        class="text-center py-16 bg-white rounded-lg border-2 border-dashed border-gray-300"
                    >
                        <i
                            class="bi bi-bookmark-x text-5xl text-gray-300 mb-4 block"
                        ></i>
                        <p class="text-gray-500 font-medium">Belum ada peminjaman.</p>
                        <a
                            href="{{ route('katalog') }}"
                            class="text-blue-500 hover:text-blue-700 underline mt-2 inline-block"
                            >Jelajahi Katalog Buku</a
                        >
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <style>
        .card-custom {
            border-radius: 15px;
            min-height: 400px;
            transition: all 0.3s ease;
        }
        .card-custom:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2) !important;
        }
        .card-bg-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            z-index: 1;
            transition: transform 0.5s ease;
        }
        .card-custom:hover .card-bg-img {
            transform: scale(1.1);
        }
        @keyframes pulse {
            0% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
            100% {
                opacity: 1;
            }
        }
        .animate-pulse {
            animation: pulse 2s infinite;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fungsi Detail Denda & WhatsApp Admin
        function showDendaDetail(judul, hari, total) {
            const formatRupiah = (angka) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                }).format(angka);
            };

            Swal.fire({
                title: '<span class="text-danger">Detail Keterlambatan</span>',
                html: `
                <div class="text-start border p-3 rounded bg-light">
                    <p class="mb-1"><strong>Judul Buku:</strong> <br>${judul}</p>
                    <hr>
                    <p class="mb-1"><strong>Jumlah Hari:</strong> ${hari} Hari</p>
                    <p class="mb-0 text-danger"><strong>Total Denda:</strong> ${formatRupiah(total)}</p>
                </div>
                <p class="mt-3 small text-muted">Harap segera mengembalikan buku ke perpustakaan untuk menghindari penambahan denda.</p>
            `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#25D366',
                cancelButtonColor: '#6c757d',
                confirmButtonText:
                    '<i class="bi bi-whatsapp me-2"></i>Hubungi Admin',
                cancelButtonText: 'Tutup',
            }).then((result) => {
                if (result.isConfirmed) {
                    const noAdmin = '6281234567890'; // Ganti dengan nomor admin kamu
                    const pesan = `Halo Admin, saya ingin bertanya terkait denda keterlambatan buku "${judul}" selama ${hari} hari.`;
                    window.open(
                        `https://wa.me/${noAdmin}?text=${encodeURIComponent(pesan)}`,
                        '_blank'
                    );
                }
            });
        }

        // Fungsi Konfirmasi Pengembalian
        function konfirmasiKembali(idPinjam, denda, hariTelat) {
            let pesan =
                'Apakah Anda yakin ingin mengajukan pengembalian buku ini?';
            let icon = 'question';

            if (denda > 0) {
                pesan = `Anda terlambat ${hariTelat} hari. Denda sebesar Rp ${new Intl.NumberFormat('id-ID').format(denda)} harus dibayarkan ke petugas.`;
                icon = 'warning';
            }

            Swal.fire({
                title: 'Konfirmasi Pengembalian',
                text: pesan,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Ajukan!',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    document
                        .getElementById(`form-kembali-${idPinjam}`)
                        .submit();
                }
            });
        }

        function konfirmasiBatal(idPinjam) {
            Swal.fire({
                title: 'Batalkan Peminjaman?',
                text: 'Pesanan buku Anda akan dibatalkan secara permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Kembali',
                reverseButtons: true, // Opsional: Biar tombol batal di kiri, ya di kanan
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById(
                        `form-cancel-${idPinjam}`
                    );

                    if (form) {
                        // --- Tambahkan Bagian Loading Ini ---
                        Swal.fire({
                            title: 'Memproses Pembatalan...',
                            text: 'Mohon tunggu sebentar ya.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });
                        // ------------------------------------

                        form.submit();
                    } else {
                        console.error(
                            `Form dengan ID form-cancel-${idPinjam} tidak ditemukan!`
                        );
                        Swal.fire({
                            icon: 'error',
                            title: 'Waduh!',
                            text: 'Terjadi kesalahan teknis, form tidak ditemukan.',
                            confirmButtonColor: '#6366F1',
                        });
                    }
                }
            });
        }
    </script>
</x-app-layout>
