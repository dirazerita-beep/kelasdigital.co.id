@extends('layouts.admin')

@section('title', 'Kelola Member')

@section('content')
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Kelola Member</h1>
        <p class="mt-1 text-sm text-gray-600">Daftar member, referrer, jumlah produk dibeli, dan saldo komisi.</p>
    </header>

    @if ($members->total() === 0)
        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-gray-500">
            Belum ada member terdaftar.
        </div>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Referrer</th>
                        <th class="px-4 py-3">Produk Dibeli</th>
                        <th class="px-4 py-3">Saldo</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($members as $member)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $member->name }}</td>
                            <td class="px-4 py-3">{{ $member->email }}</td>
                            <td class="px-4 py-3">{{ $member->referrer?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $member->products_count }}</td>
                            <td class="px-4 py-3 font-semibold">{{ formatRupiah($member->balance) }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $member->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.members.show', $member->id) }}" class="text-blue-700 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $members->onEachSide(1)->links() }}</div>
    @endif
@endsection
