@extends('layouts.dashboard')

@section('title', 'Saldo & Pencairan')

@section('content')
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Saldo & Pencairan</h1>
        <p class="mt-1 text-sm text-gray-500">Cairkan komisi afiliasi Anda ke rekening bank pribadi.</p>
    </header>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Saldo card --}}
        <div class="lg:col-span-1">
            <div class="rounded-lg bg-blue-700 text-white p-6 shadow">
                <p class="text-xs uppercase tracking-wide text-blue-100">Saldo Tersedia</p>
                <p class="mt-2 text-3xl font-bold">{{ formatRupiah($balance) }}</p>
                <p class="mt-3 text-xs text-blue-100">Minimal pencairan Rp 50.000.</p>
            </div>
        </div>

        {{-- Form ajukan --}}
        <div class="lg:col-span-2">
            <div class="rounded-lg bg-white border border-gray-200 p-6">
                <h2 class="text-base font-semibold text-gray-900">Ajukan Pencairan</h2>
                @if ($balance < 50000)
                    <p class="mt-2 text-sm text-gray-600">Saldo Anda belum mencapai minimal pencairan (Rp 50.000). Bagikan link afiliasi untuk mengumpulkan komisi.</p>
                @else
                    <form method="POST" action="{{ route('member.balance.store') }}" class="mt-4 grid gap-4 sm:grid-cols-2">
                        @csrf
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700">Jumlah Pencairan (Rp)</label>
                            <input type="number" name="amount" min="50000" max="{{ (int) $balance }}" required value="{{ old('amount') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            @error('amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Nama Bank</label>
                            <input type="text" name="bank_name" required value="{{ old('bank_name') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            @error('bank_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Nomor Rekening</label>
                            <input type="text" name="account_number" required value="{{ old('account_number') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            @error('account_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700">Nama Pemilik Rekening</label>
                            <input type="text" name="account_name" required value="{{ old('account_name') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            @error('account_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <button type="submit" class="rounded-md bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                                Ajukan Pencairan
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Riwayat --}}
    <section class="mt-8 rounded-lg bg-white border border-gray-200 overflow-hidden">
        <header class="p-5 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Riwayat Pencairan</h2>
        </header>
        @if ($withdrawals->isEmpty())
            <div class="p-8 text-center text-sm text-gray-500">Belum ada pengajuan pencairan.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-right">Jumlah</th>
                            <th class="px-4 py-3 text-left">Bank</th>
                            <th class="px-4 py-3 text-left">No. Rekening</th>
                            <th class="px-4 py-3 text-left">Atas Nama</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($withdrawals as $w)
                            <tr>
                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $w->created_at?->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ formatRupiah($w->amount) }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $w->bank_name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $w->account_number }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $w->account_name }}</td>
                                <td class="px-4 py-3">
                                    @switch($w->status)
                                        @case('approved')
                                            <span class="inline-block rounded-full bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5">Disetujui</span>
                                            @break
                                        @case('rejected')
                                            <span class="inline-block rounded-full bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5">Ditolak</span>
                                            @break
                                        @default
                                            <span class="inline-block rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-0.5">Menunggu</span>
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
