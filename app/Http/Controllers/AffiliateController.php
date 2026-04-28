<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserProduct;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    public function dashboard(): View
    {
        $userId = auth()->id();

        $ownedProductIds = UserProduct::where('user_id', $userId)->pluck('product_id');
        $products = Product::query()->whereIn('id', $ownedProductIds)->orderBy('title')->get();

        $statsByProduct = [];
        foreach ($products as $product) {
            $sales = Order::where('referred_by', $userId)
                ->where('product_id', $product->id)
                ->where('status', 'paid')
                ->count();

            $totalCommission = (float) Commission::where('earner_id', $userId)
                ->whereIn('order_id', Order::where('product_id', $product->id)->pluck('id'))
                ->sum('amount');

            $statsByProduct[$product->id] = [
                'sales' => $sales,
                'commission' => $totalCommission,
            ];
        }

        $commissionHistory = Commission::query()
            ->where('earner_id', $userId)
            ->with(['order.product'])
            ->latest()
            ->take(50)
            ->get();

        $leaderboard = DB::table('commissions')
            ->select('earner_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as penjualan'))
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->groupBy('earner_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $userIds = $leaderboard->pluck('earner_id')->all();
        $userNames = User::whereIn('id', $userIds)->pluck('name', 'id');
        foreach ($leaderboard as $row) {
            $row->name = $userNames[$row->earner_id] ?? 'Pengguna';
        }

        return view('affiliate.dashboard', [
            'products' => $products,
            'statsByProduct' => $statsByProduct,
            'commissionHistory' => $commissionHistory,
            'leaderboard' => $leaderboard,
        ]);
    }
}
