<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $now = now();
        $year = (int) $request->query('year', $now->year);
        $month = (int) $request->query('month', $now->month);

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $orders = Order::query()
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$start, $end])
            ->selectRaw('product_id, COUNT(*) as sold, SUM(amount) as revenue')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $commissions = Commission::query()
            ->whereBetween('commissions.created_at', [$start, $end])
            ->join('orders', 'orders.id', '=', 'commissions.order_id')
            ->selectRaw('orders.product_id as product_id, SUM(commissions.amount) as commission_total')
            ->groupBy('orders.product_id')
            ->get()
            ->keyBy('product_id');

        $rows = Product::query()
            ->orderBy('title')
            ->get()
            ->map(function (Product $product) use ($orders, $commissions) {
                $o = $orders->get($product->id);
                $c = $commissions->get($product->id);

                return [
                    'product' => $product,
                    'sold' => (int) ($o->sold ?? 0),
                    'revenue' => (float) ($o->revenue ?? 0),
                    'commission' => (float) ($c->commission_total ?? 0),
                ];
            });

        $totals = [
            'sold' => $rows->sum('sold'),
            'revenue' => $rows->sum('revenue'),
            'commission' => $rows->sum('commission'),
        ];

        $years = range($now->year - 4, $now->year);

        return view('admin.reports', [
            'year' => $year,
            'month' => $month,
            'rows' => $rows,
            'totals' => $totals,
            'years' => $years,
        ]);
    }
}
