<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
            ->with(['sections.lessons'])
            ->first();

        return view('products.show', [
            'product' => $product,
            'slug' => $slug,
        ]);
    }
}
