<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = Commission::query()
            ->with(['earner:id,name,email', 'order.product:id,title,slug'])
            ->latest();

        if (in_array($status, ['pending', 'paid'], true)) {
            $query->where('status', $status);
        }

        $commissions = $query->paginate(20)->withQueryString();

        $totalPending = (float) Commission::where('status', 'pending')->sum('amount');
        $totalPaid = (float) Commission::where('status', 'paid')->sum('amount');

        $counts = [
            'all' => Commission::count(),
            'pending' => Commission::where('status', 'pending')->count(),
            'paid' => Commission::where('status', 'paid')->count(),
        ];

        return view('admin.commissions.index', [
            'commissions' => $commissions,
            'totalPending' => $totalPending,
            'totalPaid' => $totalPaid,
            'counts' => $counts,
            'status' => $status,
        ]);
    }

    public function markPaid(Commission $commission): RedirectResponse
    {
        if ($commission->status !== 'pending') {
            return back()->with('error', 'Komisi sudah ditandai sebelumnya.');
        }

        $commission->update(['status' => 'paid']);

        return back()->with('status', 'Komisi ditandai sudah dibayar.');
    }
}
