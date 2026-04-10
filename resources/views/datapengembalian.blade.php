<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr class="text-capitalize text-center">
                        <th>Peminjam</th>
                        <th>Buku</th>
                        <th>Tgl pinjam</th>
                        <th>Tgl Jatuh tempo</th>
                        <th>Tgl Kembali</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($semuaPeminjaman as $item)
                        @php
                                    $tanggalSelesai = $item->tgl_kembali ?? $item->updated_at;
                                    $jt = \Carbon\Carbon::parse($item->tgl_jatuh_tempo)->startOfDay();
                                    $kb = \Carbon\Carbon::parse($item->updated_at)->startOfDay();

                                    $isTelat = $kb->gt($jt);
                                    $selisihHari = $isTelat ? $kb->diffInDays($jt) : 0;
                                    $totalDenda = $selisihHari * 50000;
                                @endphp
                        <tr class="text-capitalize text-center align-middle">
                            <td>
                                <div class="flex flex-col">
                                    <span>{{ $item->user->name }}</span>
                                </div>
                            </td>
                            <td>
                                <div>{{ $item->buku->judul }}</div>
                            </td>
                            <td>
                                <div>
                                    {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}
                                </div>
                            </td>
                            <td>
                                <div>
                                    {{ \Carbon\Carbon::parse($item->tgl_jatuh_tempo)->format('d M Y') }}
                                </div>
                            </td>
                            <td>
                                <div>
                                    @if ($item->tgl_kembali)
                                        {{ \Carbon\Carbon::parse($item->tgl_kembali)->format('d M Y') }}
                                    @else
                                        <span>{{ $kb->format('d M Y') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if ($isTelat)
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-tight bg-red-100 text-red-700 border border-red-200"
                                    >
                                        Rp {{ number_format($totalDenda, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-tight bg-emerald-100 text-emerald-700 border border-emerald-200"
                                    >
                                        Tepat Waktu
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div
                                    class="flex flex-col items-center justify-center"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-200 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-gray-400 italic text-sm">Belum ada riwayat pengembalian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
