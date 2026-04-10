<div class="max-w-7xl mx-auto px-4 py-10">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Kategori Buku</h2>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @foreach ($kategoris as $item)
            <a
                href="{{ route('isikategori', $item->id_kategori) }}"
                class="group relative overflow-hidden rounded-xl h-40 shadow-md"
            >
                @if ($item->gambar)
                    <img
                        src="{{ asset('storage/kategori/' . $item->gambar) }}"
                        alt="{{ $item->nama_kategori }}"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                    />
                @else
                    <div
                        class="w-full h-full bg-gray-300 flex items-center justify-center"
                    >
                        <span
                            class="text-gray-500 text-xs text-center px-2"
                            >{{ $item->nama_kategori }}</span
                        >
                    </div>
                @endif

                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"
                ></div>

                <div class="absolute inset-0 flex items-end p-4">
                    <span
                        class="text-white font-bold text-sm leading-tight drop-shadow-lg"
                    >
                        {{ $item->nama_kategori }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>
</div>
