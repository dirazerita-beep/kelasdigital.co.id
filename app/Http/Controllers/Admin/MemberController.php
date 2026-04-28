<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Models\User;
use App\Models\UserProduct;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(): View
    {
        $members = User::query()
            ->where('role', 'member')
            ->with('referrer:id,name')
            ->withCount(['products as products_count'])
            ->latest()
            ->paginate(20);

        return view('admin.members.index', ['members' => $members]);
    }

    public function show(int $id): View
    {
        $member = User::with(['referrer:id,name,email', 'referrals:id,name,email,referrer_id,created_at'])->findOrFail($id);

        $orders = Order::where('user_id', $member->id)
            ->with('product:id,title')
            ->latest()
            ->take(50)
            ->get();

        $commissions = Commission::where('earner_id', $member->id)
            ->with('order.product:id,title')
            ->latest()
            ->take(50)
            ->get();

        $purchasedProducts = UserProduct::where('user_id', $member->id)
            ->with('product:id,title,slug')
            ->get();

        return view('admin.members.show', [
            'member' => $member,
            'orders' => $orders,
            'commissions' => $commissions,
            'purchasedProducts' => $purchasedProducts,
        ]);
    }
}
