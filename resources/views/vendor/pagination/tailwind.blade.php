{{-- resources/views/vendor/pagination/custom.blade.php --}}
@if ($paginator->hasPages())
    <nav role="navigation" class="flex justify-center mt-4">
        <div
            class="inline-flex -space-x-px shadow-sm rounded-md border border-gray-200"
        >
            {{-- Tombol Previous ( < ) --}}
            @if ($paginator->onFirstPage())
                <span
                    class="px-3 py-2 text-blue-500 bg-white border-r border-gray-200 cursor-not-allowed"
                    >&laquo;</span
                >
            @else
                <a
                    href="{{ $paginator->previousPageUrl() }}"
                    class="px-3 py-2 text-blue-500 bg-white border-r border-gray-200 hover:bg-gray-50"
                    >&laquo;</a
                >
            @endif

            {{-- Elemen Angka --}}
            @foreach ($elements as $element)
                {{-- Separator "..." --}}
                @if (is_string($element))
                    <span
                        class="px-3 py-2 text-blue-500 bg-white border-r border-gray-200"
                        >{{ $element }}</span
                    >
                @endif
                {{-- Link Angka --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            {{-- Halaman Aktif (Biru Terang) --}}
                            <span
                                class="px-4 py-2 text-white bg-blue-500 border-r border-gray-200 font-medium"
                                >{{ $page }}</span
                            >
                        @else
                            <a
                                href="{{ $url }}"
                                class="px-4 py-2 text-blue-500 bg-white border-r border-gray-200 hover:bg-gray-50"
                                >{{ $page }}</a
                            >
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next ( > ) --}}
            @if ($paginator->hasMorePages())
                <a
                    href="{{ $paginator->nextPageUrl() }}"
                    class="px-3 py-2 text-blue-500 bg-white hover:bg-gray-50"
                    >&raquo;</a
                >
            @else
                <span
                    class="px-3 py-2 text-blue-500 bg-white cursor-not-allowed"
                    >&raquo;</span
                >
            @endif
        </div>
    </nav>
@endif
