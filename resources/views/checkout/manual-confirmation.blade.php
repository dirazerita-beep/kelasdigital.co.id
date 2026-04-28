@extends('layouts.app')

@section('title', 'Konfirmasi Pembayaran')

@section('content')
    <section class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-900">Langkah Selanjutnya</h1>
        <p class="mt-2 text-gray-600">Transfer ke rekening berikut, lalu konfirmasi via WhatsApp untuk mengaktifkan akses.</p>

        {{-- Bank info box --}}
        <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-6">
            <p class="text-sm text-blue-900">Transfer ke rekening</p>
            <dl class="mt-3 space-y-2 text-blue-950">
                <div class="flex justify-between text-sm">
                    <dt>Nama Bank</dt>
                    <dd class="font-semibold">{{ $bankName ?: '—' }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt>Nomor Rekening</dt>
                    <dd class="font-mono font-semibold">{{ $bankAccountNumber ?: '—' }}</dd>
                </div>
                <div class="flex justify-between text-sm">
                    <dt>Nama Pemilik</dt>
                    <dd class="font-semibold">{{ $bankAccountName ?: '—' }}</dd>
                </div>
            </dl>
            <div class="mt-5 border-t border-blue-200 pt-4">
                <p class="text-xs uppercase tracking-wide text-blue-700">Jumlah yang harus ditransfer</p>
                <p class="mt-1 text-3xl font-extrabold text-blue-900">{{ formatRupiah($transferAmount) }}</p>
                <p class="mt-1 text-xs text-blue-800">
                    Termasuk kode unik <span class="font-mono font-semibold">{{ str_pad((string) $uniqueCode, 3, '0', STR_PAD_LEFT) }}</span>
                    (3 digit terakhir) — agar admin mudah memverifikasi pembayaran Anda.
                </p>
            </div>
        </div>

        <p class="mt-6 text-sm text-gray-600">
            Setelah transfer, klik tombol di bawah untuk konfirmasi via WhatsApp.
        </p>

        @if ($waUrl)
            <form method="POST" action="{{ route('checkout.manual.wa', $order->id) }}" class="mt-3">
                @csrf
                <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                   onclick="this.closest('form').submit()"
                   class="block w-full rounded-md bg-green-600 px-6 py-4 text-center text-base font-bold text-white hover:bg-green-700 transition">
                    Konfirmasi via WhatsApp
                </a>
            </form>
        @else
            <div class="mt-3 rounded-md border border-yellow-200 bg-yellow-50 p-3 text-sm text-yellow-800">
                Nomor WhatsApp admin belum diatur. Hubungi admin atau cek pengaturan.
            </div>
        @endif

        <div class="mt-6 rounded-md border border-gray-200 bg-white p-4 text-sm text-gray-600">
            Akses materi akan aktif setelah admin mengkonfirmasi pembayaran Anda.
        </div>

        <a href="{{ route('member.orders') }}"
           class="mt-6 inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Lihat Status Pesanan
        </a>
    </section>
@endsection
