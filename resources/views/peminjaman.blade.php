<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alert Error --}}
            @if (session('error'))
                <div
                    class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                >
                    {{ session('error') }}
                </div>
            @endif

            {{-- Info Slot Sederhana --}}
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

                    {{-- LOOPING BUKU --}}
                    <div class="space-y-4 mb-8" id="book-container">
                        @forelse ($books as $buku)
                            <div
                                class="flex items-center gap-4 p-4 bg-blue-50 rounded-lg border border-blue-100 book-item"
                            >
                                {{-- ID Buku untuk dikirim ke Controller --}}
                                <input
                                    type="hidden"
                                    name="id_buku[]"
                                    value="{{ $buku->id_buku }}"
                                />

                                <img
                                    src="{{ asset('storage/' . $buku->gambar) }}"
                                    class="w-20 h-28 object-cover rounded shadow-sm"
                                />

                                <div class="flex-grow">
                                    <h3
                                        class="text-md font-bold text-blue-900 capitalize"
                                    >
                                        {{ $buku->judul }}
                                    </h3>
                                    <p class="text-xs text-blue-700">Stok Tersedia: <span class="font-bold">{{ $buku->jumlah }}</span></p>
                                    <p class="text-xs text-red-600 italic">Denda: Rp50.000/Hari keterlambatan</p>
                                </div>

                                <div class="flex flex-col items-end gap-3">
                                    <button
                                        type="button"
                                        onclick="removeBook(this)"
                                        class="text-red-600 hover:text-gray-400 transition-colors duration-200 flex items-center gap-1 group"
                                    >
                                        <span
                                            class="text-[11px] font-bold me-2 uppercase tracking-wider"
                                            ><i class="bi bi-trash"></i
                                            >Hapus</span
                                        >
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-10">Tidak ada buku yang dipilih.</p>
                        @endforelse
                    </div>

                    {{-- Bagian Tanggal & Tombol Submit (Alpine.js) --}}
                    <div
                        x-data="peminjamanForm()"
                        class="bg-gray-50 p-6 rounded-xl border border-gray-200"
                    >
                        <div class="mb-6">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Tanggal Kembali (Berlaku untuk semua buku)
                            </label>

                            <input
                                type="date"
                                name="tgl_kembali"
                                x-model="tglKembali"
                                @input="calculateDuration()"
                                :min="minDate"
                                :max="maxDate"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                required
                            />

                            <div class="mt-4">
                                <div
                                    class="p-3 bg-green-50 border border-green-200 rounded-lg inline-block"
                                >
                                    <p class="text-sm text-green-800 m-0">📅 Durasi Pinjam: <strong x-text="durationText"></strong></p>
                                </div>
                            </div>

                            <template x-if="errorMessage">
                                <p
                                    class="mt-2 text-sm text-red-600 font-medium"
                                    x-text="errorMessage"
                                ></p>
                            </template>
                        </div>

                        <div
                            class="flex items-center justify-end gap-4 border-t pt-6"
                        >
                            <a
                                href="{{ route('dashboard') }}"
                                class="text-gray-600 hover:text-gray-800 text-sm font-medium"
                                >Batal</a
                            >

                            <button
                                type="submit"
                                :disabled="!isValid"
                                :class="!isValid
                                    ? 'opacity-50 cursor-not-allowed bg-gray-400'
                                    : 'bg-blue-600 hover:bg-blue-700'"
                                class="text-white px-8 py-3 rounded-lg font-bold transition shadow-md"
                            >
                                Pinjam
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Logic Alpine.js
        function peminjamanForm() {
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Set minimal 7 hari dari sekarang (1 Minggu)
            const min = new Date(today);
            min.setDate(today.getDate() + 7);

            // Set maksimal 31 hari dari sekarang (1 Bulan)
            const max = new Date(today);
            max.setDate(today.getDate() + 31);

            return {
                tglKembali: '',
                minDate: min.toISOString().split('T')[0],
                maxDate: max.toISOString().split('T')[0],
                isValid: false,
                errorMessage: '',
                durationText: 'Belum dipilih',

                calculateDuration() {
                    if (!this.tglKembali) {
                        this.isValid = false;
                        return;
                    }

                    const selected = new Date(this.tglKembali);
                    selected.setHours(0, 0, 0, 0);

                    // Validasi manual jika user memaksa input lewat ketik keyboard
                    if (selected < min) {
                        this.errorMessage = '❌ Minimal peminjaman adalah 7 hari.';
                        this.isValid = false;
                        this.durationText = '-';
                        return;
                    }

                    if (selected > max) {
                        this.errorMessage =
                            '❌ Maksimal peminjaman adalah 1 bulan (31 hari).';
                        this.isValid = false;
                        this.durationText = '-';
                        return;
                    }

                    this.errorMessage = '';
                    this.isValid = true;

                    const diffTime = Math.abs(selected - today);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    this.durationText = diffDays + ' Hari';
                },
            };
        }

        // Fungsi Hapus Baris Buku
        function removeBook(button) {
            const bookItem = button.closest('.book-item');

            if (confirm('Hapus buku ini dari daftar konfirmasi?')) {
                bookItem.style.opacity = '0';
                bookItem.style.transition = '0.3s ease';

                setTimeout(() => {
                    bookItem.remove();

                    // Jika buku habis, balik ke dashboard
                    const remainingBooks = document.querySelectorAll('.book-item');
                    if (remainingBooks.length === 0) {
                        alert('Daftar buku kosong.');
                        window.location.href = '{{ route('dashboard') }}';
                    }
                }, 300);
            }
        }
    </script>
</x-app-layout>
