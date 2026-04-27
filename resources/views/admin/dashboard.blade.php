@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="grid gap-6 md:grid-cols-4">
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Total Member</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ \App\Models\User::where('role', 'member')->count() }}</p>
        </div>
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Total Produk</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ \App\Models\Product::count() }}</p>
        </div>
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Pesanan Lunas</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ \App\Models\Order::where('status', 'paid')->count() }}</p>
        </div>
        <div class="rounded-lg bg-white border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Pencairan Pending</p>
            <p class="mt-2 text-3xl font-bold text-red-600">{{ \App\Models\Withdrawal::where('status', 'pending')->count() }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center">
        <p class="text-sm text-gray-500">Ringkasan grafik &amp; aktivitas terbaru akan tampil di sini.</p>
        <p class="mt-2 text-xs text-gray-400">(Placeholder — akan diisi oleh <code>Admin\DashboardController@index</code>.)</p>
    </div>
@endsection
