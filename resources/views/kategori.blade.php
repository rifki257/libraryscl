<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Kategori Buku') }}
            </h2>
            <div class="d-flex align-items-center gap-2">
                <button
                    x-data=""
                    x-on:click.prevent="
                        $dispatch('open-modal', 'tambah-kategori')
                    "
                    class="btn btn-success d-flex align-items-center gap-1"
                >
                    <i class="bi bi-plus-lg"></i> Tambah Kategori
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden shadow-sm sm:rounded-lg" style="background-color: rgb(235, 235, 235)">
            <div class="p-6 text-gray-900">
                <div class="container">
                    <div class="card-body">
                        <div id="tabel-buku" class="mt-4">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr class="text-capitalize text-center">
                                        <th width="10%">No</th>
                                        <th>Kategori</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Loop data dari database --}}
                                    @forelse ($kategoris as $key => $item)
                                        <tr class="text-center">
                                            <td>{{ $key + 1 }}</td>
                                            <td class="text-start">{{ $item->nama_kategori }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button class="btn btn-sm btn-warning">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <form action="#" method="POST" onsubmit="return confirm('Yakin hapus kategori ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted italic">
                                                Belum ada data kategori tersedia.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <x-modal name="tambah-kategori" focusable>
        <form method="post" action="{{ route('kategori.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                Tambah Kategori Baru
            </h2>

            <div class="mt-6">
                <x-input-label
                    for="nama_kategori"
                    value="Nama Kategori"
                    class="sr-only"
                />
                <x-text-input
                    id="nama_kategori"
                    name="nama_kategori"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="Nama Kategori"
                />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <x-primary-button class="ms-3"> Simpan </x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
