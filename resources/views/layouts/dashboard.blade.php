<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Dashboard') · {{ config('app.name', 'KelasDigital') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>body{font-family:'Figtree',ui-sans-serif,system-ui,sans-serif}</style>
    </head>
    <body class="min-h-screen bg-gray-50 text-gray-700 antialiased" x-data="{ sidebarOpen: false }">
        <!-- Mobile backdrop -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-gray-900/50 md:hidden" style="display:none;"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
               class="fixed inset-y-0 left-0 z-40 w-64 transform bg-white border-r border-gray-200 transition-transform overflow-y-auto">
            <div class="h-16 flex items-center px-6 border-b border-gray-200">
                <a href="{{ route('home') }}" class="text-xl font-bold text-blue-700">KelasDigital</a>
            </div>
            <nav class="p-4 space-y-1 text-sm font-medium">
                @php
                    $navItem = function ($routeName, $label, $svg) {
                        $active = request()->routeIs($routeName);
                        $base = 'flex items-center gap-3 rounded-md px-3 py-2 transition';
                        $cls = $active
                            ? "$base bg-blue-50 text-blue-700"
                            : "$base text-gray-700 hover:bg-gray-100 hover:text-blue-700";
                        return [$cls, $svg, $label];
                    };
                @endphp
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'flex items-center gap-3 rounded-md px-3 py-2 bg-blue-50 text-blue-700' : 'flex items-center gap-3 rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10h4v-6h6v6h4V10"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('member.products') }}" class="{{ request()->routeIs('member.products') ? 'flex items-center gap-3 rounded-md px-3 py-2 bg-blue-50 text-blue-700' : 'flex items-center gap-3 rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14L12 11m0 0L4 7m8 4v10"/></svg>
                    Produk Saya
                </a>
                <a href="{{ route('member.affiliate') }}" class="{{ request()->routeIs('member.affiliate') ? 'flex items-center gap-3 rounded-md px-3 py-2 bg-blue-50 text-blue-700' : 'flex items-center gap-3 rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Afiliasi
                </a>
                <a href="{{ route('member.balance') }}" class="{{ request()->routeIs('member.balance') ? 'flex items-center gap-3 rounded-md px-3 py-2 bg-blue-50 text-blue-700' : 'flex items-center gap-3 rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h2m4 0h4M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/></svg>
                    Saldo & Pencairan
                </a>
                <a href="{{ route('member.orders') }}" class="{{ request()->routeIs('member.orders') ? 'flex items-center gap-3 rounded-md px-3 py-2 bg-blue-50 text-blue-700' : 'flex items-center gap-3 rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9h6m-6 4h6"/></svg>
                    Pesanan Saya
                </a>
                <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'flex items-center gap-3 rounded-md px-3 py-2 bg-blue-50 text-blue-700' : 'flex items-center gap-3 rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-blue-700' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil
                </a>
                <form method="POST" action="{{ route('logout') }}" class="pt-4 mt-4 border-t border-gray-200">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 rounded-md px-3 py-2 text-gray-700 hover:bg-red-50 hover:text-red-700 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </nav>
        </aside>

        <div class="md:pl-64 flex flex-col min-h-screen">
            <!-- Topbar -->
            <header class="sticky top-0 z-20 bg-white border-b border-gray-200">
                <div class="h-16 px-4 sm:px-6 flex items-center justify-between">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                    <div class="flex items-center gap-3">
                        <span class="hidden sm:block text-sm text-gray-700">{{ auth()->user()->name ?? '' }}</span>
                        <div class="h-9 w-9 rounded-full bg-blue-700 text-white flex items-center justify-center text-sm font-semibold">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            @if (session('success'))
                <div class="bg-green-50 border-b border-green-200 text-green-700 text-sm px-6 py-2">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border-b border-red-200 text-red-700 text-sm px-6 py-2">{{ session('error') }}</div>
            @endif

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </body>
</html>
