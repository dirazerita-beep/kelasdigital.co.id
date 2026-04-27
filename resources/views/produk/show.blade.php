@extends('layouts.app')

@section('title', ($product->title ?? 'Detail Produk') . ' · KelasDigital')

@section('content')
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @isset($product)
            <a href="{{ route('products.index') }}" class="text-sm text-blue-700 hover:underline">&larr; Kembali ke daftar produk</a>
            <h1 class="mt-4 text-3xl font-bold text-gray-900">{{ $product->title }}</h1>
            <p class="mt-2 text-sm text-gray-500">{{ ucfirst($product->type) }} · Komisi {{ rtrim(rtrim((string) $product->commission_rate, '0'), '.') }}%</p>
            <div class="mt-6 prose max-w-none text-gray-700">
                <p>{{ $product->description }}</p>
            </div>
            <div class="mt-8 flex items-center justify-between rounded-lg border border-gray-200 bg-white p-6">
                <div>
                    <p class="text-sm text-gray-500">Harga</p>
                    <p class="text-3xl font-bold text-blue-700">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                </div>
                <button class="rounded-md bg-blue-700 px-6 py-3 text-sm font-medium text-white hover:bg-blue-800 transition" disabled>
                    Beli Sekarang (placeholder)
                </button>
            </div>
        @else
            <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center">
                <p class="text-gray-500">Halaman detail produk dengan slug <code>{{ $slug ?? '-' }}</code>.</p>
                <p class="mt-2 text-xs text-gray-400">(Placeholder — akan diisi setelah <code>ProductController@show</code> dibuat.)</p>
            </div>
        @endisset
    </section>
@endsection
