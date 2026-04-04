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
                @if (($totalBukuAktif ?? 0) > 0)
                    <p class="text-xs mt-1 text-yellow-600 italic">*Kamu sedang memiliki {{ $totalBukuAktif }} buku dalam status pending/dipinjam.</p>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-xl font-bold mb-6 text-gray-800 border-b pb-4">
                    Konfirmasi Peminjaman Masal
                </h2>

                <form
                    action="{{ route('peminjaman.store.masal') }}"
                    method="POST"
                    id="main-form"
                >
                    @csrf

                    {{-- LOOPING BUKU YANG DIPILIH --}}
                    <div class="space-y-4 mb-8">
                        @forelse ($books as $buku)
                            <div
                                class="flex items-center gap-4 p-4 bg-blue-50 rounded-lg border border-blue-100"
                            >
                                {{-- Input Hidden ID Buku untuk dikirim ke Controller --}}
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
                                    <h3 class="text-md font-bold text-blue-900">
                                        {{ $buku->judul }}
                                    </h3>
                                    <p class="text-xs text-blue-700">Stok Tersedia: <span class="font-bold">{{ $buku->jumlah }}</span></p>
                                    <p class="text-xs text-red-600 italic">Denda: Rp50.000/Hari</p>
                                </div>

                                {{-- Input Jumlah per Buku --}}
                                <div class="flex flex-col items-end gap-3">
                                    {{-- flex-col membuat konten tersusun ke bawah --}}
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-xs font-medium text-gray-600"
                                            >Jumlah:</span
                                        >
                                        <input
                                            type="number"
                                            name="total_pinjam[]"
                                            value="1"
                                            min="1"
                                            max="{{ $buku->jumlah }}"
                                            class="qty-input w-20 p-2 text-sm border-blue-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 font-bold text-center"
                                            onchange="validateTotalSlot()"
                                        />
                                    </div>

                                    {{-- Tombol Hapus sekarang berada di bawah input jumlah --}}
                                    <button
                                        type="button"
                                        onclick="removeBook(this)"
                                        class="text-xs text-red-500 hover:text-red-700 font-medium flex items-center gap-1 transition p-1 hover:bg-red-50 rounded me-3"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500">Tidak ada buku yang dipilih.</p>
                        @endforelse
                    </div>

                    {{-- Form Tanggal & Durasi (Alpine.js) --}}
                    <div
                        x-data="peminjamanForm()"
                        class="bg-gray-50 p-6 rounded-xl border border-gray-200"
                    >
                        <div class="mb-6">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                                >Pilih Tanggal Jatuh Tempo (Berlaku untuk semua
                                buku)</label
                            >
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

                            <div class="mt-4 flex flex-col md:flex-row gap-4">
                                <div
                                    class="flex-1 p-3 bg-green-50 border border-green-200 rounded-lg"
                                >
                                    <p class="text-sm text-green-800">📅 Durasi Pinjam: <strong x-text="durationText"></strong></p>
                                </div>
                                <div
                                    id="slot-warning"
                                    class="hidden flex-1 p-3 bg-red-50 border border-red-200 rounded-lg"
                                >
                                    <p class="text-sm text-red-800 font-bold">⚠️ Total melebihi sisa jatah buku kamu!</p>
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
                                id="btn-submit"
                                :disabled="!isValid"
                                :class="!isValid
                                    ? 'opacity-50 cursor-not-allowed bg-gray-400'
                                    : 'bg-blue-600 hover:bg-blue-700'"
                                class="text-white px-8 py-3 rounded-lg font-bold transition shadow-md"
                            >
                                Ajukan Peminjaman
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Inisialisasi Data dari Laravel (Satu kali ambil saat load)
        const sisaSlotUser = {{ 6 - ($totalBukuAktif ?? 0) }};

        // Fungsi Validasi Total Slot (Cek apakah total angka di semua input <= sisa jatah)
        function validateTotalSlot() {
            const inputs = document.querySelectorAll('.qty-input');
            const warning = document.getElementById('slot-warning');
            const btn = document.getElementById('btn-submit');

            let totalInput = 0;
            inputs.forEach((input) => {
                totalInput += parseInt(input.value) || 0;
            });

            if (totalInput > sisaSlotUser) {
                warning.classList.remove('hidden');
                btn.disabled = true;
                btn.classList.add('opacity-50', 'bg-gray-400');
                btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            } else {
                warning.classList.add('hidden');
                // Tombol akan kembali aktif tergantung validasi tanggal di AlpineJS
            }
        }

        // Logika Alpine.js untuk Tanggal
        function peminjamanForm() {
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const min = new Date(today);
            min.setDate(today.getDate() + 7); // Minimal pinjam 7 hari

            const max = new Date(today);
            max.setMonth(today.getMonth() + 2); // Maksimal 2 bulan

            return {
                tglKembali: '',
                minDate: min.toISOString().split('T')[0],
                maxDate: max.toISOString().split('T')[0],
                isValid: false,
                errorMessage: '',
                durationText: 'Belum dipilih',

                calculateDuration() {
                    if (!this.tglKembali) return;

                    const selected = new Date(this.tglKembali);
                    selected.setHours(0, 0, 0, 0);

                    if (selected < min) {
                        this.errorMessage = '❌ Minimal peminjaman adalah 7 hari.';
                        this.isValid = false;
                        this.durationText = '-';
                        return;
                    }

                    this.errorMessage = '';
                    this.isValid = true;

                    const diffTime = Math.abs(selected - today);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    this.durationText = diffDays + ' Hari';

                    // Trigger validasi jumlah buku setiap kali tanggal berubah
                    validateTotalSlot();
                },
            };
        }
        function removeBook(button) {
            // 1. Cari elemen pembungkus kartu buku (div dengan class bg-blue-50)
            // Gunakan .closest() untuk mencari parent terdekat dengan class tertentu
            const bookItem = button.closest('.flex.items-center.gap-4.p-4.bg-blue-50');

            // 2. Tampilkan konfirmasi sederhana agar tidak sengaja terhapus
            if (confirm('Apakah Anda yakin ingin menghapus buku ini dari daftar?')) {
                // Tambahkan efek animasi fade out (opsional)
                bookItem.style.opacity = '0';
                bookItem.style.transition = '0.3s ease';

                setTimeout(() => {
                    // 3. Hapus elemen dari DOM
                    bookItem.remove();

                    // 4. Jalankan kembali validasi slot agar angka "Sisa Slot" terupdate
                    validateTotalSlot();

                    // 5. Jika semua buku habis dihapus, arahkan kembali ke dashboard/wishlist
                    const remainingBooks = document.querySelectorAll('.qty-input');
                    if (remainingBooks.length === 0) {
                        alert('Daftar buku kosong, kembali ke halaman utama.');
                        window.location.href = '{{ route('dashboard') }}';
                    }
                }, 300);
            }
        }
    </script>
</x-app-layout>
