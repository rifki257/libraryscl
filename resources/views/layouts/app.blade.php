<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
        rel="stylesheet"
    />
    @vite (['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <!-- Scripts -->
    @vite (['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-white flex flex-col">
        @include ('layouts.navigation')

        <!-- Page Heading -->
        @isset ($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-grow">{{ $slot }}</main>
        @auth
            @if (in_array(Auth::user()->role, ['petugas', 'kepper', 'admin']))
                @include ('layouts.footers.admin')
            @else
                @include ('layouts.footers.user')
            @endif
        @else
            @include ('layouts.footers.user')
        @endauth
    </div>
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Maaf...',
                text: '{{ session('error') }}',
            });
        </script>
    @endif
</body>
</html>
