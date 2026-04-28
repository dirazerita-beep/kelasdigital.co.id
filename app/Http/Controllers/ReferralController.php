<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\UserProduct;
use Illuminate\Http\RedirectResponse;

class ReferralController extends Controller
{
    /**
     * /ref/{user_id}/{product_id}
     *
     * Menyimpan referrer ke session HANYA jika:
     *   - User referrer ada dan berperan member.
     *   - Produk ada dan berstatus aktif.
     *   - Referrer SUDAH membeli produk tersebut (kepemilikan valid).
     *
     * Selalu redirect ke halaman produk agar UX tetap mulus walau link tidak
     * valid (mis. referrer belum beli).
     */
    public function capture(int $user_id, int $product_id): RedirectResponse
    {
        $referrer = User::find($user_id);
        $product = Product::find($product_id);

        if (! $product) {
            return redirect()->route('products.index');
        }

        if ($product->status !== 'active') {
            return redirect()->route('products.show', $product->slug);
        }

        $isValidReferrer = $referrer
            && UserProduct::query()
                ->where('user_id', $referrer->id)
                ->where('product_id', $product->id)
                ->exists();

        if ($isValidReferrer) {
            session(['referral.product_'.$product->id => $referrer->id]);
        }

        return redirect()->route('products.show', $product->slug);
    }
}
