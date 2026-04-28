<x-mail::message>
# Komisi Masuk

Hai **{{ $earner->name }}**,

Selamat! Kamu baru saja mendapatkan komisi afiliasi.

- **Produk Terjual:** {{ $product?->title ?? '-' }}
- **Jumlah Komisi:** {{ formatRupiah($commission->amount) }}
- **Level:** {{ $commission->level }}
- **Saldo Sekarang:** {{ formatRupiah($balance) }}

<x-mail::button :url="url('/afiliasi')">
Lihat Dashboard Afiliasi
</x-mail::button>

Terus semangat membagikan link referral kamu!

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
