@extends('layouts.dashboard')

@section('title', 'Afiliasi')

@section('content')
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Afiliasi</h1>
        <p class="mt-1 text-sm text-gray-500">Dapatkan komisi setiap kali link Anda menghasilkan pembelian.</p>
    </header>

    {{-- BAGIAN 1: Link afiliasi per produk --}}
    <section class="mb-8">
        <h2 class="text-base font-semibold text-gray-900 mb-3">Link Afiliasi Anda</h2>
        @if ($products->isEmpty())
            <div class="rounded-lg border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-600">
                Anda perlu memiliki minimal satu produk untuk bisa membagikan link afiliasi.
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($products as $product)
                    @php
                        $stat = $statsByProduct[$product->id] ?? ['sales' => 0, 'commission' => 0];
                        $link = url('/ref/' . auth()->id() . '/' . $product->id);
                    @endphp
                    <article class="rounded-lg border border-gray-200 bg-white p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="font-semibold text-gray-900 truncate">{{ $product->title }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Komisi {{ rtrim(rtrim(number_format((float) $product->commission_rate, 2, ',', '.'), '0'), ',') }}% per penjualan</p>
                            </div>
                            <span class="text-xs uppercase tracking-wide text-blue-700 bg-blue-50 px-2 py-0.5 rounded font-medium flex-shrink-0">{{ $product->type === 'software' ? 'Software' : 'Kursus' }}</span>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-md bg-gray-50 p-3">
                                <p class="text-xs text-gray-500">Penjualan</p>
                                <p class="mt-0.5 text-lg font-bold text-gray-900">{{ $stat['sales'] }}</p>
                            </div>
                            <div class="rounded-md bg-gray-50 p-3">
                                <p class="text-xs text-gray-500">Total Komisi</p>
                                <p class="mt-0.5 text-lg font-bold text-blue-700">{{ formatRupiah($stat['commission']) }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <input type="text" readonly value="{{ $link }}" class="flex-1 rounded-md border-gray-300 bg-gray-50 text-xs text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            <button type="button"
                                    data-affiliate-link="{{ $link }}"
                                    class="copy-btn rounded-md bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                Copy Link
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    {{-- BAGIAN 2: Riwayat komisi --}}
    <section class="mb-8 rounded-lg bg-white border border-gray-200 overflow-hidden">
        <header class="p-5 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Riwayat Komisi</h2>
            <p class="text-xs text-gray-500 mt-0.5">50 komisi terbaru.</p>
        </header>
        @if ($commissionHistory->isEmpty())
            <div class="p-8 text-center text-sm text-gray-500">Belum ada komisi tercatat.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Produk</th>
                            <th class="px-4 py-3 text-left">Level</th>
                            <th class="px-4 py-3 text-right">Jumlah</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($commissionHistory as $com)
                            <tr>
                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $com->created_at?->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $com->order->product->title ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600">L{{ $com->level }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ formatRupiah($com->amount) }}</td>
                                <td class="px-4 py-3">
                                    @if ($com->status === 'paid')
                                        <span class="inline-block rounded-full bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5">Paid</span>
                                    @else
                                        <span class="inline-block rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-0.5">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- BAGIAN 3: Leaderboard --}}
    <section class="rounded-lg bg-white border border-gray-200 overflow-hidden">
        <header class="p-5 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Leaderboard Bulan Ini</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ now()->translatedFormat('F Y') }} · 10 affiliate teratas.</p>
        </header>
        @if ($leaderboard->isEmpty())
            <div class="p-8 text-center text-sm text-gray-500">Belum ada penjualan bulan ini.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="px-4 py-3 text-left w-16">#</th>
                            <th class="px-4 py-3 text-left">Affiliate</th>
                            <th class="px-4 py-3 text-right">Penjualan</th>
                            <th class="px-4 py-3 text-right">Total Komisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($leaderboard as $i => $row)
                            <tr class="{{ $row->earner_id === auth()->id() ? 'bg-blue-50' : '' }}">
                                <td class="px-4 py-3 font-bold text-gray-700">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $row->name }}@if ($row->earner_id === auth()->id()) <span class="text-xs text-blue-700 font-medium">(Anda)</span>@endif</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ $row->penjualan }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-blue-700">{{ formatRupiah((float) $row->total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.copy-btn');
            if (! btn) return;
            const link = btn.dataset.affiliateLink;
            if (! link) return;
            const restore = btn.textContent;
            const success = () => {
                btn.textContent = 'Tersalin!';
                btn.classList.add('bg-green-600');
                btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                setTimeout(() => {
                    btn.textContent = restore;
                    btn.classList.remove('bg-green-600');
                    btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }, 1500);
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(link).then(success).catch(() => {});
            } else {
                const tmp = document.createElement('textarea');
                tmp.value = link;
                document.body.appendChild(tmp);
                tmp.select();
                try { document.execCommand('copy'); success(); } catch (err) {}
                document.body.removeChild(tmp);
            }
        });
    </script>
@endsection
