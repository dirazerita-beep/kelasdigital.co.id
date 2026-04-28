<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WithdrawalProcessedMail;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');

        $query = Withdrawal::with('user')->latest();

        if (in_array($filter, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $filter);
        }

        $withdrawals = $query->paginate(20)->withQueryString();

        $counts = [
            'all' => Withdrawal::count(),
            'pending' => Withdrawal::where('status', 'pending')->count(),
            'approved' => Withdrawal::where('status', 'approved')->count(),
            'rejected' => Withdrawal::where('status', 'rejected')->count(),
        ];

        return view('admin.withdrawals', [
            'withdrawals' => $withdrawals,
            'counts' => $counts,
            'filter' => $filter,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        $withdrawal = Withdrawal::with('user')->findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Pencairan ini sudah diproses.');
        }

        if ($data['action'] === 'approve') {
            $amount = (float) $withdrawal->amount;

            DB::transaction(function () use ($withdrawal, $amount, $data) {
                $balance = (float) ($withdrawal->user?->balance ?? 0);
                if ($balance < $amount) {
                    throw new \RuntimeException('Saldo member tidak mencukupi.');
                }

                User::where('id', $withdrawal->user_id)->decrement('balance', $amount);

                $withdrawal->update([
                    'status' => 'approved',
                    'admin_note' => $data['admin_note'] ?? null,
                    'processed_at' => now(),
                ]);
            });
        } else {
            $withdrawal->update([
                'status' => 'rejected',
                'admin_note' => $data['admin_note'] ?? null,
                'processed_at' => now(),
            ]);
        }

        $withdrawal->refresh()->loadMissing('user');

        if ($withdrawal->user?->email) {
            Mail::to($withdrawal->user->email)->queue(new WithdrawalProcessedMail($withdrawal));
        }

        return back()->with('status', 'Permintaan pencairan berhasil di-'.($data['action'] === 'approve' ? 'setujui' : 'tolak').'.');
    }
}
