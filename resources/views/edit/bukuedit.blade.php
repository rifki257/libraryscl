<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
</head>

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Buku') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container">
                        <form
                            action="{{ route('buku.update', $buku->id_buku) }}"
                            method="POST"
                            enctype="multipart/form-data"
                        >
                            @csrf
                            @method ('PUT')
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        @if ($buku->gambar)
                                            <img
                                                src="{{ asset('storage/' . $buku->gambar) }}"
                                                alt="cover"
                                                class="rounded shadow-sm mb-2 mx-auto"
                                                style="
                                                    width: 150px;
                                                    height: auto;
                                                "
                                            />
                                        @else
                                            <p class="text-muted italic">Tidak ada gambar sebelumnya</p>
                                        @endif
                                        <input
                                            type="file"
                                            name="gambar"
                                            class="form-control"
                                        />
                                        <small
                                            class="text-secondary text-sm italic"
                                            >*Biarkan kosong jika tidak ingin
                                            mengganti gambar</small
                                        >
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label">Judul</label>
                                        <input
                                            type="text"
                                            name="judul"
                                            class="form-control"
                                            value="{{ old('judul', $buku->judul) }}"
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
                                            value="{{ old('penulis', $buku->penulis) }}"
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
                                            value="{{ old('penerbit', $buku->penerbit) }}"
                                            required
                                        />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Jumlah</label>
                                        <input
                                            type="number"
                                            name="jumlah"
                                            class="form-control"
                                            value="{{ old('jumlah', $buku->jumlah) }}"
                                            min="1"
                                            oninput="
                                                this.value = this.value.replace(
                                                    /^0+/,
                                                    ''
                                                )
                                            "
                                            required
                                        />
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success mt-3" id="simpan" style="width: 80px">
                                <i class="fa-solid fa-floppy-disk"></i>
                            </button>
                            <a
                                href="{{ route('buku') }}"
                                class="btn btn-secondary mt-3"
                                id="kemnbali"
                                style="width: 80px"
                                ><i class="fa-solid fa-arrow-left"></i></i>
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div></x-app-layout
>
