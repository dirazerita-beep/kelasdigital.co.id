<?php

namespace App\Http\Middleware;

use App\Models\Product;
use App\Models\UserProduct;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProductAccess
{
    /**
     * Handle an incoming request.
     *
     * Expects a `product` route parameter (Product model or id/slug).
     * Redirects to the product page with a flash error if the
     * authenticated user does not own the product yet.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $product = $request->route('product');

        if ($product instanceof Product) {
            $productId = $product->id;
            $productSlug = $product->slug;
        } else {
            $resolved = is_numeric($product)
                ? Product::find($product)
                : Product::where('slug', $product)->first();

            if (! $resolved) {
                abort(404);
            }

            $productId = $resolved->id;
            $productSlug = $resolved->slug;
        }

        $owns = UserProduct::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->exists();

        if (! $owns) {
            return redirect()
                ->route('products.show', $productSlug)
                ->with('error', 'Anda belum membeli produk ini.');
        }

        return $next($request);
    }
}
