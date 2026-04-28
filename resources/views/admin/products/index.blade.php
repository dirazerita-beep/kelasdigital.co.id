@extends('layouts.admin')

@section('title', 'Kelola Produk')

@section('content')
    <header class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Produk</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola katalog produk, section, dan lesson.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">+ Produk Baru</a>
    </header>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
    @endif

    @if ($products->total() === 0)
        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-gray-500">
            Belum ada produk. <a href="{{ route('admin.products.create') }}" class="text-blue-700 underline">Buat produk pertama</a>.
        </div>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3">Thumbnail</th>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Tipe</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3">Komisi %</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($products as $product)
                        <tr>
                            <td class="px-4 py-3">
                                @if ($product->thumbnail)
                                    <img src="{{ asset('storage/'.$product->thumbnail) }}" alt="{{ $product->title }}" class="h-10 w-16 rounded object-cover">
                                @else
                                    <div class="h-10 w-16 rounded bg-gray-100"></div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $product->title }}</div>
                                <div class="text-xs text-gray-500">{{ $product->slug }}</div>
                            </td>
                            <td class="px-4 py-3 capitalize">{{ $product->type }}</td>
                            <td class="px-4 py-3 font-semibold">{{ formatRupiah($product->price) }}</td>
                            <td class="px-4 py-3">{{ rtrim(rtrim((string) $product->commission_rate, '0'), '.') }}%</td>
                            <td class="px-4 py-3">
                                @if ($product->status === 'active')
                                    <span class="inline-flex rounded-full bg-green-100 text-green-800 text-xs font-semibold px-2 py-0.5">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-0.5">Draft</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-700 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" class="inline"
                                      onsubmit="return confirm('Hapus produk ini? Section dan lesson akan ikut terhapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-2 text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $products->onEachSide(1)->links() }}</div>
    @endif
@endsection
