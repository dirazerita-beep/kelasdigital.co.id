@extends('layouts.app')

@section('title', 'Produk · KelasDigital')

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Semua Produk</h1>
            <p class="mt-2 text-gray-600">Kursus dan software digital untuk akselerasi belajar &amp; bisnis Anda.</p>
        </header>

        @if (isset($products) && $products->count())
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    <a href="{{ route('products.show', $product->slug) }}" class="group rounded-lg border border-gray-200 bg-white p-6 hover:shadow-md transition">
                        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide">
                            <span class="rounded bg-blue-50 text-blue-700 px-2 py-0.5">{{ ucfirst($product->type) }}</span>
                            <span class="rounded bg-green-50 text-green-700 px-2 py-0.5">{{ ucfirst($product->status) }}</span>
                        </div>
                        <h2 class="mt-3 text-lg font-semibold text-gray-900 group-hover:text-blue-700">{{ $product->title }}</h2>
                        <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $product->description }}</p>
                        <p class="mt-4 text-xl font-bold text-blue-700">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                    </a>
                @endforeach
            </div>
        @else
            <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center">
                <p class="text-gray-500">Daftar produk akan tampil di sini setelah controller produk dibuat.</p>
                <p class="mt-2 text-xs text-gray-400">(Placeholder — halaman ini akan diisi dari <code>App\Http\Controllers\ProductController@index</code>.)</p>
            </div>
        @endif
    </section>
@endsection
