<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'KelasDigital'))</title>
        <meta name="description" content="@yield('description', 'KelasDigital — kursus & software digital lengkap dengan program afiliasi.')">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS via CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>body{font-family:'Figtree',ui-sans-serif,system-ui,sans-serif}</style>
    </head>
    <body class="min-h-screen flex flex-col bg-gray-50 text-gray-700 antialiased">
        <header class="bg-white border-b border-gray-200">
            <nav x-data="{ open: false }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-8">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-blue-700 tracking-tight">KelasDigital</a>
                        <div class="hidden md:flex items-center gap-6 text-sm font-medium">
                            <a href="{{ route('home') }}"
                               class="{{ request()->routeIs('home') ? 'text-blue-700' : 'text-gray-600 hover:text-blue-700' }} transition">Beranda</a>
                            <a href="{{ route('products.index') }}"
                               class="{{ request()->routeIs('products.*') ? 'text-blue-700' : 'text-gray-600 hover:text-blue-700' }} transition">Produk</a>
                        </div>
                    </div>

                    <div class="hidden md:flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-blue-700">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 transition">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-blue-700">Login</a>
                            <a href="{{ route('register') }}" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800 transition">Daftar</a>
                        @endauth
                    </div>

                    <button @click="open = !open" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div x-show="open" x-collapse class="md:hidden pb-4 space-y-1" style="display:none;">
                    <a href="{{ route('home') }}" class="block rounded-md px-3 py-2 text-base font-medium {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">Beranda</a>
                    <a href="{{ route('products.index') }}" class="block rounded-md px-3 py-2 text-base font-medium {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">Produk</a>
                    <div class="pt-2 border-t border-gray-200">
                        @auth
                            <a href="{{ route('dashboard') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="block rounded-md px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-100">Login</a>
                            <a href="{{ route('register') }}" class="block rounded-md px-3 py-2 text-base font-medium text-blue-700 hover:bg-blue-50">Daftar</a>
                        @endauth
                    </div>
                </div>
            </nav>
        </header>

        @if (session('success'))
            <div class="bg-green-50 border-b border-green-200 text-green-700 text-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">{{ session('success') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 border-b border-red-200 text-red-700 text-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">{{ session('error') }}</div>
            </div>
        @endif

        <main class="flex-1">
            @yield('content')
        </main>

        <footer class="border-t border-gray-200 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} KelasDigital. Semua hak dilindungi.</p>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-blue-700" aria-label="Facebook">Facebook</a>
                    <a href="#" class="hover:text-blue-700" aria-label="Instagram">Instagram</a>
                    <a href="#" class="hover:text-blue-700" aria-label="YouTube">YouTube</a>
                    <a href="#" class="hover:text-blue-700" aria-label="WhatsApp">WhatsApp</a>
                </div>
            </div>
        </footer>
    </body>
</html>
