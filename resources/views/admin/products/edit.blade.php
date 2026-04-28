@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
    <header class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Produk</h1>
            <p class="mt-1 text-sm text-gray-600">Perbarui data produk, kelola section dan lesson.</p>
        </div>
        <a href="{{ route('admin.products') }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali</a>
    </header>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="rounded-lg bg-white border border-gray-200 p-6">
        @csrf
        @method('PUT')
        @include('admin.products._form', ['product' => $product])
    </form>

    <section class="mt-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Section &amp; Lesson</h2>
            <button type="button" onclick="document.getElementById('add-section-form').classList.toggle('hidden')"
                    class="rounded-md bg-blue-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-800">+ Tambah Section</button>
        </div>

        <form id="add-section-form" method="POST" action="{{ route('admin.products.sections.store', $product->id) }}"
              class="hidden mb-4 rounded-md border border-blue-200 bg-blue-50 p-4">
            @csrf
            <div class="flex gap-2">
                <input type="text" name="title" placeholder="Judul section..." required
                       class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
                <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Simpan</button>
            </div>
        </form>

        <ul id="sections-list" class="space-y-4" data-product-id="{{ $product->id }}">
            @foreach ($product->sections as $section)
                <li class="rounded-lg bg-white border border-gray-200" data-section-id="{{ $section->id }}">
                    <div class="px-4 py-3 flex items-center justify-between border-b border-gray-200 bg-gray-50 cursor-move section-handle">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400">&#x2630;</span>
                            <form method="POST" action="{{ route('admin.products.sections.update', [$product->id, $section->id]) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="title" value="{{ $section->title }}" class="rounded border-gray-300 text-sm" required>
                                <button type="submit" class="text-xs text-blue-700 hover:underline">Simpan</button>
                            </form>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="document.getElementById('add-lesson-{{ $section->id }}').classList.toggle('hidden')"
                                    class="text-xs text-blue-700 hover:underline">+ Lesson</button>
                            <form method="POST" action="{{ route('admin.products.sections.destroy', [$product->id, $section->id]) }}"
                                  onsubmit="return confirm('Hapus section ini? Semua lesson di dalamnya juga akan dihapus.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:underline">Hapus</button>
                            </form>
                        </div>
                    </div>

                    <form id="add-lesson-{{ $section->id }}" method="POST"
                          action="{{ route('admin.products.sections.lessons.store', [$product->id, $section->id]) }}"
                          class="hidden p-4 border-b border-gray-200 bg-blue-50">
                        @csrf
                        @include('admin.products._lesson_fields')
                        <div class="mt-3 flex justify-end">
                            <button type="submit" class="rounded-md bg-blue-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-800">Tambah Lesson</button>
                        </div>
                    </form>

                    <ul class="lessons-list divide-y divide-gray-100" data-section-id="{{ $section->id }}">
                        @foreach ($section->lessons as $lesson)
                            <li class="px-4 py-3 flex items-start justify-between gap-3 cursor-move lesson-handle" data-lesson-id="{{ $lesson->id }}">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400">&#x2630;</span>
                                        <span class="font-medium text-gray-900">{{ $lesson->title }}</span>
                                        <span class="text-xs text-gray-500 capitalize">[{{ $lesson->type }}]</span>
                                        @if ($lesson->duration_minutes)
                                            <span class="text-xs text-gray-500">{{ $lesson->duration_minutes }} menit</span>
                                        @endif
                                    </div>
                                    <details class="mt-2">
                                        <summary class="cursor-pointer text-xs text-blue-700">Edit lesson</summary>
                                        <form method="POST" action="{{ route('admin.products.sections.lessons.update', [$product->id, $section->id, $lesson->id]) }}" class="mt-2">
                                            @csrf
                                            @method('PUT')
                                            @include('admin.products._lesson_fields', ['lesson' => $lesson])
                                            <div class="mt-3 flex justify-end">
                                                <button type="submit" class="rounded-md bg-blue-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-800">Simpan</button>
                                            </div>
                                        </form>
                                    </details>
                                </div>
                                <form method="POST" action="{{ route('admin.products.sections.lessons.destroy', [$product->id, $section->id, $lesson->id]) }}"
                                      onsubmit="return confirm('Hapus lesson ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:underline">Hapus</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>

        @if ($product->sections->isEmpty())
            <div class="rounded-lg border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">
                Belum ada section. Klik "+ Tambah Section" di atas.
            </div>
        @endif
    </section>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        (function () {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const productId = {{ $product->id }};

            const sectionsList = document.getElementById('sections-list');
            if (sectionsList) {
                Sortable.create(sectionsList, {
                    handle: '.section-handle',
                    animation: 150,
                    onEnd: function () {
                        const order = Array.from(sectionsList.children).map(li => parseInt(li.dataset.sectionId, 10));
                        fetch(`{{ url('admin/produk') }}/${productId}/sections/reorder`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ order }),
                        });
                    }
                });
            }

            document.querySelectorAll('.lessons-list').forEach(function (ul) {
                const sectionId = parseInt(ul.dataset.sectionId, 10);
                Sortable.create(ul, {
                    handle: '.lesson-handle',
                    animation: 150,
                    onEnd: function () {
                        const order = Array.from(ul.children).map(li => parseInt(li.dataset.lessonId, 10));
                        fetch(`{{ url('admin/produk') }}/${productId}/sections/${sectionId}/lessons/reorder`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ order }),
                        });
                    }
                });
            });
        })();
    </script>
@endsection
