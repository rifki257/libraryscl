<head>
    @vite (['resources/css/app.scss', 'resources/js/app.js'])
</head>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <a
                    href="{{ route('kategori.buku') }}"
                    class="btn btn-success d-flex align-items-center gap-1"
                >
                    <i class="bi bi-list-ul"></i> Kategori
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a
                    href="{{ route('buku.create') }}"
                    class="btn btn-success d-flex align-items-center gap-1"
                >
                    <i class="bi bi-plus-lg"></i> Buku
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include ('partials.tabel_isi')
        </div>
    </div>
</x-app-layout>
