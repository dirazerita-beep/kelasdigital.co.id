<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $balance = (float) $user->balance;

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:50000', 'max:'.$balance],
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_name' => ['required', 'string', 'max:100'],
        ], [
            'amount.min' => 'Jumlah pencairan minimal Rp 50.000.',
            'amount.max' => 'Jumlah pencairan tidak boleh melebihi saldo Anda.',
        ]);

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
