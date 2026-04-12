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
            <div class="flex flex-col items-center justify-center">
                <p class="text-gray-400 italic text-sm">Belum ada riwayat pengembalian.</p>
            </div>
        </td>
    </tr>
@endforelse
