<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class ReferralController extends Controller
{
    /**
     * /ref/{user_id}/{product_id}
     * Stores referrer in session if both user (member) and product (active) exist.
     * Redirects to product detail in either case.
     */
    public function capture(int $user_id, int $product_id): RedirectResponse
    {
        $referrer = User::find($user_id);
        $product = Product::find($product_id);

        if ($referrer && $product && $product->status === 'active') {
            session(['referral.product_'.$product->id => $referrer->id]);
            return redirect()->route('products.show', $product->slug);
        }

        if ($product) {
            return redirect()->route('products.show', $product->slug);
        }

        return redirect()->route('products.index');
    }
}
