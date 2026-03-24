<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Buku') }}
        </h2>
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
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Gambar</label>
                                        <input
                                            type="file"
                                            name="gambar"
                                            class="form-control"
                                        />
                                    </div>
                                </div>
                                <div class="col-6">
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
                                            required
                                        />
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-success mt-3">Simpan</button>
                            <a
                                href="{{ route('buku') }}"
                                class="btn btn-secondary mt-3"
                                >Kembali</a
                            >
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
