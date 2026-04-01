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
        $isPending = $item->status == 'pending'; // Pastikan status di DB adalah 'pending'
        
        $gambarBuku = $item->buku->gambar 
            ? asset('storage/' . $item->buku->gambar) 
            : asset('images/default-book.png');
    @endphp

    <div class="col-12 col-md-6 col-lg-4">
        <div class="card shadow border-0 h-100 text-white position-relative overflow-hidden"
             style="border-radius: 15px; min-height: 380px; {{ $isPending ? 'filter: grayscale(1); opacity: 0.8;' : '' }}">
            
            <div class="position-absolute inset-0 w-100 h-100"
                 style="background-image: linear-gradient(to top, rgba(0,0,0,0.9) 20%, rgba(0,0,0,0.4) 60%), url('{{ $gambarBuku }}'); 
                 background-size: cover; background-position: center; z-index: 1;">
            </div>

            <div class="card-body d-flex flex-column justify-content-end p-4 position-relative" style="z-index: 2">
                <div class="mb-3">
                    @if ($isPending)
                        <span class="badge bg-secondary px-3 py-2 shadow-sm">
                            <i class="bi bi-hourglass-split me-1"></i> Menunggu Konfirmasi
                        </span>
                    @else
                        <span class="badge bg-primary px-3 py-2 shadow-sm">
                            <i class="bi bi-book me-1"></i> Sedang Dipinjam
                        </span>
                    @endif
                </div>

                <h5 class="card-title fw-bold text-white mb-2 fs-4">{{ $item->buku->judul }}</h5>

                <div class="text-white-50 small mb-3">
                    <div class="d-flex align-items-center mb-1">
                        <i class="bi bi-calendar-check me-2"></i>
                        <span>Pinjam: {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-x me-2"></i>
                        <span>Batas: {{ $tglJatuhTempo->format('d M Y') }}</span>
                    </div>
                </div>

                <div class="mt-2">
                    @if ($isPending)
                        {{-- TOMBOL PEMBATALAN --}}
                        <form action="{{ route('peminjaman.cancel', $item->id_pinjam) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-light w-100 py-2 fw-bold" 
                                    onclick="return confirm('Yakin ingin membatalkan pengajuan ini?')">
                                <i class="bi bi-x-circle me-1"></i> Batalkan Peminjaman
                            </button>
                        </form>
                    @else
                        {{-- TOMBOL PENGEMBALIAN (Hanya untuk yang sudah dikonfirmasi admin) --}}
                        <form action="{{ route('peminjaman.ajukan_kembali', $item->id_pinjam) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-warning w-100 py-2 fw-bold text-dark shadow">
                                <i class="bi bi-arrow-return-left me-1"></i> Ajukan Pengembalian
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@empty
    @endforelse
                </div>
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
