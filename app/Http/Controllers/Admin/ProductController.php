<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductLesson;
use App\Models\ProductSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()->latest()->paginate(20);

        return view('admin.products', ['products' => $products]);
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['thumbnail'] = $this->handleThumbnail($request);

        $product = Product::create($data);

        return redirect()
            ->route('admin.products.edit', $product->id)
            ->with('status', 'Produk berhasil dibuat.');
    }

    public function edit(int $id): View
    {
        $product = Product::with(['sections.lessons'])->findOrFail($id);

        return view('admin.products.edit', ['product' => $product]);
    }

    public function update(StoreProductRequest $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            $data['thumbnail'] = $this->handleThumbnail($request);
        }

        $product->update($data);

        return redirect()
            ->route('admin.products.edit', $product->id)
            ->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        if ($product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
        }

        $product->delete();

        return redirect()
            ->route('admin.products')
            ->with('status', 'Produk berhasil dihapus.');
    }

    private function handleThumbnail(Request $request): ?string
    {
        if (! $request->hasFile('thumbnail')) {
            return null;
        }

        $file = $request->file('thumbnail');
        $name = Str::uuid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('thumbnails', $name, 'public');

        return $path;
    }

    // ============================================================
    // Section management
    // ============================================================

    public function storeSection(Request $request, int $productId): RedirectResponse
    {
        $product = Product::findOrFail($productId);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
        ]);

        $maxOrder = (int) $product->sections()->max('order_index');

        $product->sections()->create([
            'title' => $data['title'],
            'order_index' => $maxOrder + 1,
        ]);

        return back()->with('status', 'Section ditambahkan.');
    }

    public function updateSection(Request $request, int $productId, int $sectionId): RedirectResponse
    {
        $section = ProductSection::where('product_id', $productId)->findOrFail($sectionId);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
        ]);

        $section->update($data);

        return back()->with('status', 'Section diperbarui.');
    }

    public function destroySection(int $productId, int $sectionId): RedirectResponse
    {
        $section = ProductSection::where('product_id', $productId)->findOrFail($sectionId);
        $section->delete();

        return back()->with('status', 'Section dihapus.');
    }

    // ============================================================
    // Lesson management
    // ============================================================

    public function storeLesson(Request $request, int $productId, int $sectionId): RedirectResponse
    {
        $section = ProductSection::where('product_id', $productId)->findOrFail($sectionId);

        $data = $this->validateLesson($request);

        $maxOrder = (int) $section->lessons()->max('order_index');

        $section->lessons()->create($data + ['order_index' => $maxOrder + 1]);

        return back()->with('status', 'Lesson ditambahkan.');
    }

    public function updateLesson(Request $request, int $productId, int $sectionId, int $lessonId): RedirectResponse
    {
        $section = ProductSection::where('product_id', $productId)->findOrFail($sectionId);
        $lesson = ProductLesson::where('section_id', $section->id)->findOrFail($lessonId);

        $data = $this->validateLesson($request);

        $lesson->update($data);

        return back()->with('status', 'Lesson diperbarui.');
    }

    public function destroyLesson(int $productId, int $sectionId, int $lessonId): RedirectResponse
    {
        $section = ProductSection::where('product_id', $productId)->findOrFail($sectionId);
        $lesson = ProductLesson::where('section_id', $section->id)->findOrFail($lessonId);
        $lesson->delete();

        return back()->with('status', 'Lesson dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateLesson(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'type' => ['required', 'in:video,file,text'],
            'youtube_id' => ['nullable', 'string', 'max:50'],
            'gdrive_file_id' => ['nullable', 'string', 'max:100'],
            'content' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    // ============================================================
    // Drag-drop reorder (AJAX)
    // ============================================================

    public function reorderSections(Request $request, int $productId): JsonResponse
    {
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        DB::transaction(function () use ($productId, $data) {
            foreach ($data['order'] as $index => $sectionId) {
                ProductSection::where('product_id', $productId)
                    ->where('id', $sectionId)
                    ->update(['order_index' => $index + 1]);
            }
        });

        return response()->json(['ok' => true]);
    }

    public function reorderLessons(Request $request, int $productId, int $sectionId): JsonResponse
    {
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        $section = ProductSection::where('product_id', $productId)->findOrFail($sectionId);

        DB::transaction(function () use ($section, $data) {
            foreach ($data['order'] as $index => $lessonId) {
                ProductLesson::where('section_id', $section->id)
                    ->where('id', $lessonId)
                    ->update(['order_index' => $index + 1]);
            }
        });

        return response()->json(['ok' => true]);
    }
}
