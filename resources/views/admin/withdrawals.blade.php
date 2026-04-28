@extends('layouts.admin')

@section('title', 'Pencairan')

@section('content')
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pencairan</h1>
        <p class="mt-1 text-sm text-gray-600">Setujui atau tolak permintaan pencairan saldo komisi member.</p>
    </header>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    @php
        $tabs = [
            'all' => ['Semua', $counts['all']],
            'pending' => ['Pending', $counts['pending']],
            'approved' => ['Disetujui', $counts['approved']],
            'rejected' => ['Ditolak', $counts['rejected']],
        ];
    @endphp
    <div class="mb-4 flex gap-2 border-b border-gray-200">
        @foreach ($tabs as $key => [$label, $count])
            <a href="{{ route('admin.withdrawals.index', ['filter' => $key]) }}"
               class="px-4 py-2 text-sm font-medium border-b-2 -mb-px {{ $filter === $key ? 'border-blue-700 text-blue-700' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                {{ $label }}
                <span class="ml-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ $count }}</span>
            </a>
        @endforeach
    </div>

    @if ($withdrawals->total() === 0)
        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-gray-500">
            Tidak ada pencairan untuk filter ini.
        </div>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3">Member</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Bank Tujuan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($withdrawals as $w)
                        <tr>
                            <td class="px-4 py-3">
                                {{ $w->user?->name ?? '-' }}<br>
                                <span class="text-xs text-gray-500">{{ $w->user?->email ?? '' }}</span>
                            </td>
                            <td class="px-4 py-3 font-semibold">{{ formatRupiah($w->amount) }}</td>
                            <td class="px-4 py-3">
                                <div>{{ $w->bank_name }}</div>
                                <div class="text-xs text-gray-500">{{ $w->account_number }} a.n. {{ $w->account_name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if ($w->status === 'approved')
                                    <span class="inline-flex rounded-full bg-green-100 text-green-800 text-xs font-semibold px-2 py-0.5">Disetujui</span>
                                @elseif ($w->status === 'rejected')
                                    <span class="inline-flex rounded-full bg-red-100 text-red-800 text-xs font-semibold px-2 py-0.5">Ditolak</span>
                                @else
                                    <span class="inline-flex rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-0.5">Pending</span>
                                @endif
                                @if ($w->admin_note)
                                    <div class="mt-1 text-xs text-gray-500">{{ $w->admin_note }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $w->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                @if ($w->status === 'pending')
                                    <form method="POST" action="{{ route('admin.withdrawals.update', $w->id) }}"
                                          class="inline-flex items-center gap-1"
                                          onsubmit="return confirm('Setujui pencairan ini? Saldo member akan dipotong sebesar jumlah pencairan.');">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="rounded-md bg-green-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-700">Setujui</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.withdrawals.update', $w->id) }}"
                                          class="inline-flex items-center gap-1 ml-1"
                                          onsubmit="return rejectWithdrawal(this);">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="admin_note" value="">
                                        <button type="submit" class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Tolak</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $withdrawals->onEachSide(1)->links() }}</div>
    @endif

    <script>
        function rejectWithdrawal(form) {
            const note = window.prompt('Alasan menolak pencairan ini? (opsional)');
            if (note === null) return false;
            form.querySelector('input[name=admin_note]').value = note;
            return true;
        }
    </script>
@endsection
