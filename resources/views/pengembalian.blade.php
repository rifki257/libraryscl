<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    {{-- Pindahkan x-data ke level teratas agar mencakup tabel DAN modal --}}
    <div class="py-12" x-data="{ openModal: false, selectedItem: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr class="text-capitalize text-center">
                        <th>Peminjam</th>
                        <th>Buku</th>
                        <th>Tgl pinjam</th>
                        <th>Jatuh tempo</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($semuaPeminjaman as $item)
                        @php
                            $jt = \Carbon\Carbon::parse($item->tgl_jatuh_tempo)->startOfDay();
                            $hariIni = now()->startOfDay();
                            $isTelat = $hariIni->gt($jt);
                            $selisihHari = $isTelat ? abs($hariIni->diffInDays($jt)) : 0;
                            $totalDenda = $selisihHari * 50000;
                        @endphp
                        <tr class="text-capitalize text-center align-middle">
                            <td>{{ $item->user->name }}</td>
                            <td>{{ $item->buku->judul ?? '-' }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($item->tgl_jatuh_tempo)->format('d M Y') }}
                            </td>
                            <td>
                                @if ($item->status == 'dipinjam' && !$isTelat)
                                    <span
                                        class="bg-gray-100 text-gray-500 px-3 py-1 rounded text-[10px] font-bold uppercase border border-gray-200"
                                    >
                                        Sedang Dipinjam
                                    </span>
                                @else
                                    <button
                                        type="button"
                                        {{-- Tambahkan type button agar tidak trigger submit --}}
                                        @click="
                                            selectedItem = { 
                                                id: '{{ $item->id_pinjam }}',
                                                name: '{{ $item->user->name }}',
                                                judul: '{{ $item->buku->judul }}',
                                                kelas: '{{ $item->user->kelas }}',
                                                email: '{{ $item->user->email }}',
                                                nis: '{{ $item->user->nis }}',
                                                totalHari: {{ $selisihHari }},
                                                totalDenda: '{{ number_format($totalDenda, 0, ',', '.') }}'
                                            };
                                            openModal = true;
                                        "
                                        class="{{ $isTelat ? 'bg-red-500 hover:bg-red-600' : 'bg-indigo-500 hover:bg-indigo-600' }} text-white px-4 py-1.5 rounded-md text-xs font-bold uppercase shadow-sm transition"
                                    >
                                        {{ $isTelat ? 'Konfirmasi Denda' : 'Konfirmasi' }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="px-6 py-12 text-center text-gray-500 italic"
                            >
                                Belum ada data.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL BOX - Pastikan berada di dalam div x-data --}}
        <div
            x-show="openModal"
            class="fixed inset-0 z-[999] overflow-y-auto"
            {{-- Naikkan Z-index --}}
            x-transition
            x-cloak
        >
            <div
                class="flex items-center justify-center min-h-screen px-4 pb-20 text-center"
            >
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="openModal = false"
                ></div>

                <div
                    class="inline-block bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full overflow-hidden text-left align-middle"
                >
                    <div
                        class="px-6 py-4 bg-gray-50 border-b border-gray-200 font-bold text-gray-800"
                    >
                        Detail Konfirmasi Pengembalian
                    </div>

                    <div class="px-6 py-4 space-y-3">
                        <div class="flex justify-between border-b pb-2">
                            <span
                                class="text-gray-500 text-xs font-bold uppercase"
                                >Nama Peminjam</span
                            >
                            <span
                                class="text-gray-900 text-sm font-semibold"
                                x-text="selectedItem.name"
                            ></span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span
                                class="text-gray-500 text-xs font-bold uppercase"
                                >Judul Buku</span
                            >
                            <span
                                class="text-gray-900 text-sm font-semibold"
                                x-text="selectedItem.judul"
                            ></span>
                        </div>

                        <div
                            x-show="selectedItem.totalHari > 0"
                            class="p-3 bg-red-50 rounded-md border border-red-100 mt-4"
                        >
                            <div class="flex justify-between">
                                <span
                                    class="text-red-700 text-xs font-bold uppercase"
                                    >Total Keterlambatan</span
                                >
                                <span class="text-red-700 text-sm font-bold"
                                    ><span
                                        x-text="selectedItem.totalHari"
                                    ></span>
                                    Hari</span
                                >
                            </div>
                            <div class="flex justify-between mt-1">
                                <span
                                    class="text-red-700 text-xs font-bold uppercase"
                                    >Total Denda</span
                                >
                                <span
                                    class="text-red-700 text-lg font-black italic"
                                    >Rp
                                    <span
                                        x-text="selectedItem.totalDenda"
                                    ></span
                                ></span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 flex gap-2">
                        <button
                            type="button"
                            @click="openModal = false"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md font-bold text-xs uppercase"
                        >
                            Batal
                        </button>

                        <form
                            :action="'/admin/konfirmasi-kembali/' +
                            selectedItem.id"
                            method="POST"
                            class="flex-1"
                        >
                            @csrf
                            @method ('PUT')
                            <button
                                type="submit"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-bold text-xs uppercase transition shadow-md"
                            >
                                Konfirmasi Selesai
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
