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

