<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductLesson;
use App\Models\ProductSection;
use App\Models\User;
use App\Models\UserProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1) Admin user
        $admin = User::create([
            'name' => 'Admin KelasDigital',
            'email' => 'admin@kelasdigital.co.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 2) Five members. Member B is referred by Member A; the rest have no referrer.
        $memberA = User::create([
            'name' => 'Member A',
            'email' => 'member-a@kelasdigital.co.id',
            'password' => Hash::make('password123'),
            'role' => 'member',
            'referrer_id' => null,
            'email_verified_at' => now(),
        ]);

        $memberB = User::create([
            'name' => 'Member B',
            'email' => 'member-b@kelasdigital.co.id',
            'password' => Hash::make('password123'),
            'role' => 'member',
            'referrer_id' => $memberA->id,
            'email_verified_at' => now(),
        ]);

        foreach (['C', 'D', 'E'] as $letter) {
            User::create([
                'name' => "Member {$letter}",
                'email' => 'member-'.strtolower($letter).'@kelasdigital.co.id',
                'password' => Hash::make('password123'),
                'role' => 'member',
                'referrer_id' => null,
                'email_verified_at' => now(),
            ]);
        }

        // 3) Three products
        $productsData = [
            [
                'title' => 'Belajar Laravel dari Nol',
                'description' => 'Kursus lengkap belajar Laravel dari dasar hingga deploy ke production.',
                'price' => 299000,
                'type' => 'course',
                'status' => 'active',
                'commission_rate' => 30.00,
            ],
            [
                'title' => 'Panduan WordPress Lengkap',
                'description' => 'Panduan komprehensif menggunakan WordPress untuk membangun website profesional.',
                'price' => 199000,
                'type' => 'course',
                'status' => 'active',
                'commission_rate' => 25.00,
            ],
            [
                'title' => 'Software Generator Invoice',
                'description' => 'Software siap pakai untuk menerbitkan invoice profesional dengan cepat.',
                'price' => 149000,
                'type' => 'software',
                'status' => 'active',
                'commission_rate' => 20.00,
            ],
        ];

        $products = collect($productsData)->map(function (array $data) {
            return Product::create(array_merge($data, [
                'slug' => Str::slug($data['title']),
            ]));
        });

        // 4) Two sections per product, three lessons per section
        foreach ($products as $product) {
            for ($s = 1; $s <= 2; $s++) {
                $section = ProductSection::create([
                    'product_id' => $product->id,
                    'title' => "Section {$s} - {$product->title}",
                    'order_index' => $s,
                ]);

                for ($l = 1; $l <= 3; $l++) {
                    ProductLesson::create([
                        'section_id' => $section->id,
                        'title' => "Lesson {$l} (Section {$s}) - {$product->title}",
                        'type' => 'video',
                        'youtube_id' => 'dQw4w9WgXcQ',
                        'duration_minutes' => 10,
                        'order_index' => $l,
                    ]);
                }
            }
        }

        // 5) Member A buys product 1
        $firstProduct = $products->first();

        $order = Order::create([
            'user_id' => $memberA->id,
            'product_id' => $firstProduct->id,
            'referred_by' => null,
            'amount' => $firstProduct->price,
            'status' => 'paid',
            'midtrans_order_id' => 'ORDER-SEED-'.Str::upper(Str::random(8)),
            'midtrans_token' => null,
            'paid_at' => now(),
        ]);

        UserProduct::create([
            'user_id' => $memberA->id,
            'product_id' => $firstProduct->id,
            'order_id' => $order->id,
        ]);
    }
}
