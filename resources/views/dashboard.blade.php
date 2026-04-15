<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
/>

<style>
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        cursor: pointer;
    }
    .transition {
        transition: all 0.3s ease;
    }
</style>
<x-app-layout>
    <div class="container py-4 px-4">
        <div class="page-inner">
            <div
                class="card border-0 shadow-sm rounded-4"
                style="
                    background: linear-gradient(135deg, rgb(235, 235, 235) 0%);
                "
            >
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        @if (Auth::user()->role === 'kepper')
                            <div class="col-md">
                                <a
                                    href="{{ route('buku') }}"
                                    class="text-decoration-none"
                                >
                                    <div
                                        class="card border-0 shadow-sm h-100 rounded-4 p-3 border-start border-primary border-4 hover-shadow transition"
                                    >
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="bg-primary bg-opacity-10 p-3 rounded-3 me-3"
                                            >
                                                <i
                                                    class="bi bi-journal-bookmark text-primary fs-4"
                                                ></i>
                                            </div>
                                            <div>
                                                <small
                                                    class="text-muted d-block fw-bold"
                                                    style="font-size: 0.7rem"
                                                    >TOTAL BUKU</small
                                                >
                                                <h4
                                                    class="fw-bold mb-0 text-dark"
                                                >
                                                    {{ $totalBuku }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md">
                                <a
                                    href="{{ route('persetujuan.data') }}"
                                    class="text-decoration-none"
                                >
                                    <div
                                        class="card border-0 shadow-sm h-100 rounded-4 p-3 border-start border-warning border-4 hover-shadow transition"
                                    >
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="bg-warning bg-opacity-10 p-3 rounded-3 me-3"
                                            >
                                                <i
                                                    class="bi bi-box-arrow-right text-warning fs-4"
                                                ></i>
                                            </div>
                                            <div>
                                                <small
                                                    class="text-muted d-block fw-bold"
                                                    style="font-size: 0.7rem"
                                                    >PINJAMAN</small
                                                >
                                                <h4
                                                    class="fw-bold mb-0 text-dark"
                                                >
                                                    {{ $totalPinjam }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md">
                                <a
                                    href="{{ route('pengembalian.data') }}"
                                    class="text-decoration-none"
                                >
                                    <div
                                        class="card border-0 shadow-sm h-100 rounded-4 p-3 border-start border-success border-4 hover-shadow transition"
                                    >
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="bg-success bg-opacity-10 p-3 rounded-3 me-3"
                                            >
                                                <i
                                                    class="bi bi-box-arrow-in-left text-success fs-4"
                                                ></i>
                                            </div>
                                            <div>
                                                <small
                                                    class="text-muted d-block fw-bold"
                                                    style="font-size: 0.7rem"
                                                    >KEMBALI</small
                                                >
                                                <h4
                                                    class="fw-bold mb-0 text-dark"
                                                >
                                                    {{ $totalKembali }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md">
                                <a
                                    href="{{ route('akun_admin', ['role' => 'admin']) }}"
                                    class="text-decoration-none"
                                >
                                    <div
                                        class="card border-0 shadow-sm h-100 rounded-4 p-3 border-start border-danger border-4 hover-shadow transition"
                                    >
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="bg-danger bg-opacity-10 p-3 rounded-3 me-3"
                                            >
                                                <i
                                                    class="bi bi-shield-lock text-danger fs-4"
                                                ></i>
                                            </div>
                                            <div>
                                                <small
                                                    class="text-muted d-block fw-bold"
                                                    style="font-size: 0.7rem"
                                                    >ADMIN</small
                                                >
                                                <h4
                                                    class="fw-bold mb-0 text-dark"
                                                >
                                                    {{ $totalAdmin }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md">
                                <a
                                    href="{{ route('akun_user', ['role' => 'user']) }}"
                                    class="text-decoration-none"
                                >
                                    <div
                                        class="card border-0 shadow-sm h-100 rounded-4 p-3 border-start border-info border-4 hover-shadow transition"
                                    >
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="bg-info bg-opacity-10 p-3 rounded-3 me-3"
                                            >
                                                <i
                                                    class="bi bi-people text-info fs-4"
                                                ></i>
                                            </div>
                                            <div>
                                                <small
                                                    class="text-muted d-block fw-bold"
                                                    style="font-size: 0.7rem"
                                                    >USER</small
                                                >
                                                <h4
                                                    class="fw-bold mb-0 text-dark"
                                                >
                                                    {{ $totalUser }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endif
                    </div>
                    {{-- akumulasi --}}
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div
                                class="card shadow-sm border-0 rounded-4 overflow-hidden"
                            >
                                <div
                                    class="p-4"
                                    style="
                                        background: linear-gradient(
                                            135deg,
                                            #667eea 0%,
                                            #764ba2 100%
                                        );
                                        color: white;
                                    "
                                >
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="rounded-circle bg-white bg-opacity-25 p-4 me-4"
                                        >
                                            <i class="bi bi-wallet2 fs-1"></i>
                                        </div>
                                        <div>
                                            <h6
                                                class="text-white-50 mb-1 text-uppercase fw-bold"
                                                style="font-size: 0.8rem"
                                            >
                                                Akumulasi Pendapatan Denda
                                            </h6>
                                            <h2 class="fw-bold mb-0">
                                                Rp {{ number_format($totalDenda, 0, ',', '.') }}
                                            </h2>
                                            <small class="text-white-50"
                                                >*Total dari seluruh riwayat
                                                pengembalian</small
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white">
                                    <div
                                        class="px-4 py-3 d-flex justify-content-between align-items-center border-bottom"
                                    >
                                        <h6
                                            class="badge bg-light text-dark border mb-0"
                                            style="font-size: 0.7rem"
                                        >
                                            Top 5
                                        </h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table
                                            class="table table-hover align-middle mb-0"
                                        >
                                            <tbody style="font-size: 0.9rem">
                                                @forelse ($topDenda as $item)
                                                    <tr>
                                                        <td class="ps-3 py-2">
                                                            <div
                                                                class="fw-bold text-dark"
                                                            >
                                                                {{ $item->user->name }}
                                                            </div>
                                                            <div
                                                                class="text-primary fw-semibold"
                                                                style="
                                                                    font-size: 0.75rem;
                                                                "
                                                            >
                                                                ID Pinjam: #{{ $item->id_pinjam }}
                                                            </div>
                                                        </td>
                                                        <td
                                                            class="text-end pe-4"
                                                        >
                                                            <span
                                                                class="text-danger fw-bold"
                                                                >Rp {{ number_format($item->denda, 0, ',', '.') }}</span
                                                            >
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td
                                                            colspan="2"
                                                            class="text-center py-4 text-muted italic"
                                                        >
                                                            <i
                                                                class="bi bi-info-circle me-1"
                                                            ></i>
                                                            Tidak ada data
                                                            keterlambatan.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- pai chart --}}
                        <div class="col-lg-6">
                            <div
                                class="card shadow-sm border-0 h-100 rounded-4"
                            >
                                <div class="card-header bg-white border-0 py-2">
                                    <h6 class="fw-bold mb-0">
                                        Statistik Sirkulasi Buku
                                    </h6>
                                </div>
                                <div
                                    class="card-body d-flex flex-column align-items-center justify-content-center"
                                >
                                    <div style="width: 250px; height: 250px">
                                        <canvas id="sirkulasiChart"></canvas>
                                    </div>
                                    <div class="mt-4 w-100">
                                        @php
                                        $totalSirkulasi = $totalRiwayat + $totalKembali + $totalPinjam;
                                        $getPercent = function($value, $total) {
                                         return $total > 0 ? round(($value / $total) * 100, 1) : 0;
                                        };
                                    @endphp
                                        <div
                                            class="d-flex justify-content-between mb-2"
                                        >
                                            <span>
                                                <i
                                                    class="bi bi-circle-fill text-primary me-2"
                                                ></i
                                                >Total Peminjaman
                                            </span>
                                            <span class="fw-bold">
                                                {{ $totalRiwayat }}
                                                <small class="text-muted ms-1"
                                                    >({{ $getPercent($totalRiwayat, $totalSirkulasi) }}%)</small
                                                >
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between mb-2"
                                        >
                                            <span>
                                                <i
                                                    class="bi bi-circle-fill text-success me-2"
                                                ></i
                                                >Sudah Kembali
                                            </span>
                                            <span class="fw-bold">
                                                {{ $totalKembali }}
                                                <small class="text-muted ms-1"
                                                    >({{ $getPercent($totalKembali, $totalSirkulasi) }}%)</small
                                                >
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between"
                                        >
                                            <span>
                                                <i
                                                    class="bi bi-circle-fill text-warning me-2"
                                                ></i
                                                >Masih Dipinjam
                                            </span>
                                            <span class="fw-bold">
                                                {{ $totalPinjam }}
                                                <small class="text-muted ms-1"
                                                    >({{ $getPercent($totalPinjam, $totalSirkulasi) }}%)</small
                                                >
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script>
        Chart.register(ChartDataLabels);

        const ctx = document.getElementById('sirkulasiChart').getContext('2d');
        const sirkulasiChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Total Riwayat', 'Sudah Kembali', 'Masih Dipinjam'],
                datasets: [
                    {
                        data: [
                            {{ $totalRiwayat }},
                            {{ $totalKembali }},
                            {{ $totalPinjam }},
                        ],
                        backgroundColor: [
                            'rgba(71, 118, 153, 0.8)',
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    datalabels: {
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 12,
                        },
                        formatter: (value, ctx) => {
                            let sum = 0;
                            let dataArr = ctx.chart.data.datasets[0].data;
                            dataArr.map((data) => {
                                sum += data;
                            });
                            let percentage = ((value * 100) / sum).toFixed(1) + '%';
                            return value > 0 ? percentage : '';
                        },
                    },
                },
            },
        });
    </script>
</x-app-layout>
