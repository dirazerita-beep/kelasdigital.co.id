<?php

namespace App\Http\Middleware;

use App\Models\Product;
use App\Models\ProductLesson;
use App\Models\UserProduct;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProductAccess
{
    /**
     * Handle an incoming request.
     *
     * Resolves the product from one of these route parameters:
     *   - `product` (Product model or id/slug)
     *   - `slug` (Product slug)
     *   - `lesson_id` (resolved via ProductLesson → section → product)
     *
     * Redirects to the product page with a flash error if the
     * authenticated user does not own the product yet.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $product = $request->route('product') ?? $request->route('slug');
        $lessonId = $request->route('lesson_id');

        if ($product instanceof Product) {
            $productId = $product->id;
            $productSlug = $product->slug;
        } elseif ($product !== null) {
            $resolved = is_numeric($product)
                ? Product::find($product)
                : Product::where('slug', $product)->first();

            if (! $resolved) {
                abort(404);
            }

            $productId = $resolved->id;
            $productSlug = $resolved->slug;
        } elseif ($lessonId !== null) {
            $lesson = ProductLesson::with('section.product')->find($lessonId);

            if (! $lesson || ! $lesson->section || ! $lesson->section->product) {
                abort(404);
            }

            $productId = $lesson->section->product->id;
            $productSlug = $lesson->section->product->slug;
        } else {
            abort(404);
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
