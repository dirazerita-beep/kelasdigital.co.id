<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Calculate and persist 2-level commissions for the given paid order.
     * Level 1: direct referrer of the buyer (or order's referred_by).
     * Level 2: referrer of the level-1 earner (grandparent).
     *
     * Commission amount for both levels uses the same product.commission_rate
     * applied to the order amount.
     */
    public function calculate(Order $order): void
    {
        $order->loadMissing(['user', 'product']);

        $earnerId = $order->referred_by ?? optional($order->user)->referrer_id;
        if (! $earnerId) {
            return;
        }

        $rate = (float) ($order->product->commission_rate ?? 0);
        if ($rate <= 0) {
            return;
        }

        $level1Amount = round(((float) $order->amount) * ($rate / 100), 2);

        DB::transaction(function () use ($order, $earnerId, $rate, $level1Amount) {
            Commission::create([
                'order_id' => $order->id,
                'earner_id' => $earnerId,
                'amount' => $level1Amount,
                'level' => 1,
                'status' => 'pending',
            ]);

            User::where('id', $earnerId)->increment('balance', $level1Amount);

            $level1User = User::find($earnerId);
            if ($level1User && $level1User->referrer_id) {
                $level2Amount = $level1Amount;

                if ($level2Amount > 0) {
                    Commission::create([
                        'order_id' => $order->id,
                        'earner_id' => $level1User->referrer_id,
                        'amount' => $level2Amount,
                        'level' => 2,
                        'status' => 'pending',
                    ]);

                    User::where('id', $level1User->referrer_id)->increment('balance', $level2Amount);
                }
            }
        });
    }
}
