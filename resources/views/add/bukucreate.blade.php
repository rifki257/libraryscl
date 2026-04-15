<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
</head>
<x-app-layout>
    @if (session('success'))
        <div class="bg-green-500 text-white p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <x-slot name="header">
        <div class="d-flex align-items-center gap-2">
            <a
                href="{{ route('kategori.buku') }}"
                class="btn btn-success d-flex align-items-center gap-1"
            >
                <i class="bi bi-list-ul"></i> Kategori
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form
                            action="{{ route('buku.store') }}"
                            method="POST"
                            enctype="multipart/form-data"
                        >
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Gambar</label>
                                        <input
                                            type="file"
                                            name="gambar"
                                            class="form-control"
                                        />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Judul</label>
                                        <input
                                            type="text"
                                            name="judul"
                                            class="form-control"
                                            placeholder="Judul Buku"
                                            required
                                        />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"
                                            >Penulis</label
                                        >
                                        <input
                                            type="text"
                                            name="penulis"
                                            class="form-control"
                                            placeholder="Nama Penulis"
                                            required
                                        />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            >Penerbit</label
                                        >
                                        <input
                                            type="text"
                                            name="penerbit"
                                            class="form-control"
                                            placeholder="Penerbit Buku"
                                            required
                                        />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Jumlah</label>
                                        <input
                                            type="number"
                                            name="jumlah"
                                            class="form-control"
                                            placeholder="Jumlah Buku"
                                            min="1"
                                            oninput="
                                                this.value =
                                                    !!this.value &&
                                                    Math.abs(this.value) >= 0
                                                        ? Math.abs(this.value)
                                                        : null
                                            "
                                            required
                                        />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"
                                            >Kategori</label
                                        >
                                        <select
                                            name="id_kategori"
                                            class="form-select"
                                            required
                                        >
                                            <option value="" selected disabled>
                                                -- Pilih Kategori --
                                            </option>
                                            @foreach ($kategoris as $item)
                                                <option
                                                    value="{{ $item->id_kategori }}"
                                                >
                                                    {{ $item->nama_kategori }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button
                                class="btn btn-success mt-3"
                                id="simpan"
                                style="width: 80px"
                            >
                                <i class="fa-solid fa-floppy-disk"></i>
                            </button>
                            <a
                                href="{{ route('buku') }}"
                                class="btn btn-secondary mt-3"
                                id="kembali"
                                style="width: 80px"
                                ><i class="fa-solid fa-arrow-left me-1"></i
                            ></a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
