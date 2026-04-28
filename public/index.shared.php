<?php

/*
|--------------------------------------------------------------------------
| index.shared.php — Front Controller untuk Shared Hosting (cPanel)
|--------------------------------------------------------------------------
|
| File ini adalah pengganti `public/index.php` standar Laravel ketika
| aplikasi di-deploy ke shared hosting cPanel. Pada layout shared hosting:
|
|   /home/<cpanel-user>/
|     ├─ laravel/                       ← seluruh source Laravel
|     │   ├─ app/
|     │   ├─ bootstrap/
|     │   ├─ config/
|     │   ├─ database/
|     │   ├─ resources/
|     │   ├─ routes/
|     │   ├─ storage/
|     │   ├─ vendor/
|     │   └─ .env
|     └─ public_html/                   ← document root
|         ├─ index.php                  ← copy dari file ini, lalu rename
|         ├─ .htaccess                  ← copy dari public/.htaccess
|         ├─ favicon.ico
|         ├─ robots.txt
|         └─ storage/                   ← symlink ke ../laravel/storage/app/public
|                                          (dibuat via `php artisan storage:link`
|                                          atau symlink manual via cPanel)
|
| LANGKAH DEPLOY SINGKAT (lihat DEPLOY.md untuk panduan lengkap):
|
|   1. Upload seluruh folder Laravel ke `/home/<cpanel-user>/laravel/`.
|   2. Pindahkan isi `public/` ke `public_html/`.
|   3. Rename `public_html/index.shared.php` menjadi `public_html/index.php`
|      (replace file index.php standar yang ikut tercopy).
|   4. Sesuaikan konstanta `LARAVEL_PATH` di bawah jika nama folder berbeda
|      (default: `__DIR__.'/../laravel'`).
|   5. Buat symlink storage:
|        ln -s ../laravel/storage/app/public public_html/storage
|   6. Set permission `laravel/storage` dan `laravel/bootstrap/cache` 775.
|   7. Edit `laravel/.env` (APP_URL, DB_*, MAIL_*, dll).
|   8. Jalankan via SSH (atau cPanel Terminal):
|        cd ~/laravel
|        php artisan migrate --force
|        php artisan db:seed --class=AdminSeeder
|        php artisan config:cache
|        php artisan route:cache
|        php artisan view:cache
|
*/

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/**
 * Path absolut ke folder Laravel di luar public_html.
 *
 * GANTI nilai ini jika folder Laravel kamu tidak bernama `laravel`
 * (misal `kelasdigital` atau `app`).
 */
$laravelPath = __DIR__.'/../laravel';

// Maintenance mode (file digenerate oleh `php artisan down`).
if (file_exists($maintenance = $laravelPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Composer autoloader.
require $laravelPath.'/vendor/autoload.php';

// Bootstrap Laravel & handle request.
(require_once $laravelPath.'/bootstrap/app.php')
    ->handleRequest(Request::capture());
