<?php

/*
|--------------------------------------------------------------------------
| Catatan Deploy ke Shared Hosting (kelasdigital.co.id)
|--------------------------------------------------------------------------
| Saat deploy ke shared hosting, isi folder `public/` ini akan dipindahkan
| ke `public_html/`, sementara seluruh source aplikasi (folder `app`,
| `bootstrap`, `config`, `routes`, `vendor`, dst.) akan diletakkan satu
| level di luar `public_html/` (mis. `/home/<cpanel-user>/kelasdigital/`).
|
| Karena itu, dua path require di bawah (`/../storage/framework/...` dan
| `/../vendor/autoload.php` serta `/../bootstrap/app.php`) HARUS diubah
| agar mengarah ke folder aplikasi di luar `public_html/`. Contoh:
|
|     require __DIR__.'/../kelasdigital/vendor/autoload.php';
|     (require_once __DIR__.'/../kelasdigital/bootstrap/app.php')
|         ->handleRequest(Request::capture());
|
| Sesuaikan nama foldernya dengan struktur shared hosting yang dipakai.
*/

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
