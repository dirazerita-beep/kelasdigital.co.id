@extends('layouts.admin')

@section('title', 'Pesanan')

@section('content')
    <header class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pesanan</h1>
            <p class="mt-1 text-sm text-gray-600">Daftar pesanan & konfirmasi pembayaran manual.</p>
        </div>
    </header>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Filter tabs --}}
    @php
        $tabs = [
            'all' => ['Semua', $counts['all']],
            'waiting' => ['Menunggu Konfirmasi', $counts['waiting']],
            'paid' => ['Aktif', $counts['paid']],
        ];
    @endphp
    <div class="mb-4 flex gap-2 border-b border-gray-200">
        @foreach ($tabs as $key => [$label, $count])
            <a href="{{ route('admin.orders', ['filter' => $key]) }}"
               class="px-4 py-2 text-sm font-medium border-b-2 -mb-px {{ $filter === $key ? 'border-blue-700 text-blue-700' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                {{ $label }}
                <span class="ml-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ $count }}</span>
            </a>
        @endforeach
    </div>

    @if ($orders->total() === 0)
        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-gray-500">
            Tidak ada pesanan untuk filter ini.
        </div>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">Member</th>
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
                            <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $order->midtrans_order_id }}</td>
                            <td class="px-4 py-3">{{ $order->user->name }}<br><span class="text-xs text-gray-500">{{ $order->user->email }}</span></td>
                            <td class="px-4 py-3">{{ $order->product->title }}</td>
                            <td class="px-4 py-3 font-semibold">{{ formatRupiah($order->amount) }}</td>
                            <td class="px-4 py-3 capitalize">{{ $order->payment_method }}</td>
                            <td class="px-4 py-3">
                                @if ($isPaid)
                                    <span class="inline-flex rounded-full bg-green-100 text-green-800 text-xs font-semibold px-2 py-0.5">Aktif</span>
                                @elseif ($isRejected)
                                    <span class="inline-flex rounded-full bg-red-100 text-red-800 text-xs font-semibold px-2 py-0.5">Ditolak</span>
                                @elseif ($isWaiting)
                                    <span class="inline-flex rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-0.5">Perlu Konfirmasi</span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-0.5">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                @if ($isWaiting)
                                    <div class="inline-flex gap-2">
                                        <form method="POST" action="{{ route('admin.orders.konfirmasi', $order->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="rounded-md bg-green-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-700">Konfirmasi</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.orders.tolak', $order->id) }}" class="inline"
                                              onsubmit="return confirm('Tolak pesanan ini?');">
                                            @csrf
                                            <button type="submit" class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Tolak</button>
                                        </form>
                                    </div>
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
