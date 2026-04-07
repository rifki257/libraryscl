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

            {{-- Info Slot --}}
            <div
                class="p-3 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 mb-4 shadow-sm rounded-r-lg"
            >
                <p class="text-sm">
                    <i class="bi bi-info-circle-fill"></i>
                    Batas maksimal pinjam: <strong>6 buku</strong>.
                    <span
                        class="ml-2 px-2 py-0.5 bg-yellow-200 rounded-full font-bold"
                    >
                        Sisa slot kamu: {{ 6 - ($totalBukuAktif ?? 0) }} buku
                    </span>
                </p>
            </div>

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

                    <div class="space-y-4 mb-8" id="book-container">
                        @forelse ($bukuTerpilih as $index => $buku)
                            <div
                                x-data="itemPeminjaman('{{ $index }}')"
                                class="p-5 bg-gray-50 rounded-xl border border-gray-200 shadow-sm book-item relative"
                            >
                                <div class="flex flex-col md:flex-row gap-6">
                                    <div class="flex gap-4 flex-grow">
                                        <input
                                            type="hidden"
                                            name="id_buku[]"
                                            value="{{ $buku->id_buku }}"
                                        />
                                        <img
                                            src="{{ asset('storage/' . $buku->gambar) }}"
                                            class="w-20 h-28 object-cover rounded-lg shadow-md flex-shrink-0"
                                        />
                                        <div class="flex-grow">
                                            <h3
                                                class="text-lg font-bold text-blue-900 capitalize leading-tight mb-1"
                                            >
                                                {{ $buku->judul }}
                                            </h3>
                                            <p class="text-xs text-blue-700">Stok Tersedia: <span class="font-bold">{{ $buku->jumlah }}</span></p>
                                            <p class="text-xs text-red-600 italic">Denda: Rp50.000/Hari keterlambatan</p>
                                        </div>
                                    </div>

                                    <div
                                        class="flex flex-col justify-between items-end min-w-[200px]"
                                    >
                                        <button
                                            type="button"
                                            @click="removeBook($el, '{{ $index }}')"
                                            class="text-red-600 hover:text-red-800 flex items-center gap-1"
                                        >
                                            <span
                                                class="text-[11px] font-bold uppercase"
                                                >Hapus</span
                                            >
                                            <i class="bi bi-trash text-lg"></i>
                                        </button>

                                        <div
                                            class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm w-full"
                                        >
                                            <label
                                                class="block text-[10px] font-bold text-gray-400 uppercase mb-1"
                                                >Tanggal Kembali</label
                                            >
                                            <input
                                                type="date"
                                                name="tgl_kembali[]"
                                                x-model="tglKembali"
                                                @input="updateStatus()"
                                                :min="minDate"
                                                :max="maxDate"
                                                class="w-full text-xs border-gray-300 rounded focus:ring-blue-500 p-1"
                                                required
                                            />
                                            <div
                                                class="mt-1 flex justify-between items-center"
                                            >
                                                <span
                                                    class="text-[9px] text-gray-400 uppercase"
                                                    >Durasi:</span
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
                            href="{{ route('wishlist.index') }}"
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
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
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
            if (confirm('Hapus buku ini?')) {
                const item = el.closest('.book-item');

                // Ambil data dari scope Alpine
                const alpineData = document.querySelector('[x-data]').__x.$data;

                // Hapus key dari filledInputs agar tidak dihitung lagi
                delete alpineData.filledInputs[id];

                item.remove();

                if (document.querySelectorAll('.book-item').length === 0) {
                    window.location.href = '{{ route("wishlist.index") }}';
                }
            }
        }
    </script>
</x-app-layout>
