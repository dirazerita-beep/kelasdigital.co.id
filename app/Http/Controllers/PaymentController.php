<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use App\Models\UserProduct;
use App\Services\CommissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function notification(Request $request, CommissionService $commissions): JsonResponse
    {
        $payload = $request->all();

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signature = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (! $orderId || ! $statusCode || ! $grossAmount || ! $signature) {
            return response()->json(['ok' => false, 'message' => 'invalid payload'], 422);
        }

        $serverKey = (string) Setting::get('midtrans_server_key', '');
        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);
        if (! hash_equals($expected, $signature)) {
            return response()->json(['ok' => false, 'message' => 'invalid signature'], 403);
        }

        $order = Order::where('midtrans_order_id', $orderId)->first();
        if (! $order || $order->payment_method !== 'midtrans') {
            return response()->json(['ok' => false, 'message' => 'order not found'], 404);
        }

        if (in_array($transactionStatus, ['settlement', 'capture'], true)
            && (! $fraudStatus || $fraudStatus === 'accept')) {
            if ($order->status !== 'paid') {
                DB::transaction(function () use ($order) {
                    $order->update(['status' => 'paid', 'paid_at' => now()]);
                    UserProduct::firstOrCreate(
                        ['user_id' => $order->user_id, 'product_id' => $order->product_id],
                        ['order_id' => $order->id]
                    );
                });
                $commissions->calculate($order->fresh(['product', 'user']));
            }
        } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)) {
            $order->update(['status' => $transactionStatus === 'expire' ? 'expired' : 'failed']);
        }

        return response()->json(['ok' => true]);
    }
}
