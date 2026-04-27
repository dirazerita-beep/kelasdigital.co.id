@extends('layouts.app')

@section('title', $product->title . ' · KelasDigital')
@section('description', \Illuminate\Support\Str::limit(strip_tags($product->description ?? ''), 150))

@section('content')
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ route('products.index') }}" class="text-sm text-blue-700 hover:underline">&larr; Kembali ke daftar produk</a>

        <div class="mt-6 grid gap-10 lg:grid-cols-2">
            {{-- LEFT: media --}}
            <div>
                @if ($product->preview_youtube_id)
                    <div class="aspect-video w-full overflow-hidden rounded-lg border border-gray-200 bg-black">
                        <iframe class="h-full w-full"
                                src="https://www.youtube.com/embed/{{ $product->preview_youtube_id }}?rel=0&modestbranding=1"
                                title="{{ $product->title }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                    </div>
                @elseif ($product->thumbnail)
                    <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->title }}"
                         class="aspect-video w-full rounded-lg border border-gray-200 object-cover bg-gray-50">
                @else
                    <div class="aspect-video w-full rounded-lg border border-gray-200 bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                        <span class="px-6 text-center text-2xl font-bold text-blue-700">{{ $product->title }}</span>
                    </div>
                @endif
            </div>

            {{-- RIGHT: info + CTA --}}
            <div class="flex flex-col">
                <div class="flex items-center gap-2">
                    <span class="rounded bg-blue-50 text-blue-700 text-xs font-semibold uppercase tracking-wide px-2 py-0.5">
                        {{ $product->type === 'software' ? 'Software' : ($product->type === 'mixed' ? 'Mixed' : 'Kursus') }}
                    </span>
                    <span class="text-xs text-gray-500">Komisi {{ rtrim(rtrim((string) $product->commission_rate, '0'), '.') }}%</span>
                </div>
                <h1 class="mt-3 text-3xl md:text-4xl font-bold text-gray-900">{{ $product->title }}</h1>
                <div class="mt-4 text-gray-700 whitespace-pre-line">{{ $product->description }}</div>

                <div class="mt-6 rounded-lg border border-gray-200 bg-white p-5">
                    <p class="text-sm text-gray-500">Harga</p>
                    <p class="text-3xl font-bold text-blue-700">{{ formatRupiah($product->price) }}</p>

                    <div class="mt-5">
                        @guest
                            <a href="{{ route('login') }}"
                               class="inline-flex w-full items-center justify-center rounded-md bg-blue-700 px-6 py-3 text-base font-semibold text-white hover:bg-blue-800 transition">
                                Login untuk Membeli
                            </a>
                        @else
                            @if ($hasBought)
                                <a href="{{ url('/belajar/' . $product->slug) }}"
                                   class="inline-flex w-full items-center justify-center rounded-md bg-green-600 px-6 py-3 text-base font-semibold text-white hover:bg-green-700 transition">
                                    Lanjutkan Belajar
                                </a>
                            @else
                                <a href="{{ url('/checkout/' . $product->slug) }}"
                                   class="inline-flex w-full items-center justify-center rounded-md bg-blue-700 px-6 py-3 text-base font-semibold text-white hover:bg-blue-800 transition">
                                    Beli Sekarang
                                </a>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
        </div>

        {{-- Curriculum --}}
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900">Kurikulum</h2>
            <p class="mt-1 text-sm text-gray-600">{{ $product->sections->count() }} section · {{ $product->sections->sum(fn ($s) => $s->lessons->count()) }} lesson</p>

            @if ($product->sections->isNotEmpty())
                <div class="mt-6 space-y-3" id="curriculum">
                    @foreach ($product->sections as $i => $section)
                        <div class="rounded-lg border border-gray-200 bg-white">
                            <button type="button"
                                    class="curriculum-toggle flex w-full items-center justify-between gap-4 px-5 py-4 text-left"
                                    aria-expanded="{{ $i === 0 ? 'true' : 'false' }}"
                                    aria-controls="section-{{ $section->id }}">
                                <span class="flex items-center gap-3">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">{{ $i + 1 }}</span>
                                    <span class="font-semibold text-gray-900">{{ $section->title }}</span>
                                </span>
                                <span class="flex items-center gap-3 text-sm text-gray-500">
                                    <span>{{ $section->lessons->count() }} lesson</span>
                                    <svg class="curriculum-chevron h-5 w-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </span>
                            </button>
                            <div id="section-{{ $section->id }}"
                                 class="curriculum-body border-t border-gray-100 {{ $i === 0 ? '' : 'hidden' }}">
                                <ul class="divide-y divide-gray-100">
                                    @foreach ($section->lessons as $lesson)
                                        <li class="flex items-center gap-3 px-5 py-3 text-sm text-gray-700">
                                            @if ($lesson->type === 'video')
                                                <svg class="h-5 w-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @elseif ($lesson->type === 'file')
                                                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            @else
                                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                            @endif
                                            <span class="flex-1">{{ $lesson->title }}</span>
                                            @if ($lesson->duration_minutes > 0)
                                                <span class="text-xs text-gray-400">{{ $lesson->duration_minutes }} menit</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>

                <script>
                    (function () {
                        document.querySelectorAll('#curriculum .curriculum-toggle').forEach(function (btn) {
                            btn.addEventListener('click', function () {
                                var expanded = btn.getAttribute('aria-expanded') === 'true';
                                btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                                var bodyId = btn.getAttribute('aria-controls');
                                var body = document.getElementById(bodyId);
                                if (body) body.classList.toggle('hidden');
                                var chevron = btn.querySelector('.curriculum-chevron');
                                if (chevron) chevron.style.transform = expanded ? '' : 'rotate(180deg)';
                            });
                            // Initialize chevron rotation for the first/expanded section
                            if (btn.getAttribute('aria-expanded') === 'true') {
                                var chevron = btn.querySelector('.curriculum-chevron');
                                if (chevron) chevron.style.transform = 'rotate(180deg)';
                            }
                        });
                    })();
                </script>
            @else
                <div class="mt-6 rounded-lg border border-dashed border-gray-300 bg-white p-8 text-center text-gray-500">
                    Kurikulum belum tersedia.
                </div>
            @endif
        </div>
    </section>
@endsection
