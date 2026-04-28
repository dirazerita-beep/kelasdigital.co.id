# Panduan Deploy ke Shared Hosting (cPanel)

Dokumen ini menjelaskan cara deploy aplikasi **KelasDigital** ke shared hosting cPanel.

> Asumsi: hosting mendukung **PHP 8.2+**, **MySQL 8 / MariaDB 10.4+**, **mod_rewrite**, dan akses **SSH** (cPanel Terminal). Tanpa SSH masih bisa, tapi beberapa perintah harus dijalankan via cPanel UI.

---

## 1. Struktur folder di cPanel

```
/home/<cpanel-user>/
├── laravel/                  ← seluruh source aplikasi (DI LUAR public_html!)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/               ← TIDAK dipakai di production (isinya sudah dipindah)
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   ├── artisan
│   └── composer.json
└── public_html/              ← document root (apa yang diakses publik)
    ├── index.php             ← copy dari laravel/public/index.shared.php (sudah di-rename)
    ├── .htaccess             ← copy dari laravel/public/.htaccess
    ├── favicon.ico
    ├── robots.txt
    └── storage/              ← symlink ke ../laravel/storage/app/public
```

**Kenapa source di luar `public_html`?** Supaya file `.env`, `vendor`, `storage`, dll. tidak bisa diakses langsung dari URL publik.

---

## 2. Persiapan di mesin lokal

```bash
# Install dependency production (tanpa dev tools)
composer install --no-dev --optimize-autoloader

# Build asset (jika repo pakai Vite)
# npm ci && npm run build

# Buat archive untuk diupload
tar -czf kelasdigital.tar.gz \
  --exclude='node_modules' \
  --exclude='tests' \
  --exclude='.git' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/data/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  .
```

---

## 3. Upload & extract di cPanel

1. Login ke **cPanel → File Manager**.
2. Upload `kelasdigital.tar.gz` ke `/home/<cpanel-user>/`.
3. Klik kanan archive → **Extract** ke folder `laravel/` (buat folder baru kalau belum ada).
4. Pastikan struktur jadi `/home/<cpanel-user>/laravel/app/`, `/home/<cpanel-user>/laravel/vendor/`, dst.

---

## 4. Pindahkan isi `public/` ke `public_html/`

Via **File Manager** atau **cPanel Terminal**:

```bash
cd ~/laravel/public
cp -R . ~/public_html/
```

Lalu **rename** front controller untuk shared hosting:

```bash
cd ~/public_html
mv index.shared.php index.php   # replace index.php standar
```

> Kalau perlu, edit `~/public_html/index.php` dan sesuaikan baris:
> ```php
> $laravelPath = __DIR__.'/../laravel';
> ```
> kalau folder source kamu bukan bernama `laravel`.

---

## 5. Buat symlink untuk storage publik (thumbnail, sertifikat, dll)

Via **cPanel Terminal**:

```bash
cd ~/public_html
ln -s ../laravel/storage/app/public storage
```

Atau via **File Manager** → New → Symbolic Link (target: `../laravel/storage/app/public`, link name: `storage`).

> Ini setara dengan `php artisan storage:link` di environment standar.

---

## 6. Setup `.env`

Copy `.env.example` jadi `.env`, lalu edit:

```bash
cd ~/laravel
cp .env.example .env
nano .env
```

Isi minimal yang **WAJIB**:

```
APP_NAME="KelasDigital"
APP_ENV=production
APP_KEY=                      # akan diisi otomatis di langkah berikutnya
APP_DEBUG=false
APP_URL=https://kelasdigital.co.id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=<nama_db_cpanel>
DB_USERNAME=<user_db_cpanel>
DB_PASSWORD=<password_db>

MAIL_MAILER=smtp
MAIL_HOST=mail.kelasdigital.co.id
MAIL_PORT=465
MAIL_USERNAME=no-reply@kelasdigital.co.id
MAIL_PASSWORD=<password_email>
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=no-reply@kelasdigital.co.id
MAIL_FROM_NAME="KelasDigital"

# Midtrans (opsional, kosongkan jika hanya pakai manual transfer)
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=true

QUEUE_CONNECTION=database     # default; bisa di-upgrade ke redis
```

Generate APP_KEY:

```bash
php artisan key:generate --force
```

---

## 7. Setup database

1. Di **cPanel → MySQL Databases**, buat database baru + user, kasih user `ALL PRIVILEGES`.
2. Update `.env` dengan kredensial tersebut.
3. Jalankan migrasi + seeder admin:

   ```bash
   cd ~/laravel
   php artisan migrate --force
   php artisan db:seed --class=AdminSeeder
   ```

   AdminSeeder membuat akun:
   - Email: `admin@kelasdigital.co.id`
   - Password: `GantiPasswordIni123!`

   **WAJIB ganti password ini setelah login pertama kali** (Profile → Update Password).

---

## 8. Set permission folder

```bash
cd ~/laravel
chmod -R 775 storage bootstrap/cache
# Pastikan owner = user cPanel (biasanya sudah otomatis)
```

---

## 9. Cache config & route

```bash
cd ~/laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

> Setiap kali `.env` atau file di `config/` berubah, jalankan `php artisan config:clear && php artisan config:cache` lagi.

---

## 10. Setup queue worker (untuk email notifikasi)

Aplikasi pakai queue untuk kirim email. Di shared hosting cPanel, gunakan **Cron Jobs** untuk jalankan scheduler tiap menit:

**cPanel → Cron Jobs → Add Cron Job**

- **Common Settings**: `Once Per Minute (* * * * *)`
- **Command**:
  ```
  cd /home/<cpanel-user>/laravel && php artisan schedule:run >> /dev/null 2>&1
  ```

Scheduler akan otomatis menjalankan `queue:work --stop-when-empty` setiap menit (sudah dikonfigurasi di `routes/console.php`).

---

## 11. Konfigurasi domain

Di **cPanel → Domains**, pastikan domain `kelasdigital.co.id` mengarah ke `/home/<cpanel-user>/public_html/`.

Aktifkan **SSL** via **Let's Encrypt** (gratis di cPanel modern), lalu uncomment baris HTTPS redirect di `public_html/.htaccess`:

```apache
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 12. Konfigurasi Midtrans webhook (jika dipakai)

Login ke **dashboard Midtrans → Settings → Configuration → Payment Notification URL**:

```
https://kelasdigital.co.id/payment/notification
```

(Route ini sudah di-exclude dari CSRF di `bootstrap/app.php`.)

---

## 13. Verifikasi deploy

1. Akses `https://kelasdigital.co.id/` → harus tampil halaman utama.
2. Akses `https://kelasdigital.co.id/admin` → redirect ke login.
3. Login pakai `admin@kelasdigital.co.id` / `GantiPasswordIni123!`.
4. **Langsung ganti password** di `/profile`.
5. Buat 1 produk uji + thumbnail → cek thumbnail muncul (jika tidak muncul, symlink storage langkah 5 belum benar).
6. Cek log error di `~/laravel/storage/logs/laravel.log` jika ada masalah.

---

## 14. Update aplikasi (deploy versi baru)

```bash
cd ~/laravel
# Backup database dulu via cPanel → Backup
# Backup .env
cp .env .env.backup

# Upload archive baru, extract, replace folder
# (atau pakai git pull kalau hosting punya git)

php artisan down
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
php artisan up
```

---

## Troubleshooting

| Masalah | Penyebab umum | Solusi |
|---|---|---|
| 500 Internal Server Error | Path di `index.php` salah / permission storage | Cek `storage/logs/laravel.log`; pastikan `chmod 775` |
| Asset CSS/JS tidak load | Vite belum di-build | `npm run build` di lokal, upload ulang `public/build/` |
| Thumbnail tidak muncul | Symlink storage belum dibuat | `ln -s ../laravel/storage/app/public storage` di `public_html/` |
| Email tidak terkirim | Cron schedule belum aktif / SMTP salah | Cek Cron Jobs aktif; test `php artisan tinker` → `Mail::raw(...)` |
| 419 Page Expired | APP_KEY berubah / cache config kotor | `php artisan config:clear && php artisan config:cache` |
| Login admin pertama kali gagal | AdminSeeder belum dijalankan | `php artisan db:seed --class=AdminSeeder` |

---

## Catatan Keamanan

- File `.env` **TIDAK BOLEH** ditaruh di `public_html/`.
- Pastikan `APP_DEBUG=false` di production.
- Backup database **berkala** via cPanel → Backup.
- Aktifkan **2FA** untuk akun cPanel.
- Monitor `storage/logs/laravel.log` untuk anomali.
