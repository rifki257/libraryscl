<x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    {{ __('Dashboard') }}
</x-responsive-nav-link>

<x-responsive-nav-link :href="route('buku')" :active="request()->routeIs('buku')">
    {{ __('Kelola Buku') }}
</x-responsive-nav-link>
@if(Auth::user()->role == 'kepper')
<x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">
    {{ __('Register') }}
</x-responsive-nav-link>
@endif