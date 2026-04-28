<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\UserProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function show(string $slug): View|RedirectResponse
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $userId = auth()->id();
        $hasBought = UserProduct::query()
            ->where('user_id', $userId)
            ->where('product_id', $product->id)
            ->exists();

        if ($hasBought) {
            return redirect()->route('member.products')
                ->with('status', 'Produk ini sudah Anda miliki.');
        }

        return view('checkout.show', [
            'product' => $product,
            'paymentMethod' => Setting::get('payment_method', 'manual'),
            'bankName' => Setting::get('bank_name', ''),
            'bankAccountNumber' => Setting::get('bank_account_number', ''),
            'bankAccountName' => Setting::get('bank_account_name', ''),
        ]);
    }

    public function process(Request $request, string $slug): RedirectResponse
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $user = $request->user();

        $alreadyOwned = UserProduct::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();
        if ($alreadyOwned) {
            return redirect()->route('member.products');
        }

        $referrerId = session('referral.product_'.$product->id);

        $paymentMethod = Setting::get('payment_method', 'manual');

        $order = Order::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'referred_by' => $referrerId,
            'amount' => $product->price,
            'status' => 'pending',
            'payment_method' => $paymentMethod,
            'manual_status' => $paymentMethod === 'manual' ? 'waiting' : null,
            'midtrans_order_id' => 'ORDER-'.Str::upper(Str::random(10)).'-'.now()->format('YmdHis'),
        ]);

        if ($paymentMethod === 'manual') {
            return redirect()->route('checkout.manual', ['order' => $order->id]);
        }

        return redirect()->route('checkout.midtrans', ['order' => $order->id]);
    }

    public function manual(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($order->payment_method === 'manual', 404);

        $order->load('product', 'user');

        $template = Setting::get('whatsapp_message_template', '');
        $waNumber = preg_replace('/\D/', '', (string) Setting::get('whatsapp_number', ''));
        $uniqueCode = (int) ($order->id % 1000);
        $transferAmount = ((float) $order->amount) + $uniqueCode;

        $message = str_replace(
            ['{product}', '{amount}', '{name}'],
            [$order->product->title, formatRupiah($transferAmount), $order->user->name],
            $template
        );

        $waUrl = $waNumber !== ''
            ? 'https://wa.me/'.$waNumber.'?text='.rawurlencode($message)
            : null;

        return view('checkout.manual-confirmation', [
            'order' => $order,
            'product' => $order->product,
            'bankName' => Setting::get('bank_name', ''),
            'bankAccountNumber' => Setting::get('bank_account_number', ''),
            'bankAccountName' => Setting::get('bank_account_name', ''),
            'uniqueCode' => $uniqueCode,
            'transferAmount' => $transferAmount,
            'waUrl' => $waUrl,
        ]);
    }

    public function midtrans(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($order->payment_method === 'midtrans', 404);

        $order->load('product');

        return view('checkout.midtrans', [
            'order' => $order,
            'product' => $order->product,
            'clientKey' => Setting::get('midtrans_client_key', ''),
            'isProduction' => Setting::get('midtrans_is_production', '0') === '1',
        ]);
    }

    public function markWhatsappSent(Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 403);
        if (! $order->whatsapp_sent_at) {
            $order->whatsapp_sent_at = now();
            $order->save();
        }

        return back();
    }
}
