<div class="hidden space-x-5 sm:-my-px sm:ms-7 sm:flex ">
    <x-nav-link :href="route('userdashboard')" :active="request()->routeIs('userdashboard')" class="w-20 justify-center">
        {{ __('Dashboard') }}
    </x-nav-link>
    <x-nav-link :href="route('katalog')" :active="request()->routeIs('katalog')" class="w-20 justify-center">
        {{ __('Buku') }}
    </x-nav-link>
    <x-nav-link :href="route('mypinjaman')" :active="request()->routeIs('mypinjaman')" class="w-20 justify-center">
        {{ __('Dipinjam') }}
    </x-nav-link>
    <x-nav-link :href="route('mybalik')" :active="request()->routeIs('mybalik')" class="w-20 justify-center">
        {{ __('Dikembalikan') }}
    </x-nav-link>
</div>