<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center w-100">
            {{-- SISI KIRI: Tombol Kategori --}}
            @if (auth()->user()->role === 'petugas')
                <div class="d-flex align-items-center">
                    <a
                        href="{{ route('kategori.buku') }}"
                        class="btn btn-success d-flex align-items-center gap-1 shadow-sm"
                    >
                        <i class="bi bi-list-ul"></i> Kategori
                    </a>
                </div>
            @endif
            {{-- SISI KANAN: Filter dan Tambah Buku (Sejajar) --}}
            <div
                class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end"
            >
                <div class="position-relative shadow-sm" style="width: 300px">
                    {{-- Ikon Search di Kiri --}}
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
                        {{-- ps-5 untuk ruang ikon kiri, pe-5 untuk ruang tombol X --}}
                        placeholder="Cari judul atau penulis..."
                        value="{{ request('search') }}"
                        style="border-radius: 8px; border: 1px solid #ddd"
                        autocomplete="off"
                    />

                    {{-- Tombol X (Reset) di Kanan --}}
                    <button
                        id="clearSearch"
                        class="btn position-absolute top-50 end-0 translate-middle-y me-2 text-muted"
                        style="
            display: {{ request('search') ? 'block' : 'none' }}; 
            border: none; 
            background: transparent;
            z-index: 5;
        "
                        type="button"
                    >
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                </div>
                {{-- Dropdown Filter --}}
                <div class="dropdown d-flex align-items-center">
                    <button
                        class="btn btn-primary dropdown-toggle px-4 shadow-sm"
                        type="button"
                        id="dropdownMenuButton"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <i class="fa-solid fa-filter me-2"></i>
                        @if (request('filter_kategori'))
                            @foreach ($kategoris as $kat)
                                @if (request('filter_kategori') == $kat->id_kategori)
                                    {{ $kat->nama_kategori }}
                                @endif
                            @endforeach
                        @else
                            Filter
                        @endif
                    </button>

                    <ul
                        class="dropdown-menu shadow border-0 p-0"
                        aria-labelledby="dropdownMenuButton"
                        style="min-width: 250px"
                    >
                        {{-- Input Search --}}
                        <li class="p-2 sticky-top bg-white border-bottom">
                            <input
                                type="text"
                                id="searchKategori"
                                class="form-control form-control-sm"
                                placeholder="Cari kategori..."
                            />
                        </li>

                        {{-- Daftar Kategori dengan Scroll --}}
                        <div
                            id="listKategori"
                            style="max-height: 250px; overflow-y: auto"
                        >
                            <li>
                                <a
                                    class="dropdown-item {{ !request('filter_kategori') ? 'active' : '' }}"
                                    href="{{ route('buku') }}"
                                >
                                    Semua Kategori
                                </a>
                            </li>
                            <li><hr class="dropdown-divider m-0" /></li>

                            @foreach ($kategoris as $kat)
                                <li class="kategori-item">
                                    <a
                                        class="dropdown-item {{ request('filter_kategori') == $kat->id_kategori ? 'active' : '' }}"
                                        href="{{ route('buku', ['filter_kategori' => $kat->id_kategori]) }}"
                                    >
                                        {{ $kat->nama_kategori }}
                                    </a>
                                </li>
                            @endforeach
                        </div>
                    </ul>

                    {{-- Tombol Reset --}}
                    @if (request('filter_kategori'))
                        <a
                            href="{{ route('buku') }}"
                            class="btn btn-link text-decoration-none text-muted btn-sm ms-1"
                        >
                            <i class="fa-solid fa-xmark"></i> Reset
                        </a>
                    @endif
                </div>

                {{-- Tombol Tambah Buku --}}
                @if (auth()->user()->role === 'petugas')
                    <a
                        href="{{ route('buku.create') }}"
                        class="btn btn-success d-flex align-items-center gap-1 shadow-sm"
                    >
                        <i class="bi bi-plus-lg"></i> Buku
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="container-buku">
                @include ('partials.tabel_isi')
            </div>
        </div>
    </div>
    {{-- Script Live Search --}}
    <script>
        $(document).ready(function () {
            // Logic Search Kategori (tetap sama)
            $('#searchKategori').on('keyup', function () {
                let filter = $(this).val().toLowerCase();
                $('.kategori-item').each(function () {
                    let text = $(this).text().toLowerCase();
                    $(this).toggle(text.includes(filter));
                });
            });

            // LOGIK LIVE SEARCH BUKU
            $('#liveSearch').on('keyup', function () {
                let search = $(this).val();
                let kategori = '{{ request('filter_kategori') }}';

                $.ajax({
                    url: '{{ route("buku") }}',
                    type: 'GET',
                    data: {
                        search: search,
                        filter_kategori: kategori,
                    },
                    beforeSend: function () {
                        // Opsional: beri efek transparan saat loading
                        $('#container-buku').css('opacity', '0.5');
                    },
                    success: function (data) {
                        $('#container-buku').html(data);
                        $('#container-buku').css('opacity', '1');
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                    },
                });
            });
        });
    </script>
</x-app-layout>
