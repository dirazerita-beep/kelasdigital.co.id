<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * AdminSeeder
 *
 * Membuat satu akun admin awal untuk login pertama kali ke /admin.
 *
 * PENTING: Setelah login, segera ubah password via /profile.
 *
 * Cara jalankan (di server production):
 *   php artisan db:seed --class=AdminSeeder
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@kelasdigital.co.id'],
            [
                'name' => 'Admin KelasDigital',
                'password' => Hash::make('GantiPasswordIni123!'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'balance' => 0,
            ],
        );
    }
}
