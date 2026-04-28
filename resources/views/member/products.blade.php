@extends('layouts.dashboard')

@section('title', 'Produk Saya')

@section('content')
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Produk Saya</h1>
        <p class="mt-1 text-sm text-gray-500">Akses dan lanjutkan kursus / produk yang sudah Anda beli.</p>
    </header>

    @if ($products->isEmpty())
        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center">
            <p class="text-sm text-gray-600">Anda belum memiliki produk apa pun.</p>
            <a href="{{ route('products.index') }}" class="mt-3 inline-block rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Jelajahi Katalog</a>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($products as $product)
                @php $p = $progressByProduct[$product->id] ?? ['percent'=>0,'completed'=>0,'total'=>0]; @endphp
                <article class="rounded-lg border border-gray-200 bg-white overflow-hidden flex flex-col">
                    <div class="aspect-video bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                        <span class="text-blue-700 font-semibold text-lg">{{ $product->title }}</span>
                    </div>
                    <div class="p-5 flex flex-col flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-gray-900">{{ $product->title }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-medium uppercase tracking-wide">{{ $product->type === 'software' ? 'Software' : 'Kursus' }}</span>
                        </div>
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                <span>Progress</span>
                                <span class="font-semibold">{{ $p['percent'] }}%</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-600" style="width: {{ $p['percent'] }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">{{ $p['completed'] }} dari {{ $p['total'] }} lesson selesai</p>
                        </div>
                        <a href="{{ route('learning.show', $product->slug) }}" class="mt-5 block text-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Belajar</a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
