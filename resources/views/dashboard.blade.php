@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <div class="grid gap-6 md:grid-cols-3">
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Produk Dimiliki</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $productsOwned }}</p>
        </div>
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Total Komisi</p>
            <p class="mt-2 text-3xl font-bold text-blue-700">{{ formatRupiah($totalCommission) }}</p>
        </div>
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Saldo Tersedia</p>
            <p class="mt-2 text-3xl font-bold text-green-700">{{ formatRupiah($balance) }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-lg bg-white border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Sedang Dipelajari</h2>
            <a href="{{ route('member.products') }}" class="text-sm font-medium text-blue-700 hover:underline">Lihat semua →</a>
        </div>

        @if ($recentProducts->isEmpty())
            <div class="p-8 text-center text-sm text-gray-500">
                Anda belum memiliki produk. <a href="{{ route('products.index') }}" class="text-blue-700 hover:underline">Jelajahi katalog</a>.
            </div>
        @else
            <ul class="divide-y divide-gray-200">
                @foreach ($recentProducts as $product)
                    @php $p = $progressByProduct[$product->id] ?? ['percent'=>0,'completed'=>0,'total'=>0]; @endphp
                    <li class="p-6 flex items-center gap-4">
                        <div class="h-16 w-16 rounded-md bg-blue-50 flex items-center justify-center text-blue-700 font-semibold flex-shrink-0">
                            {{ strtoupper(substr($product->title, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $product->title }}</p>
                            <div class="mt-2 flex items-center gap-3">
                                <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-600" style="width: {{ $p['percent'] }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-600 whitespace-nowrap">{{ $p['percent'] }}% ({{ $p['completed'] }}/{{ $p['total'] }})</span>
                            </div>
                        </div>
                        <a href="{{ route('learning.show', $product->slug) }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            {{ $p['percent'] >= 100 ? 'Lihat Lagi' : 'Lanjutkan' }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
