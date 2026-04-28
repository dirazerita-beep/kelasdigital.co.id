<?php

namespace App\Http\Controllers;

use App\Models\LessonProgress;
use App\Models\Product;
use App\Models\UserProduct;
use Illuminate\Contracts\View\View;

class MemberProductController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $ownedIds = UserProduct::where('user_id', $userId)->pluck('product_id');
        $products = Product::query()
            ->whereIn('id', $ownedIds)
            ->with('sections.lessons:id,section_id')
            ->orderBy('title')
            ->get();

        $progressByProduct = [];
        foreach ($products as $product) {
            $progressByProduct[$product->id] = $this->productProgress($product, $userId);
        }

        return view('member.products', [
            'products' => $products,
            'progressByProduct' => $progressByProduct,
        ]);
    }

    private function productProgress(Product $product, int $userId): array
    {
        $lessonIds = $product->sections
            ->flatMap(fn ($s) => $s->lessons->pluck('id'))
            ->all();

        $total = count($lessonIds);
        if ($total === 0) {
            return ['total' => 0, 'completed' => 0, 'percent' => 0];
        }

        $completed = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $lessonIds)
            ->whereNotNull('completed_at')
            ->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'percent' => (int) floor(($completed / $total) * 100),
        ];
    }
}
