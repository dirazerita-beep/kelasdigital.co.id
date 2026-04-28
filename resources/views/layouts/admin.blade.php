<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin') · {{ config('app.name', 'KelasDigital') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>body{font-family:'Figtree',ui-sans-serif,system-ui,sans-serif}</style>
    </head>
    <body class="min-h-screen bg-gray-50 text-gray-700 antialiased" x-data="{ sidebarOpen: false }">
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-gray-900/50 md:hidden" style="display:none;"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
               class="fixed inset-y-0 left-0 z-40 w-64 transform bg-gray-900 text-gray-100 transition-transform overflow-y-auto">
            <div class="h-16 flex items-center px-6 border-b border-gray-800">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-white">KelasDigital <span class="text-blue-400 text-xs font-medium uppercase tracking-wider">Admin</span></a>
            </div>
            <nav class="p-4 space-y-1 text-sm font-medium">
                @php
                    $adminLink = function ($routeName, $label) {
                        $active = request()->routeIs($routeName);
                        $base = 'flex items-center gap-3 rounded-md px-3 py-2 transition';
                        return $active
                            ? "$base bg-blue-700 text-white"
                            : "$base text-gray-300 hover:bg-gray-800 hover:text-white";
                    };
                @endphp
                <a href="{{ route('admin.dashboard') }}" class="{{ $adminLink('admin.dashboard', '') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10h4v-6h6v6h4V10"/></svg>
                    Dashboard Admin
                </a>
                <a href="{{ route('admin.products') }}" class="{{ $adminLink('admin.products', '') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14L12 11m0 0L4 7m8 4v10"/></svg>
                    Kelola Produk
                </a>
                <a href="{{ route('admin.members') }}" class="{{ $adminLink('admin.members', '') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Kelola Member
                </a>
                <a href="{{ route('admin.orders') }}" class="{{ $adminLink('admin.orders', '') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9h6m-6 4h6"/></svg>
                    Pesanan
                </a>
                <a href="{{ route('admin.commissions') }}" class="{{ $adminLink('admin.commissions', '') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Komisi
                </a>
                <a href="{{ route('admin.withdrawals') }}" class="{{ $adminLink('admin.withdrawals', '') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h2m4 0h4M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z"/></svg>
                    Pencairan
                </a>
                <a href="{{ route('admin.settings.index') }}" class="{{ $adminLink('admin.settings.*', '') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317a1.724 1.724 0 013.35 0c.21.79 1.094 1.197 1.84.83a1.724 1.724 0 012.37 2.37c-.367.745.04 1.629.83 1.84a1.724 1.724 0 010 3.35c-.79.21-1.197 1.094-.83 1.84a1.724 1.724 0 01-2.37 2.37c-.745-.367-1.629.04-1.84.83a1.724 1.724 0 01-3.35 0c-.21-.79-1.094-1.197-1.84-.83a1.724 1.724 0 01-2.37-2.37c.367-.745-.04-1.629-.83-1.84a1.724 1.724 0 010-3.35c.79-.21 1.197-1.094.83-1.84a1.724 1.724 0 012.37-2.37c.745.367 1.629-.04 1.84-.83z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Pengaturan
                </a>
                <form method="POST" action="{{ route('logout') }}" class="pt-4 mt-4 border-t border-gray-800">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 rounded-md px-3 py-2 text-gray-300 hover:bg-red-700 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </nav>
        </aside>

        <div class="md:pl-64 flex flex-col min-h-screen">
            <header class="sticky top-0 z-20 bg-white border-b border-gray-200">
                <div class="h-16 px-4 sm:px-6 flex items-center justify-between">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="flex items-center gap-3">
                        <span class="rounded-md bg-blue-700 text-white text-xs font-semibold uppercase tracking-wider px-2.5 py-1">Admin Panel</span>
                        <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard Admin')</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="hidden sm:block text-sm text-gray-700">{{ auth()->user()->name ?? '' }}</span>
                        <div class="h-9 w-9 rounded-full bg-gray-900 text-white flex items-center justify-center text-sm font-semibold">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
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
