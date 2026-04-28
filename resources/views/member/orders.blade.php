@extends('layouts.dashboard')

@section('title', 'Pesanan Saya')

@section('content')
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pesanan Saya</h1>
        <p class="mt-1 text-sm text-gray-600">Riwayat pesanan & status pembayaran.</p>
    </header>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($orders->total() === 0)
        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-gray-500">
            Belum ada pesanan. <a href="{{ route('products.index') }}" class="text-blue-700 hover:underline">Lihat katalog &rarr;</a>
        </div>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Metode</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($orders as $order)
                        @php
                            $isWaiting = $order->payment_method === 'manual' && $order->manual_status === 'waiting';
                            $isPaid = $order->status === 'paid';
                            $isRejected = $order->manual_status === 'rejected' || in_array($order->status, ['failed', 'expired'], true);
                        @endphp
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('products.show', $order->product->slug) }}" class="font-medium text-gray-900 hover:text-blue-700">{{ $order->product->title }}</a>
                                <p class="text-xs text-gray-500 font-mono">{{ $order->midtrans_order_id }}</p>
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ formatRupiah($order->amount) }}</td>
                            <td class="px-4 py-3 capitalize">{{ $order->payment_method }}</td>
                            <td class="px-4 py-3">
                                @if ($isPaid)
                                    <span class="inline-flex rounded-full bg-green-100 text-green-800 text-xs font-semibold px-2 py-0.5">Aktif</span>
                                @elseif ($isRejected)
                                    <span class="inline-flex rounded-full bg-red-100 text-red-800 text-xs font-semibold px-2 py-0.5">Ditolak</span>
                                @elseif ($isWaiting)
                                    <span class="inline-flex rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-0.5">Menunggu Konfirmasi</span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-0.5">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                @if ($isWaiting)
                                    <a href="{{ route('checkout.manual', $order->id) }}"
                                       class="inline-flex items-center rounded-md bg-green-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-700">
                                        Konfirmasi via WA
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $orders->onEachSide(1)->links() }}
        </div>
    @endif
@endsection
