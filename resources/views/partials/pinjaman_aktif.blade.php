<div class="row">
    @forelse ($sedangDipinjam as $item)
        <div class="col-md-4 mb-3">
            <div
                class="card shadow-sm border-warning h-100 text-white"
                style="background-image: linear-gradient(hsla(0, 0%, 0%, 0.7), rgba(0, 0, 0, 0.8)), 
                                    url('{{ $item->buku->gambar ? asset('storage/' . $item->buku->gambar) : asset('images/default-profile.png') }}'); 
                                    background-size: cover; background-position: center; border-radius: 10px; overflow: hidden;"
            >
                <div
                    class="card-body d-flex flex-column justify-content-end p-4"
                >
                    @php
                                            $tglJatuhTempo = \Carbon\Carbon::parse($item->tgl_jatuh_tempo)->startOfDay();
                                            $hariIni = \Carbon\Carbon::now()->startOfDay();
                                            $selisihHari = $hariIni->diffInDays($tglJatuhTempo, false);
                                            $denda = $selisihHari < 0 ? abs($selisihHari) * 150000 : 0;
                                            $isTelat = $selisihHari < 0;
                                            $jumlahHariTelat = $isTelat ? abs($selisihHari) : 0;
                                        @endphp

                    <h5
                        class="card-title fw-bold text-white mb-2 text-capitalize"
                    >
                        {{ $item->buku->judul }}
                    </h5>

                    @if ($item->status == 'proses')
                        <span class="badge bg-warning text-dark mb-2 py-2"
                            >⏳ Menunggu Verifikasi Admin</span
                        >
                    @elseif ($isTelat)
                        <span class="badge bg-danger mb-2 py-2"
                            >⚠️ Terlambat {{ $jumlahHariTelat }} Hari (Rp {{ number_format($denda, 0, ',', '.') }})</span
                        >
                    @else
                        <span class="badge bg-primary mb-2 py-2"
                            >📖 Sedang Dipinjam</span
                        >
                    @endif

                    <p class="card-text mb-1 text-white-50 text-sm">Pinjam: {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</p>
                    <p
                        class="card-text {{ $isTelat ? 'text-danger fw-bold' : 'text-white' }} mb-3 text-sm"
                    >
                        Batas Kembali: {{ $tglJatuhTempo->format('d M Y') }}
                    </p>

                    @if ($item->status == 'proses')
                        <button
                            class="btn btn-secondary w-100 py-2 fw-bold"
                            disabled
                        >
                            Sudah Diajukan
                        </button>
                    @elseif ($isTelat)
                        <div class="d-flex gap-2 w-100">
                            <button
                                type="button"
                                onclick="
                                    Swal.fire(
                                        'Telat (Denda)',
                                        'Silakan datang ke perpustakaan untuk kembalikan buku dan selesaikan pembayaran denda.',
                                        'error'
                                    )
                                "
                                class="btn btn-danger flex-fill py-2 fw-bold"
                            >
                                Terlambat
                            </button>
                            <button
                                type="button"
                                onclick="showDendaDetail({{ $jumlahHariTelat }}, {{ $denda }})"
                                class="btn btn-primary flex-fill py-2 fw-bold"
                            >
                                Detail
                            </button>
                        </div>
                    @else
                        <form
                            action="{{ route('peminjaman.ajukan_kembali', $item->id_pinjam) }}"
                            method="POST"
                            id="form-kembali-{{ $item->id_pinjam }}"
                            class="m-0"
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
                                class="btn btn-warning w-100 py-2 fw-bold"
                            >
                                Ajukan Pengembalian
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="py-10 text-center w-100">
            <p class="text-gray-500 italic">Kamu tidak sedang meminjam buku apapun.</p>
        </div>
    @endforelse
</div>
