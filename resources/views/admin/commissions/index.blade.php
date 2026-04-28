@extends('layouts.admin')

@section('title', 'Komisi')

@section('content')
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Komisi Afiliasi</h1>
        <p class="mt-1 text-sm text-gray-600">Daftar komisi referral per order, status pembayaran, dan total agregat.</p>
    </header>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-6">
        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-5">
            <div class="text-xs font-semibold uppercase tracking-wide text-yellow-700">Total Komisi Pending</div>
            <div class="mt-2 text-2xl font-bold text-yellow-900">{{ formatRupiah($totalPending) }}</div>
        </div>
        <div class="rounded-lg border border-green-200 bg-green-50 p-5">
            <div class="text-xs font-semibold uppercase tracking-wide text-green-700">Total Komisi Dibayar</div>
            <div class="mt-2 text-2xl font-bold text-green-900">{{ formatRupiah($totalPaid) }}</div>
        </div>
    </div>

    @php
        $tabs = [
            null => ['Semua', $counts['all']],
            'pending' => ['Pending', $counts['pending']],
            'paid' => ['Paid', $counts['paid']],
        ];
    @endphp
    <div class="mb-4 flex gap-2 border-b border-gray-200">
        @foreach ($tabs as $key => [$label, $count])
            <a href="{{ route('admin.commissions.index', $key ? ['status' => $key] : []) }}"
               class="px-4 py-2 text-sm font-medium border-b-2 -mb-px {{ ($status ?? null) === $key ? 'border-blue-700 text-blue-700' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                {{ $label }}
                <span class="ml-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ $count }}</span>
            </a>
        @endforeach
    </div>

    @if ($commissions->total() === 0)
        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-gray-500">
            Belum ada data komisi untuk filter ini.
        </div>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Earner</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Level</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($commissions as $c)
                        <tr>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $c->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium">{{ $c->earner?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $c->earner?->email ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $c->order?->product?->title ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold">{{ formatRupiah($c->amount) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5">
                                    L{{ $c->level }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($c->status === 'paid')
                                    <span class="inline-flex rounded-full bg-green-100 text-green-800 text-xs font-semibold px-2 py-0.5">Dibayar</span>
                                @else
                                    <span class="inline-flex rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-0.5">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if ($c->status === 'pending')
                                    <form method="POST" action="{{ route('admin.commissions.paid', $c->id) }}"
                                          onsubmit="return confirm('Tandai komisi ini sebagai sudah dibayar?');">
                                        @csrf
                                        <button type="submit"
                                                class="rounded-md bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700">
                                            Tandai Dibayar
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $commissions->links() }}
        </div>
    @endif
@endsection
