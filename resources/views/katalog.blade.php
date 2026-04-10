
<x-app-layout>
    {{-- kategori buku --}}
    @include ('partials.kategori_buku')
    {{-- akhir kategori buku --}}

    {{-- card buku --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="katalog-kontainer">
                @include ('partials.katalog_isi')
            </div>
        </div>
    </div>
    {{-- akhir card buku --}}
</x-app-layout>
