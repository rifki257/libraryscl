<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex justify-end gap-3">
                <div id="bulk-action-container" class="invisible">
                    <button
                        onclick="pinjamTerpilih()"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-1 rounded-full font-bold text-sm shadow-lg transition-all flex items-center gap-2"
                    >
                        <i class="bi bi-check-all"></i> Pinjam
                        (<span id="count-checked">0</span>)
                    </button>
                </div>
                <span
                    class="inline-flex items-center px-4 py-1 bg-[#6366F1] text-white text-sm font-bold rounded-full shadow-sm"
                >
                    {{ $wishlistItems->count() }} Buku
                </span>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- DIV ABU-ABU HANYA SATU (PEMBUNGKUS LUAR) --}}
            <div
                class="overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6"
                style="background-color: rgb(235, 235, 235)"
            >
                @forelse ($wishlistItems as $item)
                    {{-- CARD BIRU (YANG DILOOPING) --}}
                    <div
                        class="bg-[#467599] p-6 rounded-lg max-w-4xl mx-auto text-white flex gap-6 items-stretch min-h-[200px] shadow-md relative"
                    >
                        <div class="absolute top-4 right-4 z-50">
                            <input
                                type="checkbox"
                                name="buku_terpilih[]"
                                value="{{ $item->buku->id_buku }}"
                                class="wishlist-checkbox w-6 h-6 rounded border-white/40 text-blue-600 focus:ring-blue-500 bg-white/20 cursor-pointer"
                                onchange="updateBulkAction()"
                            />
                        </div>
                        {{-- Gambar Buku --}}
                        <div class="relative flex-shrink-0">
                            <img
                                src="{{ asset('storage/' . $item->buku->gambar) }}"
                                alt="{{ $item->buku->judul }}"
                                class="w-32 h-full object-cover rounded-md shadow-lg"
                            />
                        </div>

                        {{-- Konten Buku --}}
                        <div class="flex flex-col flex-grow">
                            <div class="flex-grow">
                                <h2 class="text-xl font-bold mb-1 capitalize">
                                    {{ $item->buku->judul }}
                                </h2>
                                <p class="text-gray-300 text-sm mb-2">Penulis: <span class="text-white font-medium">{{ $item->buku->penulis }}</span></p>
                                <p class="text-blue-200/70 text-[10px]">
                                    Dimasukkan pada: {{ $item->created_at->translatedFormat('d F Y, H:i') }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between mt-6">
                                <div
                                    class="flex items-center gap-2 border border-white/20 bg-white/5 p-2 rounded-md"
                                >
                                    <span class="text-xs text-blue-100"
                                        >Sisa Jatah:</span
                                    >
                                    <span class="font-bold text-white text-sm"
                                        >{{ $sisaJatah }} / 6</span
                                    >
                                </div>

                                <div class="flex items-center gap-4">
                                    {{-- Form Remove --}}
                                    <form
                                        action="{{ route('wishlist.destroy', $item->id_wishlist) }}"
                                        method="POST"
                                        class="m-0"
                                    >
                                        @csrf
                                        @method ('DELETE')
                                        <button
                                            type="submit"
                                            onclick="
                                                return confirm(
                                                    'Yakin ingin menghapus?'
                                                );
                                            "
                                            class="text-gray-300 hover:text-white text-sm transition-colors underline-offset-4 hover:underline"
                                        >
                                            Remove
                                        </button>
                                    </form>

                                    {{-- Tombol Pinjam --}}
                                    <a
                                        href="{{ route('peminjaman', $item->buku->id_buku) }}"
                                        class="px-6 py-2 border-2 border-white text-white hover:bg-white hover:text-[#467599] rounded-md font-bold text-sm transition-all"
                                    >
                                        Pinjam
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- TAMPILAN JIKA KOSONG --}}
                    <div class="text-center py-10">
                        <p class="text-gray-500">Wishlist kamu kosong. <a href="{{ route('katalog') }}" class="text-blue-500 underline">Cari buku sekarang.</a></p>
                    </div>
                @endforelse
            </div>
            {{-- AKHIR DIV ABU-ABU --}}
        </div>
    </div>
    <script>
        function updateBulkAction() {
            const checkboxes = document.querySelectorAll('.wishlist-checkbox:checked');
            const bulkContainer = document.getElementById('bulk-action-container');
            const countLabel = document.getElementById('count-checked');

            countLabel.innerText = checkboxes.length;

            // Jika checkbox yang dicentang >= 2, munculkan tombol
            if (checkboxes.length >= 2) {
                bulkContainer.classList.remove('invisible');
                bulkContainer.classList.add('visible');
            } else {
                bulkContainer.classList.add('invisible');
                bulkContainer.classList.remove('visible');
            }
        }

        function pinjamTerpilih() {
            const checkboxes = document.querySelectorAll('.wishlist-checkbox:checked');
            let ids = Array.from(checkboxes).map((cb) => cb.value);

            // Contoh aksi: Arahkan ke route peminjaman dengan banyak ID
            // Kamu bisa sesuaikan route-nya, misal mengirim query string
            window.location.href =
                '{{ route('peminjaman.masal') }}?ids=' + ids.join(',');
        }
    </script>
</x-app-layout>
