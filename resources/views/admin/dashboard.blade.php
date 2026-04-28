@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="grid gap-6 md:grid-cols-4">
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Total Member</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalMembers) }}</p>
        </div>
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Pendapatan Bulan Ini</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ formatRupiah($monthlyRevenue) }}</p>
        </div>
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Komisi Bulan Ini</p>
            <p class="mt-2 text-3xl font-bold text-blue-600">{{ formatRupiah($monthlyCommission) }}</p>
        </div>
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Produk Aktif</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($activeProducts) }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-lg bg-white border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-900">Pendapatan 30 Hari Terakhir</h2>
            <span class="text-xs text-gray-500">Berdasarkan tanggal pembayaran</span>
        </div>
        <canvas id="revenueChart" height="80"></canvas>
    </div>

    <div class="mt-6 rounded-lg bg-white border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">10 Transaksi Terbaru</h2>
        </div>

        @if ($recentOrders->isEmpty())
            <div class="p-6 text-sm text-gray-500">Belum ada transaksi.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs uppercase tracking-wide text-gray-500">
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Member</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Jumlah</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $order->midtrans_order_id }}</td>
                                <td class="px-4 py-3">{{ $order->user?->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $order->product?->title ?? '-' }}</td>
                                <td class="px-4 py-3 font-semibold">{{ formatRupiah($order->amount) }}</td>
                                <td class="px-4 py-3">
                                    @if ($order->status === 'paid')
                                        <span class="inline-flex rounded-full bg-green-100 text-green-800 text-xs font-semibold px-2 py-0.5">Lunas</span>
                                    @elseif (in_array($order->status, ['failed', 'expired'], true))
                                        <span class="inline-flex rounded-full bg-red-100 text-red-800 text-xs font-semibold px-2 py-0.5">{{ ucfirst($order->status) }}</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-0.5">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const ctx = document.getElementById('revenueChart');
            if (!ctx) return;
            const labels = @json($chartLabels);
            const values = @json($chartValues);
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: values,
                        borderColor: '#1d4ed8',
                        backgroundColor: 'rgba(29, 78, 216, 0.08)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return 'Rp ' + Number(value).toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        })();
    </script>
@endsection
