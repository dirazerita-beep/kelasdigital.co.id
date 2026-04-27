@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <div class="grid gap-6 md:grid-cols-3">
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Produk Dimiliki</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ auth()->user()->products()->count() }}</p>
        </div>
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Saldo Komisi</p>
            <p class="mt-2 text-3xl font-bold text-blue-700">Rp {{ number_format((float) auth()->user()->balance, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Total Referral</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ auth()->user()->referrals()->count() }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-lg bg-white border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-900">Selamat datang, {{ auth()->user()->name }}!</h2>
        <p class="mt-2 text-sm text-gray-600">Anda berhasil masuk. Gunakan menu di samping untuk mengakses produk, program afiliasi, dan pengaturan akun.</p>
    </div>
@endsection
