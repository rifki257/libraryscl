<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Form Peminjaman Buku') }}
        </h2>
    </x-slot> --}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div
                    class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                >
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
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
                        <p class="text-sm text-blue-700">Stok Tersedia: <span class="font-extrabold">{{ $buku->jumlah }}</span></p>
                        <p class="text-sm text-red-700">Denda: <span>Rp150.000/Hari</span></p>
                    </div>
                </div>

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

                        <div class="mb-6">
                            <label
                                class="block text-sm font-semibold text-gray-700 mb-2"
                            >
                                Pilih Tanggal Jatuh Tempo (Pengembalian)
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
                            <p class="text-xs text-gray-500 mt-1 italic">* Dihitung 7 hari mulai dari besok</p>
                            <div
                                class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg"
                            >
                                <p class="text-sm text-green-800 flex items-center gap-2">
                                    <span>📅</span>
                                    <span
                                        >Durasi Pinjam:
                                        <strong x-text="durationText"></strong
                                    ></span>
                                </p>
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
        function peminjamanForm() {
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const min = new Date(today);
            min.setDate(today.getDate() + 7);

            const max = new Date(today);
            max.setMonth(today.getMonth() + 2);

            return {
                tglKembali: '',
                minDate: min.toISOString().split('T')[0],
                maxDate: max.toISOString().split('T')[0],
                isValid: false,
                errorMessage: '',
                durationText: '',

                calculateDuration() {
                    if (!this.tglKembali) return;

                    const selected = new Date(this.tglKembali);
                    selected.setHours(0, 0, 0, 0);

                    // Validasi Range
                    if (selected < min) {
                        this.errorMessage =
                            '❌ Minimal peminjaman adalah 1 minggu (7 hari).';
                        this.isValid = false;
                        this.durationText = '';
                        return;
                    }
                    if (selected > max) {
                        this.errorMessage =
                            '❌ Maksimal peminjaman adalah 2 bulan.';
                        this.isValid = false;
                        this.durationText = '';
                        return;
                    }

                    // Hitung Selisih
                    this.errorMessage = '';
                    this.isValid = true;

                    const diffTime = Math.abs(selected - today);
                    const diffDays = Math.ceil(
                        diffTime / (1000 * 60 * 60 * 24)
                    );

                    const months = Math.floor(diffDays / 30);
                    const remainingDays = diffDays % 30;

                    if (months > 0) {
                        this.durationText = `${months} Bulan ${remainingDays > 0 ? remainingDays + ' Hari' : ''} (${diffDays} Hari)`;
                    } else {
                        this.durationText = `${diffDays} Hari`;
                    }
                },
            };
        }
    </script>
</x-app-layout>
