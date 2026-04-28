<x-mail::message>
# Update Pencairan Dana

Hai **{{ $user->name }}**,

Permintaan pencairan dana kamu telah diproses oleh admin.

- **Status:** {{ ucfirst($withdrawal->status) }}
- **Jumlah:** {{ formatRupiah($withdrawal->amount) }}
- **Bank Tujuan:** {{ $withdrawal->bank_name }} — {{ $withdrawal->account_number }} a.n. {{ $withdrawal->account_name }}

@if (filled($withdrawal->admin_note))
**Catatan Admin:**
{{ $withdrawal->admin_note }}
@endif

@if ($withdrawal->status === 'approved')
Dana akan ditransfer ke rekening kamu dalam 1x24 jam kerja.
@elseif ($withdrawal->status === 'rejected')
Saldo kamu tidak dipotong dan kamu bisa mengajukan pencairan kembali setelah memperbaiki catatan di atas.
@endif

<x-mail::button :url="url('/saldo')">
Lihat Riwayat Pencairan
</x-mail::button>

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
