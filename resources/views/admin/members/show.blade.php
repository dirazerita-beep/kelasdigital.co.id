@extends('layouts.admin')

@section('title', 'Detail Member')

@section('content')
    <header class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $member->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $member->email }} · Member sejak {{ $member->created_at->format('d M Y') }}</p>
        </div>
        <a href="{{ route('admin.members.index') }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali</a>
    </header>

    <section class="grid gap-6 md:grid-cols-3">
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-xs font-medium text-gray-500 uppercase">Saldo Komisi</p>
            <p class="mt-2 text-2xl font-bold text-blue-700">{{ formatRupiah($member->balance) }}</p>
        </div>
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-xs font-medium text-gray-500 uppercase">Referrer</p>
            <p class="mt-2 text-base font-semibold text-gray-900">{{ $member->referrer?->name ?? '—' }}</p>
            @if ($member->referrer)
                <p class="text-xs text-gray-500">{{ $member->referrer->email }}</p>
            @endif
        </div>
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-xs font-medium text-gray-500 uppercase">Downline Langsung</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $member->referrals->count() }}</p>
        </div>
    </section>

    <section class="mt-6 rounded-lg bg-white border border-gray-200">
        <header class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Downline (Referral Tree Level 1)</h2>
        </header>
        @if ($member->referrals->isEmpty())
            <div class="p-6 text-sm text-gray-500">Belum ada downline.</div>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($member->referrals as $ref)
                    <li class="px-6 py-3 flex items-center justify-between text-sm">
                        <div>
                            <span class="font-medium text-gray-900">{{ $ref->name }}</span>
                            <span class="text-gray-500"> · {{ $ref->email }}</span>
                        </div>
                        <span class="text-xs text-gray-500">Bergabung {{ $ref->created_at->format('d M Y') }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="mt-6 rounded-lg bg-white border border-gray-200">
        <header class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Produk Dibeli</h2>
        </header>
        @if ($purchasedProducts->isEmpty())
            <div class="p-6 text-sm text-gray-500">Belum ada produk dibeli.</div>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($purchasedProducts as $up)
                    <li class="px-6 py-3 text-sm">
                        <span class="font-medium text-gray-900">{{ $up->product?->title ?? '—' }}</span>
                        <span class="ml-2 text-xs text-gray-500">{{ $up->created_at->format('d M Y') }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="mt-6 rounded-lg bg-white border border-gray-200">
        <header class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Riwayat Pembelian</h2>
        </header>
        @if ($orders->isEmpty())
            <div class="p-6 text-sm text-gray-500">Belum ada pembelian.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr><th class="px-4 py-3 text-left">Order ID</th><th class="px-4 py-3 text-left">Produk</th><th class="px-4 py-3 text-left">Jumlah</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-left">Tanggal</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($orders as $order)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs">{{ $order->midtrans_order_id }}</td>
                                <td class="px-4 py-3">{{ $order->product?->title ?? '-' }}</td>
                                <td class="px-4 py-3">{{ formatRupiah($order->amount) }}</td>
                                <td class="px-4 py-3 capitalize">{{ $order->status }}</td>
                                <td class="px-4 py-3 text-xs">{{ $order->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="mt-6 rounded-lg bg-white border border-gray-200">
        <header class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Riwayat Komisi</h2>
        </header>
        @if ($commissions->isEmpty())
            <div class="p-6 text-sm text-gray-500">Belum ada komisi.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr><th class="px-4 py-3 text-left">Tanggal</th><th class="px-4 py-3 text-left">Produk</th><th class="px-4 py-3 text-left">Level</th><th class="px-4 py-3 text-left">Jumlah</th><th class="px-4 py-3 text-left">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($commissions as $c)
                            <tr>
                                <td class="px-4 py-3 text-xs">{{ $c->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3">{{ $c->order?->product?->title ?? '-' }}</td>
                                <td class="px-4 py-3">L{{ $c->level }}</td>
                                <td class="px-4 py-3 font-semibold">{{ formatRupiah($c->amount) }}</td>
                                <td class="px-4 py-3 capitalize">{{ $c->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
