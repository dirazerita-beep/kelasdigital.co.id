<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        $totalMembers = User::where('role', 'member')->count();

        $monthlyRevenue = (float) Order::where('status', 'paid')
            ->whereBetween('paid_at', [$startOfMonth, $now])
            ->sum('amount');

        $monthlyCommission = (float) Commission::whereBetween('created_at', [$startOfMonth, $now])
            ->sum('amount');

        $activeProducts = Product::where('status', 'active')->count();

        // Last 30 days revenue line chart data
        $start = $now->copy()->subDays(29)->startOfDay();
        $rows = Order::query()
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$start, $now])
            ->selectRaw('DATE(paid_at) as d, SUM(amount) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        $labels = [];
        $values = [];
        for ($i = 0; $i < 30; $i++) {
            $day = $start->copy()->addDays($i);
            $key = $day->format('Y-m-d');
            $labels[] = $day->format('d M');
            $values[] = (float) ($rows[$key] ?? 0);
        }

        $recentOrders = Order::with(['user', 'product'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', [
            'totalMembers' => $totalMembers,
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyCommission' => $monthlyCommission,
            'activeProducts' => $activeProducts,
            'chartLabels' => $labels,
            'chartValues' => $values,
            'recentOrders' => $recentOrders,
        ]);
    }
}
