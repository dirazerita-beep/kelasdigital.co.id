@extends('layouts.app')

@section('title', 'KelasDigital — Tingkatkan Skill Digital Kamu')

@section('content')
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 text-center">
            <h1 class="text-4xl md:text-6xl font-bold tracking-tight">
                Tingkatkan Skill Digital Kamu Bersama <span class="text-blue-200">KelasDigital</span>
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-lg md:text-xl text-blue-100">
                Belajar dari kursus berkualitas dan dapatkan penghasilan dari afiliasi.
            </p>
            <div class="mt-10">
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center justify-center rounded-md bg-white text-blue-700 px-8 py-3 text-base font-semibold hover:bg-blue-50 transition shadow-lg">
                    Lihat Semua Kursus
                </a>
            </div>
        </div>
    </section>

    {{-- Featured Products --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <header class="mb-10 text-center">
            <h2 class="text-3xl font-bold text-gray-900">Kursus Terpopuler</h2>
            <p class="mt-2 text-gray-600">Pilihan kursus &amp; produk digital yang paling diminati.</p>
        </header>

        @if ($featuredProducts->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($featuredProducts as $product)
                    <article class="flex flex-col rounded-lg border border-gray-200 bg-white overflow-hidden hover:shadow-md transition">
                        <div class="aspect-[16/10] w-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                            @if ($product->thumbnail)
                                <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->title }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-blue-700 text-2xl font-bold tracking-tight px-4 text-center">{{ $product->title }}</span>
                            @endif
                        </div>
                        <div class="flex flex-col flex-1 p-5">
                            <span class="self-start rounded bg-blue-50 text-blue-700 text-xs font-semibold uppercase tracking-wide px-2 py-0.5">
                                {{ $product->type === 'software' ? 'Software' : 'Kursus' }}
                            </span>
                            <h3 class="mt-3 text-lg font-semibold text-gray-900 line-clamp-2">{{ $product->title }}</h3>
                            <p class="mt-3 text-xl font-bold text-blue-700">{{ formatRupiah($product->price) }}</p>
                            <a href="{{ route('products.show', $product->slug) }}"
                               class="mt-4 inline-flex justify-center rounded-md bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800 transition">
                                Lihat Detail
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-gray-500">
                Belum ada kursus aktif. Cek kembali nanti.
            </div>
        @endif
    </section>

    {{-- Keunggulan --}}
    <section class="bg-gray-50 border-y border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid gap-6 md:grid-cols-3">
            @foreach ([
                ['Materi Berkualitas', 'Kurikulum disusun praktisi yang sudah terbukti di industri.', 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'],
                ['Akses Selamanya', 'Beli sekali, akses materi tanpa batas waktu — kapan saja, di mana saja.', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Komisi Afiliasi', 'Dapatkan komisi hingga 30% setiap referral berhasil membeli.', 'M17 9V7a4 4 0 00-8 0v2M5 9h14l-1 12H6L5 9z'],
            ] as [$title, $desc, $svg])
                <div class="rounded-lg bg-white border border-gray-200 p-6">
                    <div class="h-10 w-10 rounded-md bg-blue-50 text-blue-700 flex items-center justify-center mb-4">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $svg }}"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                    <p class="mt-2 text-sm text-gray-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Siap Mulai Belajar?</h2>
        <p class="mt-3 text-gray-600">Buat akun gratis dan langsung akses program afiliasi.</p>
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
            @guest
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-md bg-blue-700 px-8 py-3 text-base font-semibold text-white hover:bg-blue-800 transition shadow">
                    Daftar Sekarang
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-8 py-3 text-base font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Sudah Punya Akun
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-md bg-blue-700 px-8 py-3 text-base font-semibold text-white hover:bg-blue-800 transition shadow">
                    Buka Dashboard
                </a>
            @endguest
        </div>
    </section>
@endsection
