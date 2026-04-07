<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Wishlist Saya') }}
            </h2>
            <div class="flex justify-end gap-3">
                {{-- Tombol Bulk Action (Muncul jika checklist > 1) --}}
                <div id="bulk-action-container" class="invisible">
                    <button
                        onclick="bukaModalPilihan()"
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-1 rounded-full font-bold text-sm shadow-lg transition-all flex items-center gap-2"
                    >
                        <i class="bi bi-check-all text-lg"></i> Pinjam Terpilih
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
            <div
                class="bg-[#ebebeb] overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6"
            >
                @forelse ($wishlistItems as $item)
                    {{-- Card Buku --}}
                    <div
                        class="bg-[#467599] p-6 rounded-lg max-w-4xl mx-auto text-white flex flex-col md:flex-row gap-6 shadow-md relative border border-white/10"
                    >
                        {{-- Checkbox --}}
                        <div class="absolute top-4 right-4 z-10">
                            <input
                                type="checkbox"
                                name="buku_terpilih[]"
                                value="{{ $item->buku->id_buku }}"
                                class="wishlist-checkbox w-6 h-6 rounded border-white/40 text-blue-600 focus:ring-blue-500 bg-white/20 cursor-pointer"
                                onchange="updateBulkAction()"
                            />
                        </div>

                        {{-- Gambar Buku --}}
                        <div class="w-full md:w-32 flex-shrink-0">
                            <img
                                src="{{ asset('storage/' . $item->buku->gambar) }}"
                                alt="{{ $item->buku->judul }}"
                                class="w-full h-48 md:h-full object-cover rounded-md shadow-lg border border-white/20"
                            />
                        </div>

                        {{-- Konten Buku --}}
                        <div class="flex flex-col flex-grow justify-between">
                            <div>
                                <h2
                                    class="text-xl font-bold mb-1 capitalize leading-tight"
                                >
                                    {{ $item->buku->judul }}
                                </h2>
                                <p class="text-gray-200 text-sm mb-2">
                                    Penulis:
                                    <span
                                        class="text-white font-semibold"
                                        >{{ $item->buku->penulis }}</span
                                    >
                                </p>
                                <p class="text-blue-200/60 text-[10px] italic">
                                    Dimasukkan pada: {{ $item->created_at->translatedFormat('d F Y, H:i') }}
                                </p>
                            </div>

                            <div
                                class="flex flex-wrap items-center justify-between mt-6 gap-4"
                            >
                                {{-- Badge Sisa Jatah --}}
                                <div
                                    class="flex items-center gap-2 border border-white/20 bg-white/10 px-3 py-1.5 rounded-md"
                                >
                                    <span class="text-xs text-blue-100"
                                        >Sisa Jatah Peminjaman:</span
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
                                                    'Yakin ingin menghapus buku ini?'
                                                );
                                            "
                                            class="text-gray-300 hover:text-red-400 text-sm transition-colors underline-offset-4 hover:underline"
                                        >
                                            Remove
                                        </button>
                                    </form>

                                    {{-- Tombol Pinjam Satuan --}}
                                    <a
                                        href="{{ route('peminjaman', $item->buku->id_buku) }}"
                                        class="px-6 py-2 border-2 border-white text-white hover:bg-white hover:text-[#467599] rounded-md font-bold text-sm transition-all shadow-sm"
                                    >
                                        Pinjam
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="text-center py-16 bg-white rounded-lg border-2 border-dashed border-gray-300"
                    >
                        <i
                            class="bi bi-bookmark-x text-5xl text-gray-300 mb-4 block"
                        ></i>
                        <p class="text-gray-500 font-medium">Wishlist kamu masih kosong.</p>
                        <a
                            href="{{ route('katalog') }}"
                            class="text-blue-500 hover:text-blue-700 underline mt-2 inline-block"
                            >Jelajahi Katalog Buku</a
                        >
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL PILIHAN (POP-UP) --}}
    <div
        id="peminjaman-modal"
        class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm"
    >
        <div
            id="modal-content"
            class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md transform transition-all scale-95 opacity-0 duration-300"
        >
            <div class="text-center">
                <div
                    class="bg-indigo-100 text-indigo-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                >
                    <i class="bi bi-calendar2-check text-3xl"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-800">
                    Opsi Peminjaman
                </h3>
                <p class="text-gray-500 text-sm mt-2">Pilih metode pengembalian untuk <span id="modal-count-text" class="font-bold text-indigo-600"></span> buku ini.</p>
            </div>

            <div class="mt-8 space-y-4">
                {{-- Opsi 1: Hari Sama --}}
                <button
                    onclick="eksekusiPeminjaman('sama')"
                    class="w-full flex items-center justify-between px-5 py-4 bg-emerald-50 border-2 border-emerald-100 hover:border-emerald-500 rounded-xl group transition-all"
                >
                    <div class="flex items-center gap-4 text-left">
                        <i
                            class="bi bi-clock-fill text-2xl text-emerald-600"
                        ></i>
                        <div>
                            <span
                                class="block font-bold text-emerald-900 text-sm"
                                >Kembali Di Tanggal yang Sama</span
                            >
                            <span class="text-[11px] text-emerald-700 italic"
                                >Buku di kembalikan pada tanggal yang sama.</span
                            >
                        </div>
                    </div>
                </button>

                {{-- Opsi 2: Hari Berbeda --}}
                <button
                    onclick="eksekusiPeminjaman('beda')"
                    class="w-full flex items-center justify-between px-5 py-4 bg-blue-50 border-2 border-blue-100 hover:border-blue-500 rounded-xl group transition-all"
                >
                    <div class="flex items-center gap-4 text-left">
                        <i
                            class="bi bi-calendar-range-fill text-2xl text-blue-600"
                        ></i>
                        <div>
                            <span class="block font-bold text-blue-900 text-sm"
                                >Kembali Di Tanggal Berbeda</span
                            >
                            <span class="text-[11px] text-blue-700 italic"
                                >Buku di kembalikan sesuai tanggal kembali masing masing.</span
                            >
                        </div>
                    </div>
                </button>

                <button
                    onclick="tutupModal()"
                    class="w-full py-3 text-gray-400 text-sm font-bold hover:text-red-500 transition-colors mt-2"
                >
                    BATALKAN
                </button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('peminjaman-modal');
        const modalContent = document.getElementById('modal-content');

        function updateBulkAction() {
            const checkboxes = document.querySelectorAll('.wishlist-checkbox:checked');
            const bulkContainer = document.getElementById('bulk-action-container');
            const countLabel = document.getElementById('count-checked');

            countLabel.innerText = checkboxes.length;

            if (checkboxes.length >= 2) {
                bulkContainer.classList.remove('invisible');
                bulkContainer.classList.add('visible');
            } else {
                bulkContainer.classList.remove('visible');
                bulkContainer.classList.add('invisible');
            }
        }

        function bukaModalPilihan() {
            const checkboxes = document.querySelectorAll('.wishlist-checkbox:checked');
            document.getElementById('modal-count-text').innerText = checkboxes.length;

            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function tutupModal() {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        function eksekusiPeminjaman(tipe) {
            const checkboxes = document.querySelectorAll('.wishlist-checkbox:checked');
            let ids = Array.from(checkboxes).map((cb) => cb.value);

            if (ids.length > 0) {
                if (tipe === 'sama') {
                    window.location.href = `{{ route('peminjaman.masal') }}?ids=${ids.join(',')}`;
                } else {
                    window.location.href = `{{ route('peminjaman.beda') }}?ids=${ids.join(',')}`;
                }
            }
        }
        modal.addEventListener('click', (e) => {
            if (e.target === modal) tutupModal();
        });
    </script>
</x-app-layout>
