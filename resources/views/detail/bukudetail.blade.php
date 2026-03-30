<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Buku') }}
            </h2>
            <a href="{{ route('buku.create') }}" class="btn btn-success"
                >Tambah</a
            >
        </div>
    </x-slot>

    <div class="py-12"></div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="row">
                    <div class="col">
                        <td>
                            @if ($buku->gambar)
                                <img
                                    src="{{ asset('storage/' . $buku->gambar) }}"
                                    alt="cover"
                                    style="width: 200px; height: auto"
                                    class="rounded shadow-sm mx-auto mb-4"
                                />
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th class="bg-light" style="width: 30%">
                                        Id Buku
                                    </th>
                                    <td class="text-capitalize">
                                        {{ $buku->id_buku }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light" style="width: 30%">
                                        Judul
                                    </th>
                                    <td class="text-capitalize">
                                        {{ $buku->judul }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Penulis</th>
                                    <td class="text-capitalize">
                                        {{ $buku->penulis }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Penerbit</th>
                                    <td class="text-capitalize">
                                        {{ $buku->penerbit }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Jumlah</th>
                                    <td class="text-capitalize">
                                        {{ $buku->jumlah }} Ekor
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <a
                            href="{{ route('buku.edit', $buku->id_buku) }}"
                            class="btn btn-warning text-white mt-2"
                            style="width: 80px"
                            >Edit</a
                        >
                        <a
                            href="{{ route('buku') }}"
                            class="btn btn-secondary mt-2"
                            >Kembali</a
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
