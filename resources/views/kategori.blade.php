<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <a
                href="{{ route('buku') }}"
                class="btn btn-secondary"
                style="width: 80px"
                ><i class="fa-solid fa-arrow-left me-1"></i
            ></a>
            <div class="d-flex align-items-center gap-2">
                <button
                    x-data=""
                    x-on:click.prevent="
                        $dispatch('open-modal', 'tambah-kategori')
                    "
                    class="btn btn-success d-flex align-items-center gap-1"
                >
                    <i class="bi bi-plus-lg"></i>Kategori
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr class="text-capitalize text-center">
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Kategori</th>
                        <th>Total buku</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kategoris as $key => $item)
                        <tr class="text-center align-middle">
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <div
                                    class="d-flex justify-content-center align-items-center"
                                    style="height: 50px"
                                >
                                    @if ($item->gambar)
                                        <img
                                            src="{{ asset('storage/kategori/' . $item->gambar) }}"
                                            alt="{{ $item->nama_kategori }}"
                                            style="
                                                width: 50px;
                                                height: 50px;
                                                object-fit: cover;
                                            "
                                            class="rounded"
                                        />
                                    @else
                                        <span class="text-muted small"
                                            >No Image</span
                                        >
                                    @endif
                                </div>
                            </td>
                            <td>{{ $item->nama_kategori }}</td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $item->buku_count }} Judul
                                </span>
                            </td>
                            <td>
                                <div
                                    class="d-flex justify-content-center align-items-center gap-2"
                                >
                                    {{-- edit --}}
                                    <button
                                        x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'edit-kategori-{{ $item->id_kategori }}')"
                                        class="btn btn-warning"
                                        style="width: 80px"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    {{-- hapus --}}
                                    <button
                                        x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'konfirmasi-hapus-{{ $item->id_kategori }}')"
                                        class="btn btn-danger"
                                        style="width: 80px"
                                        title="Hapus Kategori"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                {{-- halaman edit --}}
                                <x-modal
                                    name="edit-kategori-{{ $item->id_kategori }}"
                                    focusable
                                >
                                    <form
                                        method="post"
                                        action="{{ route('kategori.update', $item->id_kategori) }}"
                                        class="p-6"
                                        enctype="multipart/form-data"
                                    >
                                        @csrf
                                        @method ('PUT')

                                        <h2
                                            class="text-lg font-medium text-gray-900 text-start"
                                        >
                                            Edit Kategori
                                        </h2>

                                        <div class="mt-6 text-start">
                                            <x-input-label
                                                for="nama_kategori_{{ $item->id_kategori }}"
                                                value="Nama Kategori"
                                            />
                                            <x-text-input
                                                id="nama_kategori_{{ $item->id_kategori }}"
                                                name="nama_kategori"
                                                type="text"
                                                class="form-control"
                                                value="{{ $item->nama_kategori }}"
                                                required
                                            />
                                        </div>

                                        <div class="mt-4 text-start">
                                            <x-input-label
                                                value="Gambar Saat Ini"
                                            />
                                            @if ($item->gambar)
                                                <img
                                                    src="{{ asset('storage/kategori/' . $item->gambar) }}"
                                                    class="rounded mb-2"
                                                    style="
                                                        width: 100px;
                                                        height: 100px;
                                                        object-fit: cover;
                                                    "
                                                />
                                            @else
                                                <p class="text-muted small">Tidak ada gambar.</p>
                                            @endif

                                            <x-input-label
                                                for="gambar_{{ $item->id_kategori }}"
                                                value="Ganti Gambar (Opsional)"
                                            />
                                            <input
                                                type="file"
                                                id="gambar_{{ $item->id_kategori }}"
                                                name="gambar"
                                                class="form-control"
                                                accept="image/*"
                                            />
                                        </div>

                                        <div class="mt-6 flex justify-end">
                                            <x-secondary-button
                                                x-on:click="$dispatch('close')"
                                                >Batal</x-secondary-button
                                            >
                                            <x-primary-button class="ms-3"
                                                >Update</x-primary-button
                                            >
                                        </div>
                                    </form>
                                </x-modal>
                                {{-- halaman hapus --}}
                                <x-modal
                                    name="konfirmasi-hapus-{{ $item->id_kategori }}"
                                    focusable
                                >
                                    <div class="p-6">
                                        <div
                                            class="d-flex align-items-center gap-3 text-danger mb-4"
                                        >
                                            <div
                                                class="bg-danger-subtle p-3 rounded-circle d-flex align-items-center justify-content-center"
                                                style="
                                                    width: 60px;
                                                    height: 60px;
                                                "
                                            >
                                                <i
                                                    class="bi bi-exclamation-triangle fs-1 text-danger"
                                                ></i>
                                            </div>
                                            <div>
                                                <h2
                                                    class="text-lg font-medium text-gray-900 m-0"
                                                >
                                                    Hapus Kategori?
                                                </h2>
                                                <p class="text-muted m-0">Tindakan ini tidak dapat dibatalkan.</p>
                                            </div>
                                        </div>

                                        <div
                                            class="mt-4 bg-light p-3 rounded border text-start"
                                        >
                                            Apakah Anda yakin ingin menghapus
                                            kategori
                                            <strong
                                                >"{{ $item->nama_kategori }}"</strong
                                            >?
                                            @if ($item->buku_count > 0)
                                                <div
                                                    class="alert alert-warning mt-2 mb-0 py-2 small d-flex align-items-center gap-2"
                                                >
                                                    <i
                                                        class="bi bi-info-circle-fill"
                                                    ></i>
                                                    Perhatian: Ada
                                                    <strong
                                                        >{{ $item->buku_count }} buku</strong
                                                    >
                                                    yang menggunakan kategori
                                                    ini.
                                                </div>
                                            @endif
                                        </div>

                                        <form
                                            method="post"
                                            action="{{ route('kategori.destroy', $item->id_kategori) }}"
                                            class="mt-6 flex justify-end gap-2"
                                        >
                                            @csrf
                                            @method ('DELETE')

                                            <x-secondary-button
                                                x-on:click="$dispatch('close')"
                                                type="button"
                                            >
                                                Batal
                                            </x-secondary-button>

                                            <button
                                                type="submit"
                                                class="btn btn-danger d-flex align-items-center gap-2"
                                            >
                                                <i class="bi bi-trash-fill"></i>
                                                Ya, Hapus Sekarang
                                            </button>
                                        </form>
                                    </div>
                                </x-modal>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="4"
                                class="text-center text-muted fst-italic"
                            >
                                Belum ada data kategori tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{-- <div class="mt-10">
            {{ $dataBuku->links() }}
        </div> --}}
    {{-- halaman buat --}}
    <x-modal name="tambah-kategori" focusable>
        <form
            method="post"
            action="{{ route('kategori.store') }}"
            class="p-6"
            enctype="multipart/form-data"
        >
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                Tambah Kategori Baru
            </h2>

            <div class="mt-6">
                <x-input-label for="nama_kategori" value="Nama Kategori" />
                <x-text-input
                    id="nama_kategori"
                    name="nama_kategori"
                    type="text"
                    class="form-control"
                    placeholder="Nama Kategori"
                    required
                />
            </div>

            <div class="mt-4">
                <x-input-label for="gambar" value="Gambar Kategori" />
                <input
                    type="file"
                    id="gambar"
                    name="gambar"
                    class="form-control"
                    accept="image/*"
                    required
                />
                @error('gambar')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
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
