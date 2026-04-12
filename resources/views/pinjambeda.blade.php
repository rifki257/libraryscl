<x-app-layout>
    <div
        class="py-12"
        x-data="{
            // Objek untuk menyimpan status pengisian tiap buku
            filledInputs: {},
            // Fungsi untuk mengecek apakah semua sudah terisi
            get canSubmit() {
                const itemCount =
                    document.querySelectorAll('.book-item').length;
                if (itemCount === 0) return false;

                // Menghitung berapa banyak nilai di filledInputs yang bernilai true
                const filledCount = Object.values(this.filledInputs).filter(
                    (v) => v === true
                ).length;
                return filledCount === itemCount;
            },
        }"
    >
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alert Error --}}
            @if (session('error'))
                <div
                    class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                >
                    {{ session('error') }}
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-xl font-bold mb-6 text-gray-800 border-b pb-4">
                    Konfirmasi Peminjaman
                </h2>

                <form
                    action="{{ route('peminjaman.store.masal') }}"
                    method="POST"
                    id="main-form"
                >
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @forelse ($bukuTerpilih as $index => $buku)
                            <div
                                x-data="itemPeminjaman('{{ $index }}')"
                                class="p-4 bg-gray-50 rounded-xl border border-gray-200 shadow-sm book-item relative flex flex-col justify-between"
                            >
                                <div class="flex flex-col gap-4">
                                    <div class="flex gap-3 relative">
                                        <input
                                            type="hidden"
                                            name="id_buku[]"
                                            value="{{ $buku->id_buku }}"
                                        />

                                        <img
                                            src="{{ asset('storage/' . $buku->gambar) }}"
                                            class="w-16 h-24 object-cover rounded-lg shadow-md flex-shrink-0"
                                        />

                                        <div class="flex-grow pr-12">
                                            <h3
                                                class="text-md font-bold text-blue-900 capitalize leading-tight mb-1 line-clamp-2"
                                            >
                                                {{ $buku->judul }}
                                            </h3>
                                            <p class="text-[11px] text-blue-700">Stok: <span class="font-bold">{{ $buku->jumlah }}</span></p>
                                            <p class="text-[9px] text-red-600 italic">Denda: Rp50rb/Hari</p>
                                        </div>

                                        <button
                                            type="button"
                                            @click="removeBook($el, '{{ $index }}')"
                                            class="absolute top-0 right-0 text-red-500 hover:text-red-700 flex items-center gap-1 transition-colors"
                                        >
                                            <span
                                                class="text-[13px] font-bold uppercase"
                                                >Hapus</span
                                            >
                                        </button>
                                    </div>

                                    <div
                                        class="bg-white p-3 rounded-lg border border-gray-100 shadow-sm"
                                    >
                                        <label
                                            class="block text-[9px] font-bold text-gray-400 uppercase mb-1"
                                        >
                                            Tanggal Kembali
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <input
                                                type="date"
                                                name="tgl_kembali[]"
                                                x-model="tglKembali"
                                                @input="updateStatus()"
                                                :min="minDate"
                                                :max="maxDate"
                                                class="flex-grow text-xs border-gray-200 rounded focus:ring-blue-500 p-1.5"
                                                required
                                            />

                                            <div
                                                class="bg-green-50 px-2 py-1.5 rounded border border-green-100 flex-shrink-0"
                                            >
                                                <span
                                                    class="text-[10px] font-black text-green-600"
                                                    x-text="durationText"
                                                ></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div
                                class="text-center py-12 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200"
                            >
                                <p class="text-gray-500">Daftar pinjam kamu kosong.</p>
                                <a
                                    href="{{ route('wishlist.index') }}"
                                    class="text-blue-600 font-bold hover:underline mt-2 inline-block"
                                    >Cari buku lagi?</a
                                >
                            </div>
                        @endforelse
                    </div>

                    <div
                        class="flex items-center justify-end gap-4 border-t pt-6"
                    >
                        <a
                            href="{{ route('katalog') }}"
                            class="text-gray-600 text-sm font-medium"
                            >Batal</a
                        >

                        {{-- TOMBOL UTAMA --}}
                        <button
                            type="submit"
                            x-bind:disabled="!canSubmit"
                            x-bind:class="
                                canSubmit
                                    ? 'bg-blue-600 hover:bg-blue-700 shadow-lg'
                                    : 'bg-gray-400 cursor-not-allowed opacity-50'
                            "
                            class="text-white px-10 py-3 rounded-lg font-bold transition-all duration-200 flex items-center gap-2"
                        >
                            <i class="bi bi-send-check"></i> Pinjam
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function itemPeminjaman(id) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const min = new Date(today);
            min.setDate(today.getDate() + 7);
            const max = new Date(today);
            max.setDate(today.getDate() + 31);

            return {
                tglKembali: '',
                minDate: min.toISOString().split('T')[0],
                maxDate: max.toISOString().split('T')[0],
                durationText: 'Belum dipilih',

                updateStatus() {
                    // Update durasi teks
                    if (this.tglKembali) {
                        const selected = new Date(this.tglKembali);
                        const diffTime = Math.abs(selected - today);
                        const diffDays = Math.ceil(
                            diffTime / (1000 * 60 * 60 * 24)
                        );
                        this.durationText = diffDays + ' Hari';

                        // Set status TRUE di form utama
                        this.$data.filledInputs[id] = true;
                    } else {
                        this.durationText = 'Belum dipilih';
                        this.$data.filledInputs[id] = false;
                    }
                },
            };
        }

        function removeBook(el, id) {
            Swal.fire({
                title: 'Hapus dari daftar?',
                text: 'Buku ini akan dihapus dari rencana peminjaman saat ini.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    // 1. Ambil element card buku
                    const item = el.closest('.book-item');

                    // 2. Beri efek transisi biar nggak kaku saat hilang
                    item.style.transition = 'all 0.3s ease';
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.9)';

                    setTimeout(() => {
                        // 3. Ambil data dari scope Alpine (untuk update validasi tombol)
                        // Menggunakan cara yang lebih aman untuk akses data Alpine
                        const alpineElement =
                            document.querySelector('[x-data]');
                        if (alpineElement && alpineElement.__x) {
                            const alpineData = alpineElement.__x.$data;
                            // Hapus key dari filledInputs agar tombol "Pinjam" update statusnya
                            delete alpineData.filledInputs[id];
                        }

                        // 4. Hapus element dari DOM
                        item.remove();
                    }, 300); // Tunggu animasi selesai
                }
            });
        }
    </script>
</x-app-layout>
