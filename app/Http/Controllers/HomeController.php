<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::query()
            ->where('status', 'active')
            ->latest()
            ->take(4)
            ->get();

        return view('home', [
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
