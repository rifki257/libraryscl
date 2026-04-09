<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-[#ebebeb] overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6"
            >
                <div class="flex justify-end gap-3">
                    {{-- Tombol Bulk Action (Muncul jika checklist > 1) --}}
                    <div id="bulk-action-container" class="invisible">
                        <button
                            onclick="eksekusiLangsung()"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-1 rounded-full font-bold text-sm shadow-lg transition-all flex items-center gap-2"
                        >
                            <i class="bi bi-check-all text-lg"></i> Pinjam
                            (<span id="count-checked">0</span>)
                        </button>
                    </div>
                    <span
                        class="inline-flex items-center px-4 py-1 bg-[#6366F1] text-white text-sm font-bold rounded-full shadow-sm"
                    >
                        {{ $wishlistItems->count() }} Buku
                    </span>
                </div>
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
                                    <form
                                        id="form-remove-{{ $item->id_wishlist }}"
                                        action="{{ route('wishlist.destroy', $item->id_wishlist) }}"
                                        method="POST"
                                        class="m-0"
                                    >
                                        @csrf
                                        @method ('DELETE')
                                        <button
                                            type="button"
                                            {{-- Ubah dari submit ke button agar tidak langsung kirim --}}
                                            onclick="konfirmasiHapus('form-remove-{{ $item->id_wishlist }}')"
                                            class="text-gray-300 hover:text-red-400 text-sm transition-colors underline-offset-4 hover:underline"
                                        >
                                            Remove
                                        </button>
                                    </form>

                                    @if ($sisaJatah > 0)
                                        <a
                                            href="{{ route('peminjaman.beda', ['id' => $item->buku->id_buku]) }}"
                                            class="px-6 py-2 border-2 border-white text-white hover:bg-white hover:text-[#467599] rounded-md font-bold text-sm transition-all shadow-sm"
                                        >
                                            Pinjam
                                        </a>
                                    @else
                                        <button
                                            disabled
                                            class="px-6 py-2 border-2 border-gray-400 text-gray-400 cursor-not-allowed rounded-md font-bold text-sm shadow-sm"
                                            title="Jatah peminjaman kamu sudah habis"
                                        >
                                            Pinjam
                                        </button>
                                    @endif
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const modal = document.getElementById('peminjaman-modal');
        const modalContent = document.getElementById('modal-content');
        const sisaJatahAwal = {{ $sisaJatah }};

        function updateBulkAction() {
            const checkboxes = document.querySelectorAll('.wishlist-checkbox:checked');
            const bulkContainer = document.getElementById('bulk-action-container');
            const countLabel = document.getElementById('count-checked');

            countLabel.innerText = checkboxes.length;

            // Validasi Modern dengan SweetAlert2
            if (checkboxes.length > sisaJatahAwal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kuota Peminjaman Penuh',
                    text: `Sisa jatah pinjam buku kamu ${sisaJatahAwal} lagi.`,
                    confirmButtonColor: '#6366F1',
                    showConfirmButton: true,
                    timer: 5000,
                });

                event.target.checked = false;
                countLabel.innerText = document.querySelectorAll(
                    '.wishlist-checkbox:checked'
                ).length;
                return;
            }

            if (checkboxes.length >= 2) {
                bulkContainer.classList.replace('invisible', 'visible');
            } else {
                bulkContainer.classList.replace('visible', 'invisible');
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

        function eksekusiLangsung() {
            const checkboxes = document.querySelectorAll('.wishlist-checkbox:checked');
            const ids = Array.from(checkboxes).map((cb) => cb.value);

            if (ids.length > sisaJatahAwal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Limit Tercapai',
                    text: 'Kurangi pilihan buku kamu agar sesuai jatah.',
                    confirmButtonColor: '#EF4444',
                });
                return;
            }

            if (ids.length > 0) {
                Swal.fire({
                    title: 'Mohon Tunggu',
                    text: 'Sedang menyiapkan data peminjaman...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                window.location.href = `{{ route('peminjaman.beda') }}?ids=${ids.join(',')}`;
            }
        }

        function konfirmasiHapus(formId) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Buku ini akan dihapus dari daftar wishlist kamu.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', 
                cancelButtonColor: '#6b7280', 
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true, 
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });
                    // Kirim form-nya
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</x-app-layout>
