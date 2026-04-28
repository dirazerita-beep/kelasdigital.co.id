<x-mail::message>
# Pembelian Berhasil

Hai **{{ $user->name }}**,

Terima kasih! Pembayaran kamu untuk produk berikut sudah kami terima dan akses materi sudah aktif.

- **Produk:** {{ $product->title }}
- **Harga:** {{ formatRupiah($order->amount) }}
- **Order ID:** {{ $order->midtrans_order_id }}

<x-mail::button :url="url('/belajar/'.$product->slug)">
Akses Materi Sekarang
</x-mail::button>

Selamat belajar dan semoga sukses!

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
