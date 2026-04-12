<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-0">
                {{ __('konfir pinjam') }}
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
                    name="search"
                    class="form-control ps-5 pe-5"
                    placeholder="Cari peminjam atau buku..."
                    value="{{ request('search') }}"
                    style="border-radius: 8px; border: 1px solid #ddd"
                    autocomplete="off"
                />

                {{-- TOMBOL RESET (X) --}}
                <button
                    id="clearSearch"
                    class="btn position-absolute top-50 end-0 translate-middle-y me-2 text-muted"
                    style="{{ request('search') ? 'display: block;' : 'display: none;' }} border: none; background: transparent; z-index: 5;"
                    type="button"
                >
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </div>
            {{-- filter sisi paling kiri --}}
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr class="text-capitalize text-center">
                        <th>Peminjam</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl jatuh tempo</th>
                        <th>Persetujuan</th>
                    </tr>
                </thead>
                <tbody>
                    @include ('partials.konfir_pinjam')
                </tbody>
            </table>
            <div class="mt-4" id="pagination-wrapper">
                {{-- Cek apakah variabel ini punya fungsi links (berarti dia hasil paginate) --}}
                @if (method_exists($semuaPeminjaman, 'links'))
                    {{ $semuaPeminjaman->links() }}
                @endif
            </div>
        </div>
    </div>
    <tr>
        <script>
            function tolakPeminjaman(id, judulBuku) {
                Swal.fire({
                    title: 'Tolak Peminjaman?',
                    text: `Memberikan alasan penolakan untuk buku: ${judulBuku}`,
                    icon: 'warning',
                    input: 'textarea',
                    inputPlaceholder: 'Contoh: Maaf, stok buku saat ini sedang habis...',
                    inputValue: 'Maaf, stok habis.', // Alasan default
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Kirim & Tolak',
                    cancelButtonText: 'Batal',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Alasan harus diisi agar user tidak bingung!';
                        }
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Isi input hidden dengan alasan dari SweetAlert
                        document.getElementById(`alasan-${id}`).value = result.value;

                        // Tampilkan loading
                        Swal.fire({
                            title: 'Mengirim Penolakan...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });

                        // Submit form
                        document.getElementById(`form-tolak-${id}`).submit();
                    }
                });
            }

            $(document).ready(function () {
                // Fungsi utama AJAX
                function doSearch(query) {
                    $.ajax({
                        url: '{{ route('admin.persetujuan') }}',
                        type: 'GET',
                        data: { search: query },
                        beforeSend: function () {
                            $('tbody').css('opacity', '0.5');
                        },
                        success: function (data) {
                            $('tbody').html(data);
                            $('tbody').css('opacity', '1');
                            
                        },
                    });
                }

                // Jalankan search saat mengetik
                $('#liveSearch').on('keyup', function () {
                    let val = $(this).val();

                    // Tampilkan/Sembunyikan tombol X
                    if (val.length > 0) {
                        $('#clearSearch').show();
                    } else {
                        $('#clearSearch').hide();
                    }

                    doSearch(val);
                });

                // Jalankan reset saat tombol X diklik
                $('#clearSearch').on('click', function () {
                    $('#liveSearch').val(''); // Kosongkan input
                    $(this).hide(); // Sembunyikan tombol X
                    doSearch(''); // Ambil data awal (tanpa filter)
                    $('#liveSearch').focus();
                });
            });
        </script></x-app-layout
>
