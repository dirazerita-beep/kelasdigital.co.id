<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmedMail;
use App\Models\Order;
use App\Models\UserProduct;
use App\Services\CommissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');

        $query = Order::query()->with(['user', 'product'])->latest();

        if ($filter === 'waiting') {
            $query->where('payment_method', 'manual')->where('manual_status', 'waiting');
        } elseif ($filter === 'paid') {
            $query->where('status', 'paid');
        }

        $orders = $query->paginate(20)->withQueryString();

        $counts = [
            'all' => Order::count(),
            'waiting' => Order::where('payment_method', 'manual')->where('manual_status', 'waiting')->count(),
            'paid' => Order::where('status', 'paid')->count(),
        ];

        return view('admin.orders', [
            'orders' => $orders,
            'filter' => $filter,
            'counts' => $counts,
        ]);
    }

    public function konfirmasi(int $id, CommissionService $commissions): RedirectResponse
    {
        $order = Order::with('product')->findOrFail($id);

        if ($order->status === 'paid') {
            return back()->with('status', 'Pesanan sudah aktif.');
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'paid',
                'manual_status' => 'confirmed',
                'paid_at' => now(),
            ]);

            UserProduct::firstOrCreate(
                ['user_id' => $order->user_id, 'product_id' => $order->product_id],
                ['order_id' => $order->id]
            );
        });

        $fresh = $order->fresh(['product', 'user']);
        $commissions->calculate($fresh);

        if ($fresh->user?->email) {
            Mail::to($fresh->user->email)->queue(new OrderConfirmedMail($fresh));
        }

        return back()->with('status', 'Pembayaran dikonfirmasi & akses produk diaktifkan.');
    }

    public function tolak(int $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        if ($order->status === 'paid') {
            return back()->with('error', 'Pesanan sudah aktif, tidak bisa ditolak.');
        }

        $order->update([
            'manual_status' => 'rejected',
            'status' => 'failed',
        ]);

        return back()->with('status', 'Pesanan ditolak.');
    }
}
