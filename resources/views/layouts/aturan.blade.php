<div x-data="{ openAturan: false }">
    <a
        href="javascript:void(0)"
        @click="openAturan = true"
        class="hover:text-indigo-600 transition-colors"
    >
        Aturan & Denda
    </a>

    <div x-show="openAturan" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div
            class="fixed inset-0 bg-black opacity-50"
            @click="openAturan = false"
        ></div>

        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div
                class="relative bg-white dark:bg-gray-800 w-full max-w-md rounded-xl shadow-2xl p-6 transition-all transform"
            >
                <div
                    class="flex justify-between items-center border-b pb-3 mb-4"
                >
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        <i class="bi bi-info-circle mr-2 text-indigo-600"></i>
                        Aturan & Denda
                    </h3>
                    <button
                        @click="openAturan = false"
                        class="text-gray-400 hover:text-gray-600 text-2xl"
                    >
                        &times;
                    </button>
                </div>

                <div class="text-sm text-gray-600 dark:text-gray-300 space-y-3">
                    <p><strong>1. Durasi Pinjam:</strong> Minimal 7 hari dan maksimal 30 hari / 1 Bulan.</p>
                    <p><strong>2. Denda Keterlambatan:</strong> Rp50.000 / hari per buku.</p>
                    <p><strong>3. Kerusakan / Kehilangan:</strong> Ketika ada kerusakan / Kehilangan maka petugas akan mendenda secara langsung di tempat sebesar Rp100.000.</p>
                    <p><strong>4. Kuota:</strong> Maksimal peminjaman adalah 6 buku sekaligus.</p>
                    <p><strong>5. Denda:</strong> Denda transparan.</p>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        @click="openAturan = false"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold text-sm transition-colors"
                    >
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
