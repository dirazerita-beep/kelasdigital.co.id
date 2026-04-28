<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWithdrawalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $balance = (float) ($this->user()->balance ?? 0);

        return [
            'amount' => ['required', 'numeric', 'min:50000', 'max:'.$balance],
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_name' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.min' => 'Jumlah pencairan minimal Rp 50.000.',
            'amount.max' => 'Jumlah pencairan tidak boleh melebihi saldo Anda.',
        ];
    }
}
