<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2
                class="font-semibold text-xl text-gray-800 leading-tight mb-0 text-capitalize"
            >
                {{ __('Riwayat Pengembalian') }}
            </h2>

            <div class="position-relative" style="width: 300px">
                <span
                    class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"
                    style="z-index: 5"
                >
                    <i class="bi bi-search"></i>
                </span>
                <input
                    type="text"
                    id="liveSearch"
                    class="form-control ps-5 pe-5 shadow-sm"
                    placeholder="Cari nama atau judul buku..."
                    autocomplete="off"
                    style="
                        border-radius: 8px;
                        border: 1px solid #ddd;
                    "
                />
                <button
                    id="clearSearch"
                    class="btn position-absolute top-50 end-0 translate-middle-y me-2"
                    style="display: none; border: none; background: transparent"
                >
                    <i class="bi bi-x-circle-fill text-muted"></i>
                </button>
            </div>
            {{-- filter denda  --}}
        </div>
    </x-slot>
    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr class="text-capitalize text-center">
                        <th>Peminjam</th>
                        <th>Buku</th>
                        <th>Tgl pinjam</th>
                        <th>Tgl Jatuh tempo</th>
                        <th>Tgl Kembali</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody id="historyTableBody">
                    @include ('admin.table_history_rows')
                </tbody>
            </table>
        </div>
    </div>
    <div class="mx-4">{{ $semuaPeminjaman->links() }}</div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            function fetchHistory(query) {
                $.ajax({
                    url: '{{ route('pengembalian.data') }}', // Pastikan nama route benar
                    type: 'GET',
                    data: { search: query },
                    success: function (data) {
                        $('#historyTableBody').html(data);
                    },
                });
            }

            $('#liveSearch').on('keyup', function () {
                let val = $(this).val();
                val.length > 0
                    ? $('#clearSearch').fadeIn(100)
                    : $('#clearSearch').fadeOut(100);
                fetchHistory(val);
            });

            $('#clearSearch').on('click', function () {
                $('#liveSearch').val('');
                $(this).hide();
                fetchHistory('');
                $('#liveSearch').focus();
            });
        });
    </script>
</x-app-layout>
