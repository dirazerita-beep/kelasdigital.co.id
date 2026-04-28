@extends('layouts.dashboard')

@section('title', $product->title . ($currentLesson ? ' · ' . $currentLesson->title : ''))

@section('content')
    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[320px,1fr]">
        {{-- KIRI: Sidebar kurikulum --}}
        <aside class="rounded-lg bg-white border border-gray-200 overflow-hidden self-start">
            <div class="p-5 border-b border-gray-200">
                <p class="text-xs font-medium text-blue-700 uppercase tracking-wide">Kurikulum</p>
                <h2 class="mt-1 text-base font-semibold text-gray-900">{{ $product->title }}</h2>
                <div class="mt-3">
                    <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                        <span>Progress</span>
                        <span class="font-semibold">{{ $progress['percent'] }}%</span>
                    </div>
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-600 transition-all" style="width: {{ $progress['percent'] }}%"></div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ $progress['completed'] }} dari {{ $progress['total'] }} lesson selesai</p>
                </div>
                @if ($isCompleted)
                    <a href="#" class="mt-4 flex items-center justify-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Unduh Sertifikat
                    </a>
                @endif
            </div>
            <nav class="p-3 space-y-3 max-h-[60vh] overflow-y-auto">
                @foreach ($product->sections as $section)
                    <div>
                        <p class="px-2 py-1 text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $section->title }}</p>
                        <ul class="mt-1 space-y-0.5">
                            @foreach ($section->lessons as $lesson)
                                @php
                                    $isComplete = in_array($lesson->id, $completedIds, true);
                                    $isActive = $currentLesson && $currentLesson->id === $lesson->id;
                                @endphp
                                <li>
                                    <a href="{{ route('learning.lesson', ['slug' => $product->slug, 'lesson_id' => $lesson->id]) }}"
                                       class="flex items-start gap-2 rounded-md px-2 py-2 text-sm {{ $isActive ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                        @if ($isComplete)
                                            <span class="mt-0.5 inline-flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-green-100 text-green-700">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                        @else
                                            <span class="mt-0.5 inline-flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full border-2 border-gray-300"></span>
                                        @endif
                                        <span class="flex-1">{{ $lesson->title }}</span>
                                        <span class="text-xs text-gray-400 uppercase">{{ $lesson->type }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>
        </aside>

        {{-- KANAN: Konten lesson --}}
        <section class="rounded-lg bg-white border border-gray-200 overflow-hidden">
            @if (! $currentLesson)
                <div class="p-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <p class="mt-3 text-sm">Pilih materi di sebelah kiri untuk mulai belajar.</p>
                </div>
            @else
                <header class="p-6 border-b border-gray-200">
                    <p class="text-xs uppercase tracking-wide text-blue-700 font-semibold">{{ $currentLesson->section->title ?? '' }}</p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ $currentLesson->title }}</h1>
                    @if ($currentLesson->duration_minutes > 0)
                        <p class="mt-1 text-xs text-gray-500">Durasi {{ $currentLesson->duration_minutes }} menit</p>
                    @endif
                </header>

                <div class="p-6 space-y-6">
                    @if ($currentLesson->type === 'video' && $currentLesson->youtube_id)
                        <div class="aspect-video rounded-md overflow-hidden bg-black">
                            <iframe class="h-full w-full"
                                    src="https://www.youtube.com/embed/{{ $currentLesson->youtube_id }}?rel=0&modestbranding=1&fs=0"
                                    title="{{ $currentLesson->title }}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        </div>
                    @elseif ($currentLesson->type === 'video')
                        <div class="rounded-md border border-dashed border-gray-300 p-8 text-center text-sm text-gray-500">Video belum tersedia.</div>
                    @endif

                    @if ($currentLesson->type === 'file')
                        @if ($currentLesson->gdrive_file_id)
                            <a href="{{ route('lesson.download', $currentLesson->id) }}"
                               class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/></svg>
                                Download File
                            </a>
                        @else
                            <div class="rounded-md border border-dashed border-gray-300 p-8 text-center text-sm text-gray-500">File belum tersedia.</div>
                        @endif
                    @endif

                    @if ($currentLesson->type === 'text')
                        <article class="prose max-w-none text-gray-800">
                            {!! $currentLesson->content ?: '<p class="text-gray-500">Konten teks belum tersedia.</p>' !!}
                        </article>
                    @endif

                    {{-- Tandai Selesai --}}
                    <div class="pt-4 border-t border-gray-200">
                        @if ($isLessonComplete)
                            <button type="button" disabled class="inline-flex items-center gap-2 rounded-md bg-green-100 px-5 py-2.5 text-sm font-semibold text-green-700 cursor-not-allowed">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                Sudah Diselesaikan
                            </button>
                        @else
                            <form method="POST" action="{{ route('lesson.complete', $currentLesson->id) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Tandai Selesai
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <footer class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    @if ($prevLessonId ?? null)
                        <a href="{{ route('learning.lesson', ['slug' => $product->slug, 'lesson_id' => $prevLessonId]) }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-blue-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Lesson Sebelumnya
                        </a>
                    @else
                        <span></span>
                    @endif
                    @if ($nextLessonId ?? null)
                        <a href="{{ route('learning.lesson', ['slug' => $product->slug, 'lesson_id' => $nextLessonId]) }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-blue-700">
                            Lesson Berikutnya
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span></span>
                    @endif
                </footer>
            @endif
        </section>
    </div>
@endsection
