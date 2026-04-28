@extends('layouts.app')

@section('title', 'Pembayaran Midtrans')

@section('content')
    <section class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-900">Pembayaran via Midtrans</h1>
        <p class="mt-2 text-gray-600">Order ID: <span class="font-mono">{{ $order->midtrans_order_id }}</span></p>

        <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6">
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-3xl font-bold text-blue-700">{{ formatRupiah($order->amount) }}</p>

            @if (! $clientKey)
                <div class="mt-6 rounded-md border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                    Snap belum aktif: <strong>Client Key Midtrans</strong> belum diisi di pengaturan admin.
                    Setelah admin mengisi credential, halaman ini akan menampilkan Snap pop-up otomatis.
                </div>
            @else
                <p class="mt-6 text-sm text-gray-600">Snap pop-up akan terbuka. Jika tidak muncul, klik tombol berikut.</p>
                <button id="snap-pay-btn" type="button"
                        class="mt-3 rounded-md bg-blue-700 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-800 transition">
                    Bayar dengan Snap
                </button>

                <script src="https://app.{{ $isProduction ? '' : 'sandbox.' }}midtrans.com/snap/snap.js"
                        data-client-key="{{ $clientKey }}"></script>
                <script>
                    document.getElementById('snap-pay-btn').addEventListener('click', function () {
                        // Snap token harus di-generate server-side oleh admin yang sudah memasang Midtrans SDK.
                        // Placeholder ini hanya menampilkan tombol; integrasi token request akan dilakukan saat credential aktif.
                        alert('Snap token belum di-generate. Hubungi admin untuk menyelesaikan integrasi server-side.');
                    });
                </script>
            @endif
        </div>

        <a href="{{ route('member.orders') }}" class="mt-6 inline-block text-sm text-gray-500 hover:text-blue-700">Lihat status pesanan &rarr;</a>
    </section>
@endsection
