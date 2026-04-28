@extends('layouts.app')

@section('title', 'Checkout · ' . $product->title)

@section('content')
    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
        <p class="mt-1 text-sm text-gray-600">Periksa pesanan Anda sebelum melanjutkan pembayaran.</p>

        {{-- Order summary --}}
        <div class="mt-6 rounded-lg border border-gray-200 bg-white p-5">
            <div class="flex items-start gap-4">

            @if($product->thumbnail)
                <img src="{{ asset('storage/' . $product->thumbnail) }}"
                 class="h-20 w-32 shrink-0 rounded-md object-cover">
            @else
                 <div class="flex h-20 w-32 shrink-0 items-center justify-center rounded-md bg-gradient-to-br from-blue-100 to-indigo-100 text-center text-blue-700 text-sm font-semibold px-2">
                     {{ $product->title }}
                 </div>
            @endif
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $product->title }}</h2>
                    <p class="text-xs uppercase text-gray-500 tracking-wide">{{ $product->type === 'software' ? 'Software' : 'Kursus' }}</p>
                    <p class="mt-2 text-2xl font-bold text-blue-700">{{ formatRupiah($product->price) }}</p>
                </div>
            </div>
        </div>

        {{-- Payment method block --}}
        <div class="mt-6 rounded-lg border border-gray-200 bg-white p-5">
            @if ($paymentMethod === 'manual')
                <h3 class="text-sm font-semibold text-gray-900">Transfer Manual</h3>
                <p class="mt-1 text-xs text-gray-500">Setelah klik tombol di bawah, Anda akan mendapat instruksi transfer & link konfirmasi WhatsApp.</p>
                <dl class="mt-4 grid gap-3 sm:grid-cols-3 text-sm">
                    <div>
                        <dt class="text-xs uppercase text-gray-500">Bank</dt>
                        <dd class="font-semibold text-gray-900">{{ $bankName ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-gray-500">No. Rekening</dt>
                        <dd class="font-semibold text-gray-900">{{ $bankAccountNumber ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase text-gray-500">Atas Nama</dt>
                        <dd class="font-semibold text-gray-900">{{ $bankAccountName ?: '—' }}</dd>
                    </div>
                </dl>
            @else
                <h3 class="text-sm font-semibold text-gray-900">Pembayaran via Midtrans</h3>
                <p class="mt-1 text-xs text-gray-500">Bayar dengan kartu kredit, transfer bank, atau e-wallet. Pembayaran diproses otomatis.</p>
                <div class="mt-4 inline-flex items-center gap-2 rounded-md bg-gray-50 border border-gray-200 px-3 py-2 text-xs text-gray-600">
                    <span class="font-semibold text-gray-900">Midtrans</span> Snap · sandbox/production sesuai pengaturan admin.
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('checkout.process', $product->slug) }}" class="mt-6">
            @csrf
            <button type="submit"
                    class="w-full rounded-md bg-blue-700 px-6 py-3 text-base font-semibold text-white hover:bg-blue-800 transition">
                Lanjutkan Pembayaran
            </button>
        </form>

        <a href="{{ route('products.show', $product->slug) }}" class="mt-3 inline-block text-sm text-gray-500 hover:text-blue-700">&larr; Kembali ke detail produk</a>
    </section>
@endsection
