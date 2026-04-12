<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                    <div
                        class="bg-gray-50 p-6 rounded-xl border border-gray-100 flex flex-col items-center"
                    >
                        <h3
                            class="text-md font-bold mb-4 text-gray-700 self-start italic"
                        >
                            Statistik Sirkulasi
                        </h3>
                        <div style="width: 250px; height: 250px">
                            <canvas id="loanChart"></canvas>
                        </div>

                        <div class="mt-6 flex gap-4 text-xs font-medium">
                            <span class="flex items-center"
                                ><span
                                    class="w-3 h-3 bg-yellow-400 rounded-full mr-1"
                                ></span>
                                Ajuan</span
                            >
                            <span class="flex items-center"
                                ><span
                                    class="w-3 h-3 bg-blue-500 rounded-full mr-1"
                                ></span>
                                Pinjam</span
                            >
                            <span class="flex items-center"
                                ><span
                                    class="w-3 h-3 bg-purple-500 rounded-full mr-1"
                                ></span>
                                Kembali</span
                            >
                        </div>
                    </div>

                    <div class="overflow-hidden">
                        <h3
                            class="text-md font-bold mb-4 text-gray-700 flex items-center"
                        >
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="Header 12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Aktivitas Sirkulasi Terakhir
                        </h3>
                        <div
                            class="bg-white border border-gray-200 rounded-xl overflow-x-auto shadow-sm"
                        >
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 text-left">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-xs font-bold text-gray-500 uppercase"
                                        >
                                            Buku
                                        </th>
                                        <th
                                            class="px-4 py-3 text-xs font-bold text-gray-500 uppercase"
                                        >
                                            Status
                                        </th>
                                        <th
                                            class="px-4 py-3 text-xs font-bold text-gray-500 uppercase"
                                        >
                                            Tanggal
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 text-sm">
                                    @foreach ($recentActivities as $activity)
                                        <tr>
                                            <td
                                                class="px-4 py-3 font-medium text-gray-900"
                                            >
                                                {{ $activity->buku->judul }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @php
                $statusClasses = [
                    'menunggu' => 'bg-yellow-100 text-yellow-700',
                    'dipinjam' => 'bg-blue-100 text-blue-700',
                    'proses'   => 'bg-purple-100 text-purple-700',
                    'kembali'  => 'bg-success-100 text-green-700',
                ];
                $class = $statusClasses[$activity->status] ?? 'bg-gray-100 text-gray-700';
            @endphp
                                                <span
                                                    class="px-2 py-1 text-xs font-semibold rounded-full {{ $class }}"
                                                >
                                                    {{ ucfirst($activity->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-500">
                                                {{ $activity->updated_at->format('d M Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 text-right">
                            <a
                                href="{{ route('laporan_user') }}"
                                class="text-xs text-blue-600 font-bold hover:underline"
                                >Lihat Semua Riwayat →</a
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    const ctx = document.getElementById('loanChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Diajukan Pinjam', 'Sedang Dipinjam', 'Diajukan Kembali'],
            datasets: [
                {
                    // DATA REAL DARI DATABASE MASUK DISINI
                    data: [
                        @json ($countAjuan),
                        @json ($countPinjam),
                        @json ($countKembali),
                    ],
                    backgroundColor: [
                        '#FACC15', // Ajuan
                        '#3B82F6', // Pinjam
                        '#A855F7', // Kembali
                    ],
                    borderWidth: 0,
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
            },
        },
    });
</script>
