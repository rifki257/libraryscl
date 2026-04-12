
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<x-app-layout>
    @include ('partials.kategori_buku')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="katalog-kontainer">
                @include ('partials.katalog_isi')
            </div>
        </div>
    </div>
</x-app-layout>
