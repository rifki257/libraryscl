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
            {{-- Naikkan Z-index --}}
            x-transition
            x-cloak
        >
            <div
                class="flex items-center justify-center min-h-screen px-4 pb-20 text-center"
            >
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="openModal = false"
                ></div>

                <div
                    class="inline-block bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full overflow-hidden text-left align-middle"
                >
                    <div
                        class="px-6 py-4 bg-gray-50 border-b border-gray-200 font-bold text-gray-800"
                    >
                        Detail Konfirmasi Pengembalian
                    </div>

                    <div class="px-6 py-4 space-y-3">
                        <div class="flex justify-between border-b pb-2">
                            <span
                                class="text-gray-500 text-xs font-bold uppercase"
                                >Nama Peminjam</span
                            >
                            <span
                                class="text-gray-900 text-sm font-semibold"
                                x-text="selectedItem.name"
                            ></span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span
                                class="text-gray-500 text-xs font-bold uppercase"
                                >Judul Buku</span
                            >
                            <span
                                class="text-gray-900 text-sm font-semibold"
                                x-text="selectedItem.judul"
                            ></span>
                        </div>

                        <div
                            x-show="selectedItem.totalHari > 0"
                            class="p-3 bg-red-50 rounded-md border border-red-100 mt-4"
                        >
                            <div class="flex justify-between">
                                <span
                                    class="text-red-700 text-xs font-bold uppercase"
                                    >Total Keterlambatan</span
                                >
                                <span class="text-red-700 text-sm font-bold"
                                    ><span
                                        x-text="selectedItem.totalHari"
                                    ></span>
                                    Hari</span
                                >
                            </div>
                            <div class="flex justify-between mt-1">
                                <span
                                    class="text-red-700 text-xs font-bold uppercase"
                                    >Total Denda</span
                                >
                                <span
                                    class="text-red-700 text-lg font-black italic"
                                    >Rp
                                    <span
                                        x-text="selectedItem.totalDenda"
                                    ></span
                                ></span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 flex gap-2">
                        <button
                            type="button"
                            @click="openModal = false"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md font-bold text-xs uppercase"
                        >
                            Batal
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
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-bold text-xs uppercase transition shadow-md"
                            >
                                Konfirmasi Selesai
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
