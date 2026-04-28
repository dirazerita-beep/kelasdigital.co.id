@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Laporan Penjualan</h1>
        <p class="mt-1 text-sm text-gray-600">Ringkasan penjualan, pendapatan, dan komisi per produk per bulan.</p>
    </header>

    <form method="GET" class="mb-6 flex flex-wrap items-end gap-3 rounded-lg bg-white border border-gray-200 p-4">
        <div>
            <label class="block text-xs font-medium text-gray-700">Bulan</label>
            <select name="month" class="mt-1 rounded-md border-gray-300 text-sm">
                @foreach (range(1, 12) as $m)
                    <option value="{{ $m }}" @selected($m === $month)>
                        {{ \Carbon\Carbon::create(null, $m, 1)->isoFormat('MMMM') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700">Tahun</label>
            <select name="year" class="mt-1 rounded-md border-gray-300 text-sm">
                @foreach ($years as $y)
                    <option value="{{ $y }}" @selected($y === $year)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Tampilkan</button>
    </form>

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3 text-right">Terjual</th>
                    <th class="px-4 py-3 text-right">Pendapatan</th>
                    <th class="px-4 py-3 text-right">Komisi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($rows as $row)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $row['product']->title }}</div>
                            <div class="text-xs text-gray-500 capitalize">{{ $row['product']->type }}</div>
                        </td>
                        <td class="px-4 py-3 text-right">{{ number_format($row['sold']) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ formatRupiah($row['revenue']) }}</td>
                        <td class="px-4 py-3 text-right text-blue-700">{{ formatRupiah($row['commission']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada produk untuk dilaporkan.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50 font-semibold">
                <tr>
                    <td class="px-4 py-3">Total</td>
                    <td class="px-4 py-3 text-right">{{ number_format($totals['sold']) }}</td>
                    <td class="px-4 py-3 text-right">{{ formatRupiah($totals['revenue']) }}</td>
                    <td class="px-4 py-3 text-right text-blue-700">{{ formatRupiah($totals['commission']) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
