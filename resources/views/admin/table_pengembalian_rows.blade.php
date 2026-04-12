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
        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
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
                    {{ $isTelat ? 'Konfirmasi' : 'Konfirmasi' }}
                </button>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
            Belum ada data.
        </td>
    </tr>
@endforelse
