<x-mail::message>
# Selamat, {{ $user->name }}!

Kamu telah **menyelesaikan 100%** materi dari produk berikut:

**{{ $product->title }}**

Pencapaian luar biasa! Sekarang kamu bisa mengunduh sertifikat resmi sebagai bukti penyelesaianmu.

<x-mail::button :url="url('/sertifikat/'.$product->id)">
Unduh Sertifikat
</x-mail::button>

Teruslah belajar dan kembangkan keterampilan kamu di kelas-kelas berikutnya.

Salam hangat,<br>
{{ config('app.name') }}
</x-mail::message>
