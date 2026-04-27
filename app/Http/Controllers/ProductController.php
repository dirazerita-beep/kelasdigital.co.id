<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UserProduct;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->where('status', 'active')
            ->latest()
            ->paginate(12);

        return view('products.index', [
            'products' => $products,
        ]);
    }

    public function show(string $slug): View
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->with([
                'sections' => fn ($q) => $q->orderBy('order_index'),
                'sections.lessons' => fn ($q) => $q->orderBy('order_index'),
            ])
            ->firstOrFail();

        $hasBought = false;
        if ($user = auth()->user()) {
            $hasBought = UserProduct::query()
                ->where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->exists();
        }

        return view('products.show', [
            'product' => $product,
            'hasBought' => $hasBought,
        ]);
    }
}
