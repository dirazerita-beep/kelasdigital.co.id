<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\LessonProgress;
use App\Models\Product;
use App\Models\UserProduct;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $productsOwned = UserProduct::where('user_id', $user->id)->count();
        $totalCommission = (float) Commission::where('earner_id', $user->id)->sum('amount');
        $balance = (float) $user->balance;

        $ownedProductIds = UserProduct::where('user_id', $user->id)->pluck('product_id');
        $recent = Product::query()
            ->whereIn('id', $ownedProductIds)
            ->orderByDesc('id')
            ->take(3)
            ->get();

        $progressByProduct = [];
        foreach ($recent as $product) {
            $progressByProduct[$product->id] = $this->productProgress($product, $user->id);
        }

        return view('dashboard', [
            'productsOwned' => $productsOwned,
            'totalCommission' => $totalCommission,
            'balance' => $balance,
            'recentProducts' => $recent,
            'progressByProduct' => $progressByProduct,
        ]);
    }

    private function productProgress(Product $product, int $userId): array
    {
        $lessonIds = $product->sections()->with('lessons:id,section_id')->get()
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
