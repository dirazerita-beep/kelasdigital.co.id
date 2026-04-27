@extends('layouts.app')

@section('title', 'Semua Kursus & Produk · KelasDigital')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <header class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Semua Kursus &amp; Produk</h1>
            <p class="mt-2 text-gray-600">Pilih kursus atau software digital yang sesuai kebutuhan Anda.</p>
        </header>

        @if ($products->total() > 0)
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
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
                            <h2 class="mt-3 text-lg font-semibold text-gray-900 line-clamp-2">{{ $product->title }}</h2>
                            <p class="mt-2 text-sm text-gray-600 line-clamp-2 flex-1">{{ $product->description }}</p>
                            <p class="mt-4 text-xl font-bold text-blue-700">{{ formatRupiah($product->price) }}</p>
                            <a href="{{ route('products.show', $product->slug) }}"
                               class="mt-4 inline-flex justify-center rounded-md bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800 transition">
                                Lihat Detail
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $products->onEachSide(1)->links() }}
            </div>
        @else
            <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-gray-500">
                <p>Belum ada produk aktif saat ini.</p>
            </div>
        @endif
    </section>
@endsection
