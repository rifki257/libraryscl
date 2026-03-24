<div class="hidden space-x-8 sm:-my-px sm:ms-7 sm:flex">
    <x-nav-link
        :href="route('dashboard')"
        :active="request()->routeIs('dashboard')"
        class="px-2" 
    >
        {{ __('Dashboard') }}
    </x-nav-link>

    <x-nav-link
        :href="route('buku')"
        :active="request()->routeIs('buku')"
        class="px-2"
    >
        {{ __('Buku') }}
    </x-nav-link>

    <x-nav-link
        :href="route('peminjamandata')"
        :active="request()->routeIs('peminjamandata')"
        class="px-2"
    >
        {{ __('Peminjam') }}
    </x-nav-link>

    <x-nav-link
        :href="route('pengembalian')"
        :active="request()->routeIs('pengembalian')"
        class="px-2"
    >
        {{ __('Pengembalian') }}
    </x-nav-link>
    @if (Auth::user()->role == 'kepper')
        @php
        $active = request()->routeIs('register.petugas') || 
                    request()->routeIs('akun_admin') || 
                    request()->routeIs('akun_user');
    @endphp
        <div class="hidden sm:flex sm:items-center">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button
                        class="inline-flex items-center h-16 px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none {{ $active 
                    ? 'border-indigo-400 text-gray-900 focus:border-indigo-700' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300' }}"
                    >
                        <div>{{ __('Kelola Akun') }}</div>

                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link
                        :href="route('register.petugas')"
                        :active="request()->routeIs('register.petugas')"
                    >
                        {{ __('Buat Akun') }}
                    </x-dropdown-link>

                    <x-dropdown-link
                        :href="route('akun_admin')"
                        :active="request()->routeIs('akun_admin')"
                    >
                        {{ __('Daftar Akun Admin') }}
                    </x-dropdown-link>

                    <x-dropdown-link
                        :href="route('akun_user')"
                        :active="request()->routeIs('akun_user')"
                    >
                        {{ __('Daftar Akun User') }}
                    </x-dropdown-link>
                </x-slot>
            </x-dropdown>
        </div>
    @endif
</div>
