@extends('layouts.admin')

@section('title', 'Pengaturan Pembayaran')

@section('content')
    <div class="max-w-3xl">
        <header class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Pengaturan Pembayaran</h1>
            <p class="mt-1 text-sm text-gray-600">Pilih metode pembayaran dan atur info rekening / WhatsApp admin.</p>
        </header>

        @if (session('status'))
            <div class="mb-6 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}"
              x-data="{ method: '{{ old('payment_method', $payment_method) }}' }"
              class="space-y-6 rounded-lg border border-gray-200 bg-white p-6">
            @csrf

            <div>
                <label class="text-sm font-semibold text-gray-900">Metode Pembayaran</label>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <label class="flex items-start gap-3 rounded-md border p-4 cursor-pointer"
                           :class="method === 'manual' ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="payment_method" value="manual" x-model="method" class="mt-1">
                        <span>
                            <span class="block font-semibold text-gray-900">Transfer Manual</span>
                            <span class="block text-xs text-gray-500">Member transfer ke rekening, lalu konfirmasi via WhatsApp.</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-3 rounded-md border p-4 cursor-pointer"
                           :class="method === 'midtrans' ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="payment_method" value="midtrans" x-model="method" class="mt-1">
                        <span>
                            <span class="block font-semibold text-gray-900">Midtrans</span>
                            <span class="block text-xs text-gray-500">Otomatis: kartu, transfer, e-wallet via Snap.</span>
                        </span>
                    </label>
                </div>
            </div>

            {{-- Manual fields --}}
            <div x-show="method === 'manual'" class="space-y-4 border-t border-gray-100 pt-6">
                <h2 class="text-sm font-semibold text-gray-900">Info Rekening (untuk Transfer Manual)</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-medium text-gray-700">Nama Bank</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $bank_name) }}"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-700">Nomor Rekening</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $bank_account_number) }}"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-700">Nama Pemilik Rekening</label>
                    <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $bank_account_name) }}"
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>

            {{-- Midtrans fields --}}
            <div x-show="method === 'midtrans'" class="space-y-4 border-t border-gray-100 pt-6" x-cloak>
                <h2 class="text-sm font-semibold text-gray-900">Kredensial Midtrans</h2>
                <div>
                    <label class="text-xs font-medium text-gray-700">Server Key</label>
                    <input type="text" name="midtrans_server_key" value="{{ old('midtrans_server_key', $midtrans_server_key) }}"
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-700">Client Key</label>
                    <input type="text" name="midtrans_client_key" value="{{ old('midtrans_client_key', $midtrans_client_key) }}"
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm font-mono">
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="midtrans_is_production" value="1" {{ $midtrans_is_production === '1' ? 'checked' : '' }}>
                    Mode Produksi (uncheck untuk Sandbox)
                </label>
            </div>

            {{-- WhatsApp (always shown) --}}
            <div class="space-y-4 border-t border-gray-100 pt-6">
                <h2 class="text-sm font-semibold text-gray-900">Konfirmasi via WhatsApp</h2>
                <div>
                    <label class="text-xs font-medium text-gray-700">Nomor WhatsApp Admin <span class="text-gray-400">(format internasional, tanpa "+")</span></label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $whatsapp_number) }}" placeholder="6281234567890"
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-700">Template Pesan WA <span class="text-gray-400">(placeholders: {product}, {amount}, {name})</span></label>
                    <textarea name="whatsapp_message_template" rows="4"
                              class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ old('whatsapp_message_template', $whatsapp_message_template) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                <button type="submit" class="rounded-md bg-blue-700 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-800 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
