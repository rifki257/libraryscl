<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('Pengembalian Buku') }}
            </h2>

            <div class="position-relative shadow-sm" style="width: 300px">
                <span
                    class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"
                    style="z-index: 5"
                >
                    <i class="bi bi-search"></i>
                </span>
                <input
                    type="text"
                    id="liveSearch"
                    class="form-control ps-5 pe-5"
                    placeholder="Cari peminjam atau buku..."
                    autocomplete="off"
                    style="border-radius: 8px; border: 1px solid #ddd"
                />
                <button
                    id="clearSearch"
                    class="btn position-absolute top-50 end-0 translate-middle-y me-2"
                    style="display: none; border: none; background: transparent"
                >
                    <i class="bi bi-x-circle-fill text-muted"></i>
                </button>
            </div>
        </div>
    </x-slot>
    {{-- Pindahkan x-data ke level teratas agar mencakup tabel DAN modal --}}
    <div class="py-3" x-data="{ openModal: false, selectedItem: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr class="text-capitalize text-center">
                        <th>Peminjam</th>
                        <th>Buku</th>
                        <th>Tgl pinjam</th>
                        <th>Jatuh tempo</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="pengembalianTableBody">
                    @include ('admin.table_pengembalian_rows')
                </tbody>
            </table>
            <div class="mt-4">{{ $semuaPeminjaman->links() }}</div>
        </div>

        {{-- MODAL BOX - Pastikan berada di dalam div x-data --}}
        <div
            x-show="openModal"
            class="fixed inset-0 z-[999] overflow-y-auto"
            x-transition.opacity
            x-cloak
        >
            <div class="flex items-center justify-center min-h-screen p-4">
                <div
                    class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
                    @click="openModal = false"
                ></div>

                <div
                    class="relative bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden transition-all"
                >
                    <div class="p-6">
                        <h3
                            class="text-lg font-bold text-gray-900 mb-4 text-center"
                        >
                            Konfirmasi Pengembalian
                        </h3>

                        <div class="space-y-4">
                            <div class="flex justify-between text-sm italic">
                                <span class="text-gray-500">Peminjam:</span>
                                <span
                                    class="font-semibold text-gray-800"
                                    x-text="selectedItem.name"
                                ></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Buku:</span>
                                <span
                                    class="font-semibold text-gray-800 text-right ml-4"
                                    x-text="selectedItem.judul"
                                ></span>
                            </div>

                            <template x-if="selectedItem.totalHari > 0">
                                <div
                                    class="mt-6 p-4 bg-red-50 rounded-xl border border-red-100 text-center"
                                >
                                    <p class="text-xs text-red-600 font-bold uppercase tracking-wider">Terlambat <span x-text="selectedItem.totalHari"></span> Hari</p>
                                    <p class="text-2xl font-black text-red-700 mt-1">Rp <span x-text="selectedItem.totalDenda"></span></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 flex gap-3">
                        <button
                            @click="openModal = false"
                            class="flex-1 px-4  text-sm font-bold text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                        >
                            BATAL
                        </button>
                        <form
                            :action="'/admin/konfirmasi-kembali/' +
                            selectedItem.id"
                            method="POST"
                            class="flex-1"
                        >
                            @csrf
                            @method ('PUT')
                            <button
                                type="submit"
                                class="w-full px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-md shadow-indigo-200 transition"
                            >
                                KONFIRMASI
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            function doSearch(query) {
                $.ajax({
                    url: '{{ route('pengembalian') }}', // Ganti dengan nama route Anda
                    type: 'GET',
                    data: { search: query },
                    success: function (data) {
                        $('#pengembalianTableBody').html(data);
                    },
                });
            }

            $('#liveSearch').on('keyup', function () {
                let val = $(this).val();
                val.length > 0 ? $('#clearSearch').show() : $('#clearSearch').hide();
                doSearch(val);
            });

            $('#clearSearch').on('click', function () {
                $('#liveSearch').val('');
                $(this).hide();
                doSearch('');
                $('#liveSearch').focus();
            });
        });
    </script>
</x-app-layout>
