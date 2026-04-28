@php $lesson = $lesson ?? null; @endphp

<div class="grid gap-3 md:grid-cols-2">
    <div>
        <label class="block text-xs font-medium text-gray-700">Judul Lesson</label>
        <input type="text" name="title" value="{{ old('title', $lesson->title ?? '') }}" required
               class="mt-1 block w-full rounded-md border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700">Tipe</label>
        <select name="type" required class="mt-1 block w-full rounded-md border-gray-300 text-sm">
            @foreach (['video' => 'Video', 'file' => 'File', 'text' => 'Text'] as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $lesson->type ?? 'video') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700">YouTube ID</label>
        <input type="text" name="youtube_id" value="{{ old('youtube_id', $lesson->youtube_id ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700">Google Drive File ID</label>
        <input type="text" name="gdrive_file_id" value="{{ old('gdrive_file_id', $lesson->gdrive_file_id ?? '') }}"
               class="mt-1 block w-full rounded-md border-gray-300 text-sm">
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-medium text-gray-700">Konten (untuk tipe text)</label>
        <textarea name="content" rows="3" class="mt-1 block w-full rounded-md border-gray-300 text-sm">{{ old('content', $lesson->content ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700">Durasi (menit)</label>
        <input type="number" name="duration_minutes" min="0" value="{{ old('duration_minutes', $lesson->duration_minutes ?? 0) }}"
               class="mt-1 block w-full rounded-md border-gray-300 text-sm">
    </div>
</div>
