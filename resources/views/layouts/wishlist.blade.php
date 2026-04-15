<a
    href="{{ request()->routeIs('wishlist.index') ? route('katalog') : route('wishlist.index') }}"
    class="relative inline-flex items-center p-2 rounded-full transition-all 
    {{ request()->routeIs('wishlist.index') ? 'text-black bg-gray-100' : 'text-gray-600 hover:text-black hover:bg-gray-100' }}"
>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="{{ request()->routeIs('wishlist.index') ? 'currentColor' : 'none' }}"
        viewBox="0 0 24 24"
        stroke-width="{{ request()->routeIs('wishlist.index') ? '2' : '1.5' }}"
        stroke="currentColor"
        class="w-6 h-6"
    >
        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
    </svg>

    {{-- Logika Badge: Menyesuaikan kolom 'id' sebagai penunjuk user --}}
    @php
        $wishlistCount = 0;
        if(auth()->check()){
            $wishlistCount = \App\Models\Wishlist::where('id', auth()->id())->count();
        }
    @endphp

    @if ($wishlistCount > 0)
        <span
            class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-red-500 rounded-full border-2 border-white"
        >
            {{ $wishlistCount }}
        </span>
    @endif
</a>
