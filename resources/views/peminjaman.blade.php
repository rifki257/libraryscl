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
            <div
                class="p-3 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 mb-4 shadow-sm rounded-r-lg"
            >
                <p class="text-sm">
                    <i class="bi bi-info-circle-fill"></i>
                    Batas maksimal pinjam: <strong>6 buku</strong>.
                    <span
                        class="ml-2 px-2 py-0.5 bg-yellow-200 rounded-full font-bold"
                    >
                        Sisa slot kamu: {{ 6 - $totalBukuAktif }} buku
                    </span>
                </p>
                {{-- Tambahkan info detail jika slot hampir habis --}}
                @if ($totalBukuAktif > 0)
                    <p class="text-xs mt-1 text-yellow-600 italic">*Kamu sedang memiliki {{ $totalBukuAktif }} buku (pending/dipinjam).</p>
                @endif
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                {{-- Detail Buku --}}
                <div
                    class="flex items-center gap-4 mb-8 p-4 bg-blue-50 rounded-lg border border-blue-100"
                >
                    <img
                        src="{{ asset('storage/' . $buku->gambar) }}"
                        class="w-30 h-40 object-cover rounded shadow-md"
                    />
                    <div>
                        <h3 class="text-lg font-bold text-blue-900">
                            {{ $buku->judul }}
                        </h3>
                        <p class="text-sm text-blue-700">Stok Perpustakaan: <span class="font-extrabold">{{ $buku->jumlah }}</span></p>
                        <p class="text-sm text-red-700">Denda: <span>Rp50.000/Hari</span></p>

                        {{-- Selector Jumlah Pinjam --}}
                        <div class="flex items-center gap-3 mt-3">
                            <p class="text-sm font-medium text-gray-700">Jumlah Pinjam:</p>
                            <div
                                class="flex items-center border border-blue-200 rounded-lg bg-white"
                            >
                                <button
                                    type="button"
                                    onclick="decrementQty()"
                                    class="px-3 py-1 text-blue-600 hover:bg-blue-50 font-bold"
                                >
                                    -
                                </button>

                                <input
                                    type="number"
                                    id="total_pinjam_display"
                                    class="w-12 text-center border-none focus:ring-0 text-sm font-bold"
                                    value="1"
                                    readonly
                                />

                                <button
                                    type="button"
                                    onclick="incrementQty()"
                                    class="px-3 py-1 text-blue-600 hover:bg-blue-50 font-bold"
                                >
                                    +
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Peminjaman --}}
                <div x-data="peminjamanForm()">
                    <form
                        action="{{ route('peminjaman.store') }}"
                        method="POST"
                    >
                        @csrf
                        <input
                            type="hidden"
                            name="id_buku"
                            value="{{ $buku->id_buku }}"
                        />

                        {{-- Input Hidden untuk Jumlah (dikirim ke controller) --}}
                        <input
                            type="hidden"
                            name="total_pinjam"
                            id="total_pinjam_hidden"
                            value="1"
                        />

                        <div class="mb-6">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                                >Pilih Tanggal Jatuh Tempo</label
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

                            <div
                                class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg"
                            >
                                <p class="text-sm text-green-800">📅 Durasi Pinjam: <strong x-text="durationText"></strong></p>
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
                                href="{{ route('katalog') }}"
                                class="text-gray-600 hover:text-gray-800 text-sm font-medium"
                                >Batal</a
                            >
                            <button
                                type="submit"
                                :disabled="!isValid"
                                :class="!isValid
                                    ? 'opacity-50 cursor-not-allowed bg-gray-400'
                                    : 'bg-blue-600 hover:bg-blue-700'"
                                class="text-white px-6 py-2 rounded-lg font-bold transition shadow-md"
                            >
                                Pinjam
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 1. Inisialisasi Data dari Laravel (Controller)
        // Mengambil data stok buku dan jatah user yang sudah dihitung di backend
        const stokBuku = {{ $buku->jumlah }};
        const limitMaks = 6;
        const totalAktif = {{ $totalBukuAktif }}; // Mengambil total buku (pending + dipinjam)
        const sisaSlot = limitMaks - totalAktif;

        // Element Selector
        const displayQty = document.getElementById('total_pinjam_display');
        const hiddenQty = document.getElementById('total_pinjam_hidden');

        // 2. Logika Tombol Quantity (Plus/Minus)
        function incrementQty() {
            let current = parseInt(displayQty.value);

            // Cek Jatah User: Jika user sudah punya 4 buku, maka sisaSlot adalah 2.
            // Tombol + akan berhenti jika angka mencapai 2.
            if (current >= sisaSlot) {
                alert(
                    'Batas pinjam kamu sisa ' +
                        sisaSlot +
                        ' slot lagi (termasuk yang sedang diajukan atau dipinjam).'
                );
                return;
            }

            // Cek Stok Perpustakaan: Jangan sampai pinjam melebihi buku yang ada di rak.
            if (current >= stokBuku) {
                alert('Maaf, stok buku di perpustakaan hanya tersisa ' + stokBuku);
                return;
            }

            // Update nilai jika validasi lolos
            displayQty.value = current + 1;
            hiddenQty.value = displayQty.value;
        }

        function decrementQty() {
            let current = parseInt(displayQty.value);
            if (current > 1) {
                displayQty.value = current - 1;
                hiddenQty.value = displayQty.value;
            }
        }

        // 3. Logika Alpine.js untuk Form & Tanggal
        function peminjamanForm() {
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            // Aturan: Minimal pinjam 7 hari dari sekarang
            const min = new Date(today);
            min.setDate(today.getDate() + 7);

            // Aturan: Maksimal pinjam 2 bulan ke depan
            const max = new Date(today);
            max.setMonth(today.getMonth() + 2);

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

                    // Hitung selisih hari
                    const diffTime = Math.abs(selected - today);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    this.durationText = diffDays + ' Hari';
                },
            };
        }
    </script>
</x-app-layout>
