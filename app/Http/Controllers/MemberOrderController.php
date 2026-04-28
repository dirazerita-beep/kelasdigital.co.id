<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\View\View;

class MemberOrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->where('user_id', auth()->id())
            ->with('product')
            ->latest()
            ->paginate(20);

        return view('member.orders', [
            'orders' => $orders,
            'whatsappNumber' => Setting::get('whatsapp_number', ''),
        ]);
    }
}
