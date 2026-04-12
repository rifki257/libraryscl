<nav
    x-data="{ open: false, userDropdown: false, showModal: false }"
    class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-md"
>
    @if (!Auth::check() || Auth::user()->role === 'anggota')
        <div
            class="border-b border-gray-100 py-2"
            style="background: rgb(234, 234, 234)"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="flex justify-end space-x-6 text-xs font-medium text-gray-600"
                >
                    <a href="#" class="hover:text-indigo-600 transition-colors"
                        >Aturan & Denda</a
                    >
                    <a href="#" class="hover:text-indigo-600 transition-colors"
                        >Hubungi Pustakawan</a
                    >
                </div>
            </div>
        </div>
    @endif
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a
                        href="{{ Auth::check() && Auth::user()->role === 'admin' ? route('admin.dashboard') : route('userdashboard') }}"
                    >
                        <x-application-logo
                            class="block h-9 w-auto fill-current text-gray-800"
                        />
                    </a>
                </div>

                @auth
                    @if (Auth::user()->role === 'anggota')
                        @include ('layouts.navigation-user')
                    @else
                        @include ('layouts.navigation-admin')
                    @endif
                @else
                    @include ('layouts.navigation-guest')
                @endauth
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <nav class="bg-white px-3 py-2.5">
                    <div
                        class="flex justify-between items-center max-w-7xl mx-auto"
                    >
                        <div class="flex items-center space-x-4">
                            {{-- awal notif --}}
                            @if (Auth::user()->role == 'anggota')
                                <x-dropdown align="right" width="64">
                                    <x-slot name="trigger">
                                        <button
                                            class="relative inline-flex items-center p-2 rounded-full text-gray-600 hover:text-black hover:bg-gray-100 transition-all focus:outline-none"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                            </svg>

                                            @auth
                                                @if (auth()->user()->unreadNotifications->count() > 0)
                                                    <span
                                                        class="absolute top-1.5 right-1.5 flex h-4 w-4"
                                                    >
                                                        <span
                                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"
                                                        ></span>
                                                        <span
                                                            class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[10px] text-white items-center justify-center font-bold"
                                                        >
                                                            {{ auth()->user()->unreadNotifications->count() }}
                                                        </span>
                                                    </span>
                                                @endif
                                            @endauth
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <div class="w-80 sm:w-96">
                                            <div
                                                class="block px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100"
                                            >
                                                Pemberitahuan
                                            </div>

                                            <div
                                                class="max-h-80 overflow-y-auto"
                                            >
                                                @auth
                                                    @forelse (auth()->user()->notifications as $notification)
                                                        <div
                                                            class="px-4 py-3 hover:bg-gray-50 transition border-b border-gray-50 last:border-0 {{ $notification->read_at ? 'opacity-60' : '' }}"
                                                        >
                                                            <div
                                                                class="flex flex-col gap-1"
                                                            >
                                                                <div
                                                                    class="flex justify-between items-start"
                                                                >
                                                                    <span
                                                                        class="text-[10px] font-bold text-red-600 uppercase"
                                                                        >Peminjaman
                                                                        Ditolak</span
                                                                    >
                                                                    <span
                                                                        class="text-[9px] text-gray-400"
                                                                        >{{ $notification->created_at->diffForHumans() }}</span
                                                                    >
                                                                </div>
                                                                <p class="text-xs text-gray-700 leading-snug">
                                                                    {{ $notification->data['pesan'] }}
                                                                </p>
                                                                @if (isset($notification->data['alasan']))
                                                                    <p class="text-[11px] text-gray-500 italic bg-gray-100 p-1.5 rounded mt-1">"{{ $notification->data['alasan'] }}"</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div
                                                            class="px-4 py-6 text-center text-gray-500"
                                                        >
                                                            <p class="text-xs italic">Tidak ada notifikasi baru</p>
                                                        </div>
                                                    @endforelse
                                                @endauth
                                            </div>

                                            @auth
                                                @if (auth()->user()->notifications->count() > 0)
                                                    <a
                                                        href="{{ route('markNotificationsRead') }}"
                                                        class="block w-full text-center py-2 text-[11px] font-bold text-indigo-600 hover:bg-indigo-50 border-t border-gray-100"
                                                    >
                                                        Tandai Semua Dibaca
                                                    </a>
                                                @endif
                                            @endauth
                                        </div>
                                    </x-slot>
                                </x-dropdown>
                            @endif
                            {{-- akhir notif --}}
                            {{-- awal wishlist --}}
                            @if (Auth::user()->role == 'anggota')
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
                            @endif
                            {{-- akhir wishlist --}}
                        </div>
                    </div>
                </nav>
                <x-dropdown
                    align="right"
                    width="48"
                    x-on:click.outside="userDropdown = false"
                >
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                        >
                            <div>
                                {{ Auth::check() ? Auth::user()->name : 'Guest' }}
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @auth
                            <x-dropdown-link
                                :href="route('profile.edit')"
                                class="pt-3"
                                >{{ __('Profile') }}
                            </x-dropdown-link>
                            @if (Auth::user()->role === 'anggota')
                                <x-dropdown-link
                                    :href="route('userdashboard')"
                                    :active="request()->routeIs('userdashboard')"
                                    class="w-20 justify-center"
                                >
                                    {{ __('Dashboard') }}
                                </x-dropdown-link>
                                <x-dropdown-link
                                    :href="route('katalog')"
                                    :active="request()->routeIs('katalog')"
                                    class="w-20 justify-center"
                                >
                                    {{ __('Buku') }}
                                </x-dropdown-link>
                                <x-dropdown-link
                                    :href="route('mypinjaman')"
                                    :active="request()->routeIs('mypinjaman')"
                                    class="w-20 justify-center"
                                >
                                    {{ __('Dipinjam') }}
                                </x-dropdown-link>
                                {{-- <x-dropdown-link
                                    :href="route('mybalik')"
                                    :active="request()->routeIs('mybalik')"
                                    class="w-20 justify-center"
                                >
                                    {{ __('Dikembalikan') }}
                                </x-dropdown-link> --}}
                            @endif
                            {{-- akhir anggota --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link
                                    :href="route('logout')"
                                    onclick="
                                        event.preventDefault();
                                        this.closest('form').submit();
                                    "
                                >
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        @else
                            <x-dropdown-link :href="route('login')"
                                >{{ __('Log In') }}
                            </x-dropdown-link>
                        @endauth
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button
                    @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path
                            :class="{
                                hidden: open,
                                'inline-flex': !open,
                            }"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            :class="{
                                hidden: !open,
                                'inline-flex': open,
                            }"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ block: open, hidden: !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @if (Auth::user()->role === 'anggota')
                    @include ('layouts.navigation-user-mobile')
                @else
                    @include ('layouts.navigation-admin-mobile')
                @endif
            @else
                <x-responsive-nav-link :href="route('katalog')"
                    >Katalog Buku</x-responsive-nav-link
                >
            @endauth
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">
                        {{ Auth::user()->name }}
                    </div>
                    <div class="font-medium text-sm text-gray-500">
                        {{ Auth::user()->email }}
                    </div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link
                        :href="route('profile.edit')"
                        >{{ __('Profile') }}</x-responsive-nav-link
                    >
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link
                            :href="route('logout')"
                            onclick="
                                event.preventDefault();
                                this.closest('form').submit();
                            "
                        >
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('login')"
                        >Log In</x-responsive-nav-link
                    >
                    <x-responsive-nav-link :href="route('register')"
                        >Register</x-responsive-nav-link
                    >
                </div>
            @endauth
        </div>
    </div>
</nav>
