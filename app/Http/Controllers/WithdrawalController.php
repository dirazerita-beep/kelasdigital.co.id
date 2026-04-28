<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWithdrawalRequest;
use App\Models\Withdrawal;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class WithdrawalController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $withdrawals = Withdrawal::query()
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('affiliate.withdrawal', [
            'balance' => (float) $user->balance,
            'withdrawals' => $withdrawals,
        ]);
    }

    public function store(StoreWithdrawalRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
            'account_name' => $data['account_name'],
            'status' => 'pending',
        ]);

        return redirect()->route('member.balance')
            ->with('status', 'Permintaan pencairan diajukan. Admin akan memprosesnya secepatnya.');
    }
}
