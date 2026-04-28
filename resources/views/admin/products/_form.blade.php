@php
    $product = $product ?? null;
    $isEdit = (bool) $product;
@endphp

<div class="grid gap-6 md:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-gray-700">Judul</label>
        <input type="text" name="title" id="product-title"
               value="{{ old('title', $product->title ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Slug</label>
        <input type="text" name="slug" id="product-slug"
               value="{{ old('slug', $product->slug ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
        <p class="mt-1 text-xs text-gray-500">Hanya huruf kecil, angka, dan tanda hubung. Akan diisi otomatis dari judul.</p>
        @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6">
    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
    <textarea name="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>{{ old('description', $product->description ?? '') }}</textarea>
    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div class="mt-6 grid gap-6 md:grid-cols-3">
    <div>
        <label class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
        <input type="number" min="0" step="1" name="price"
               value="{{ old('price', $product?->price !== null ? (int) $product->price : '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
        @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Tipe</label>
        <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @foreach (['course' => 'Course', 'software' => 'Software', 'mixed' => 'Mixed'] as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $product->type ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Komisi (%)</label>
        <input type="number" min="0" max="100" step="0.01" name="commission_rate"
               value="{{ old('commission_rate', $product->commission_rate ?? '30') }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
        @error('commission_rate') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6 grid gap-6 md:grid-cols-2">
    <div>
        <label class="block text-sm font-medium text-gray-700">Preview YouTube ID</label>
        <input type="text" name="preview_youtube_id"
               value="{{ old('preview_youtube_id', $product->preview_youtube_id ?? '') }}"
               placeholder="dQw4w9WgXcQ"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('preview_youtube_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @foreach (['active' => 'Aktif', 'draft' => 'Draft'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $product->status ?? 'draft') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6">
    <label class="block text-sm font-medium text-gray-700">Thumbnail</label>
    @if ($isEdit && $product->thumbnail)
        <div class="mt-2 mb-2">
            <img src="{{ asset('storage/'.$product->thumbnail) }}" alt="thumbnail" class="h-24 w-40 rounded object-cover border border-gray-200">
        </div>
    @endif
    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm">
    <p class="mt-1 text-xs text-gray-500">JPEG, PNG, atau WEBP. Maksimal 2 MB.</p>
    @error('thumbnail') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div class="mt-8 flex items-center justify-end gap-3">
    <a href="{{ route('admin.products.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
    <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
        {{ $isEdit ? 'Simpan Perubahan' : 'Buat Produk' }}
    </button>
</div>

<script>
    (function () {
        const titleEl = document.getElementById('product-title');
        const slugEl = document.getElementById('product-slug');
        if (!titleEl || !slugEl) return;
        let userEdited = {{ $isEdit ? 'true' : 'false' }};
        slugEl.addEventListener('input', function () { userEdited = true; });
        titleEl.addEventListener('input', function () {
            if (userEdited) return;
            const v = titleEl.value.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            slugEl.value = v;
        });
    })();
</script>
