@extends('layouts.app')

@section('title', 'KelasDigital — Belajar Digital, Cuan Digital')

@section('content')
    <section class="bg-gradient-to-b from-blue-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 text-center">
            <h1 class="text-4xl md:text-6xl font-bold tracking-tight text-gray-900">
                Belajar Digital, <span class="text-blue-700">Cuan Digital</span>
            </h1>
            <p class="mt-6 max-w-2xl mx-auto text-lg text-gray-600">
                Kursus dan software digital pilihan, lengkap dengan program afiliasi.
                Beli sekali, pakai selamanya, dan dapatkan komisi setiap kali Anda mereferensikan.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-md bg-blue-700 px-6 py-3 text-base font-medium text-white hover:bg-blue-800 transition">
                    Lihat Produk
                </a>
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 transition">
                        Daftar Gratis
                    </a>
                @endguest
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid md:grid-cols-3 gap-6">
        @foreach ([
            ['Kursus Berkualitas', 'Materi disusun praktisi, bisa diakses kapan saja.', 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'],
            ['Program Afiliasi', 'Komisi hingga 30% setiap referral berhasil membeli.', 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z'],
            ['Pencairan Mudah', 'Saldo komisi bisa ditarik ke rekening kapan saja.', 'M3 10h18M7 15h2m4 0h4M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z'],
        ] as [$title, $desc, $svg])
            <div class="rounded-lg border border-gray-200 bg-white p-6 hover:shadow-md transition">
                <div class="h-10 w-10 rounded-md bg-blue-50 text-blue-700 flex items-center justify-center mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $svg }}"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                <p class="mt-2 text-sm text-gray-600">{{ $desc }}</p>
            </div>
        @endforeach
    </section>
@endsection
