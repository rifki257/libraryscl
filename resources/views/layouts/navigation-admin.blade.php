<div class="hidden space-x-2 sm:-my-px sm:ms-7 sm:flex items-center">
    <a
        href="{{ route('dashboard') }}"
        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md transition ease-in-out duration-150 {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 bg-white hover:text-gray-700' }}"
    >
        {{ __('Dashboard') }}
    </a>

    <a
        href="{{ route('buku') }}"
        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md transition ease-in-out duration-150 {{ request()->routeIs('buku') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 bg-white hover:text-gray-700' }}"
    >
        {{ __('Buku') }}
    </a>
    @php
        $activeSirkulasi = request()->routeIs('admin.persetujuan', 'persetujuan.data', 'pengembalian', 'pengembalian.data');
    @endphp
    <div class="hidden sm:flex sm:items-center">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md transition ease-in-out duration-150 {{ $activeSirkulasi ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 bg-white hover:text-gray-700 focus:outline-none' }}"
                >
                    <div>Sirkulasi Buku</div>
                    <div class="ms-1">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
            </x-slot>
            <x-slot name="content">
                <div class="block px-4 py-2 text-xs text-gray-400">
                    {{ __('Manajemen Peminjaman') }}
                </div>
                @if (auth()->user()->role === 'petugas')
                    <x-dropdown-link :href="route('admin.persetujuan')">
                        {{ __('Konfir Peminjaman') }}
                    </x-dropdown-link>
                @endif
                <x-dropdown-link :href="route('persetujuan.data')">
                    {{ __('Data Peminjaman') }}
                </x-dropdown-link>
                <div class="border-t border-gray-200"></div>
                <div class="block px-4 py-2 text-xs text-gray-400">
                    {{ __('Manajemen Pengembalian') }}
                </div>
                @if (auth()->user()->role === 'petugas')
                    <x-dropdown-link :href="route('pengembalian')">
                        {{ __('Konfir Pengembalian') }}
                    </x-dropdown-link>
                @endif
                <x-dropdown-link :href="route('pengembalian.data')">
                    {{ __('Data Pengembalian') }}
                </x-dropdown-link>
            </x-slot>
        </x-dropdown>
    </div>
    @php
    $activeAkun = request()->routeIs('register.petugas', 'akun_admin', 'users.siswa');
@endphp

    <div class="hidden sm:flex sm:items-center">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md transition ease-in-out duration-150 {{ $activeAkun ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 bg-white hover:text-gray-700 focus:outline-none' }}"
                >
                    <div>Kelola Akun</div>
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
                    {{ __('Buat Akun Baru') }}
                </x-dropdown-link>

                @if (Auth::user()->role == 'kepper')
                    <x-dropdown-link
                        :href="route('akun_admin')"
                        :active="request()->routeIs('akun_admin')"
                    >
                        {{ __('Daftar Akun Admin') }}
                    </x-dropdown-link>
                @endif

                <div class="border-t border-gray-200"></div>
                <div class="block px-4 py-2 text-xs text-gray-400">
                    {{ __('Manajemen User') }}
                </div>

                <x-dropdown-link
                    :href="route('users.siswa')"
                    :active="request()->routeIs('users.siswa')"
                >
                    {{ __('Daftar Semua Siswa') }}
                </x-dropdown-link>
            </x-slot>
        </x-dropdown>
    </div>
    @if (auth()->user()->role === 'petugas')
        <a
            href="{{ route('admin.laporan.index') }}"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md transition ease-in-out duration-150 {{ request()->routeIs('admin.laporan.index') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 bg-white hover:text-gray-700' }}"
        >
            {{ __('Laporan') }}
        </a>
    @endif
</div>
