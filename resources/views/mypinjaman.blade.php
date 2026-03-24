<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Buku yang Sedang Dipinjam') }}
            </h2>
            <span class="badge bg-primary rounded-pill"
                >{{ $sedangDipinjam->count() }} Buku</span
            >
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 p-md-5"
            >
                <div class="row g-4">
                    @forelse ($sedangDipinjam as $item)
                        @php
                            $tglJatuhTempo = \Carbon\Carbon::parse($item->tgl_jatuh_tempo)->startOfDay();
                            $hariIni = \Carbon\Carbon::now()->startOfDay();
                            $selisihHari = $hariIni->diffInDays($tglJatuhTempo, false);
                            $dendaValue = 150000; 
                            $denda = $selisihHari < 0 ? abs($selisihHari) * $dendaValue : 0;
                            $isTelat = $selisihHari < 0;
                            $jumlahHariTelat = $isTelat ? abs($selisihHari) : 0;
                            
                            $gambarBuku = $item->buku->gambar 
                                ? asset('storage/' . $item->buku->gambar) 
                                : asset('images/default-book.png');
                        @endphp
                        <div class="col-12 col-md-6 col-lg-4">
                            <div
                                class="card shadow border-0 h-100 text-white position-relative overflow-hidden"
                                style="border-radius: 15px; min-height: 380px"
                            >
                                <div
                                    class="position-absolute inset-0 w-100 h-100"
                                    style="background-image: linear-gradient(to top, rgba(0,0,0,0.95) 20%, rgba(0,0,0,0.4) 60%, rgba(0,0,0,0.7) 100%), url('{{ $gambarBuku }}'); 
                                            background-size: cover; background-position: center; z-index: 1;"
                                ></div>

                                <div
                                    class="card-body d-flex flex-column justify-content-end p-4 position-relative"
                                    style="z-index: 2"
                                >
                                    <div class="mb-3">
                                        @if ($item->status == 'proses')
                                            <span
                                                class="badge bg-warning text-dark px-3 py-2 shadow-sm"
                                            >
                                                <i
                                                    class="bi bi-clock-history me-1"
                                                ></i>
                                                Menunggu Verifikasi
                                            </span>
                                        @elseif ($isTelat)
                                            <span
                                                class="badge bg-danger px-3 py-2 shadow-sm"
                                            >
                                                <i
                                                    class="bi bi-exclamation-triangle me-1"
                                                ></i>
                                                Terlambat {{ $jumlahHariTelat }} Hari
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-primary px-3 py-2 shadow-sm"
                                            >
                                                <i class="bi bi-book me-1"></i>
                                                Sedang Dipinjam
                                            </span>
                                        @endif
                                    </div>

                                    <h5
                                        class="card-title fw-bold text-white mb-2 fs-4 text-capitalize"
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
                                        <div
                                            class="d-flex align-items-center {{ $isTelat ? 'text-danger fw-bold' : '' }}"
                                        >
                                            <i
                                                class="bi bi-calendar-x me-2"
                                            ></i>
                                            <span
                                                >Batas: {{ $tglJatuhTempo->format('d M Y') }}</span
                                            >
                                        </div>
                                    </div>

                                    <div class="mt-2">
                                        @if ($item->status == 'proses')
                                            <button
                                                class="btn btn-light w-100 py-2 fw-bold opacity-75 mb-3"
                                                disabled
                                            >
                                                <i
                                                    class="bi bi-hourglass-split me-1"
                                                ></i>
                                                Permintaan Diproses...
                                            </button>
                                        @elseif ($isTelat)
                                            <div class="row g-2">
                                                <div class="col-8">
                                                    <button
                                                        type="button"
                                                        onclick="Swal.fire('Perhatian', 'Harap hubungi petugas perpustakaan untuk pelunasan denda Rp {{ number_format($denda, 0, ',', '.') }}', 'warning')"
                                                        class="btn btn-danger w-100 py-2 fw-bold mb-3"
                                                    >
                                                        Selesaikan Denda
                                                    </button>
                                                </div>
                                                <div class="col-4">
                                                    <button
                                                        type="button"
                                                        onclick="showDendaDetail({{ $jumlahHariTelat }}, {{ $denda }})"
                                                        class="btn btn-outline-light w-100 py-2"
                                                    >
                                                        <i
                                                            class="bi bi-info-circle"
                                                        ></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <form
                                                action="{{ route('peminjaman.ajukan_kembali', $item->id_pinjam) }}"
                                                method="POST"
                                                id="form-kembali-{{ $item->id_pinjam }}"
                                            >
                                                @csrf
                                                @method ('PUT')
                                                <input
                                                    type="hidden"
                                                    name="denda"
                                                    value="{{ $denda }}"
                                                />
                                                <button
                                                    type="button"
                                                    onclick="konfirmasiKembali({{ $item->id_pinjam }}, {{ $denda }}, {{ $jumlahHariTelat }})"
                                                    class="btn btn-warning w-100 py-2 fw-bold text-dark shadow"
                                                >
                                                    <i
                                                        class="bi bi-arrow-return-left me-1"
                                                    ></i>
                                                    Ajukan Pengembalian
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <h5 class="text-gray-500 fw-medium">
                                Wah, rak bukumu kosong...
                            </h5>
                            <p class="text-muted small">Kamu tidak memiliki pinjaman aktif saat ini.</p>
                            <a
                                href="{{ route('katalog') }}"
                                class="btn btn-primary mt-3 px-4 rounded-pill"
                                >Cari Buku Sekarang</a
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

        function showDendaDetail(hari, nominal) {
            Swal.fire({
                title: 'Detail Keterlambatan',
                html: `<div class="text-start">Total Hari: <b>${hari} Hari</b><br>Total Denda: <b class="text-danger">Rp ${new Intl.NumberFormat('id-ID').format(nominal)}</b></div>`,
                icon: 'info',
            });
        }
    </script>
</x-app-layout>
