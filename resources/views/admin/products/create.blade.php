@extends('layouts.admin')

@section('title', 'Produk Baru')

@section('content')
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Produk Baru</h1>
        <p class="mt-1 text-sm text-gray-600">Isi data dasar produk. Section dan lesson bisa ditambahkan setelah produk dibuat.</p>
    </header>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="rounded-lg bg-white border border-gray-200 p-6">
        @csrf
        @include('admin.products._form')
    </form>
@endsection
