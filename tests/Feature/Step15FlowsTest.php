<?php

namespace Tests\Feature;

use App\Mail\CommissionEarnedMail;
use App\Mail\CourseCompletedMail;
use App\Mail\OrderConfirmedMail;
use App\Mail\WithdrawalProcessedMail;
use App\Models\LessonProgress;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductLesson;
use App\Models\ProductSection;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserProduct;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * End-to-end coverage untuk 10 skenario Step 15.
 *
 * Setiap test sengaja dibuat self-contained agar mudah dibaca dan
 * dijalankan satuan via:
 *   php artisan test --filter Step15FlowsTest::scenario_X_*
 */
class Step15FlowsTest extends TestCase
{
    use RefreshDatabase;

    private function ensurePaymentSettings(): void
    {
        Setting::set('payment_method', 'manual');
        Setting::set('bank_name', 'BCA');
        Setting::set('bank_account_number', '1234567890');
        Setting::set('bank_account_name', 'KelasDigital');
        Setting::set('whatsapp_number', '6281234567890');
        Setting::set('whatsapp_message_template', 'Halo Admin, saya transfer untuk {product} sebesar {amount}. Nama: {name}.');
    }

    private function makeProduct(array $attrs = []): Product
    {
        return Product::create(array_merge([
            'title' => 'Kelas Test',
            'slug' => 'kelas-test',
            'description' => 'Deskripsi kelas test.',
            'price' => 100000,
            'thumbnail' => null,
            'type' => 'course',
            'status' => 'active',
            'commission_rate' => 20,
            'preview_youtube_id' => null,
        ], $attrs));
    }

    private function makeMember(array $attrs = []): User
    {
        return User::create(array_merge([
            'name' => 'Member',
            'email' => 'member-'.uniqid().'@example.test',
            'password' => Hash::make('password'),
            'role' => 'member',
            'balance' => 0,
            'email_verified_at' => now(),
        ], $attrs));
    }

    private function makeAdmin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin-'.uniqid().'@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'balance' => 0,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Berikan kepemilikan produk ke user via Order paid sintetis.
     */
    private function grantOwnership(User $user, Product $product): UserProduct
    {
        $order = Order::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'amount' => $product->price,
            'status' => 'paid',
            'payment_method' => 'manual',
            'manual_status' => 'confirmed',
            'paid_at' => now(),
            'midtrans_order_id' => 'SEED-'.uniqid(),
        ]);

        return UserProduct::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => $order->id,
        ]);
    }

    /**
     * Skenario 1: Register member baru → login → dashboard.
     */
    public function test_scenario_1_register_login_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi',
            'email' => 'budi@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'budi@example.test')->first();
        $this->assertNotNull($user);
        $this->assertSame('member', $user->role);

        $this->get('/dashboard')->assertOk();
    }

    /**
     * Skenario 2: Beli produk via manual transfer → admin konfirmasi
     * → produk muncul di Produk Saya + email order-confirmed di-queue.
     */
    public function test_scenario_2_manual_purchase_and_admin_confirm(): void
    {
        Mail::fake();
        $this->ensurePaymentSettings();

        $product = $this->makeProduct();
        $member = $this->makeMember();
        $admin = $this->makeAdmin();

        $this->actingAs($member)->post('/checkout/'.$product->slug);

        $order = Order::where('user_id', $member->id)->where('product_id', $product->id)->firstOrFail();
        $this->assertSame('pending', $order->status);
        $this->assertSame('manual', $order->payment_method);
        $this->assertSame('waiting', $order->manual_status);

        $this->actingAs($admin)->post('/admin/pesanan/'.$order->id.'/konfirmasi')
            ->assertRedirect();

        $order->refresh();
        $this->assertSame('paid', $order->status);
        $this->assertSame('confirmed', $order->manual_status);

        $this->assertDatabaseHas('user_products', [
            'user_id' => $member->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($member)->get('/produk-saya')
            ->assertOk()
            ->assertSee($product->title);

        Mail::assertQueued(OrderConfirmedMail::class, fn ($m) => $m->hasTo($member->email));
    }

    /**
     * Skenario 3: Akses materi → tandai semua lesson selesai → email
     * course-completed di-queue + sertifikat dapat di-generate.
     */
    public function test_scenario_3_complete_all_lessons_and_certificate(): void
    {
        Mail::fake();

        $product = $this->makeProduct();
        $section = ProductSection::create(['product_id' => $product->id, 'title' => 'Bab 1', 'order_index' => 1]);
        $l1 = ProductLesson::create(['section_id' => $section->id, 'title' => 'Lesson 1', 'type' => 'video', 'order_index' => 1, 'youtube_id' => 'abc']);
        $l2 = ProductLesson::create(['section_id' => $section->id, 'title' => 'Lesson 2', 'type' => 'text', 'order_index' => 2, 'content' => 'isi']);

        $member = $this->makeMember();
        $this->grantOwnership($member, $product);

        $this->actingAs($member)->get('/belajar/'.$product->slug)->assertOk();
        $this->actingAs($member)->get('/belajar/'.$product->slug.'/'.$l1->id)->assertOk();

        $this->actingAs($member)->post('/progress/'.$l1->id)->assertRedirect();
        $this->actingAs($member)->post('/progress/'.$l2->id)->assertRedirect();

        $this->assertSame(2, LessonProgress::where('user_id', $member->id)->whereNotNull('completed_at')->count());

        Mail::assertQueued(CourseCompletedMail::class);

        $this->actingAs($member)
            ->get('/sertifikat/'.$product->id)
            ->assertOk();
    }

    /**
     * Skenario 4: Link afiliasi valid (referrer sudah beli) → session tersimpan.
     * Link afiliasi invalid (referrer belum beli) → session TIDAK tersimpan.
     */
    public function test_scenario_4_affiliate_link_validity(): void
    {
        $product = $this->makeProduct();
        $owner = $this->makeMember();
        $stranger = $this->makeMember();

        $this->grantOwnership($owner, $product);

        // Referrer valid (sudah beli) → session tersimpan.
        $this->get('/ref/'.$owner->id.'/'.$product->id)
            ->assertRedirect('/produk/'.$product->slug);
        $this->assertSame($owner->id, session('referral.product_'.$product->id));

        // Reset session.
        $this->flushSession();

        // Referrer invalid (belum beli) → session TIDAK tersimpan.
        $this->get('/ref/'.$stranger->id.'/'.$product->id)
            ->assertRedirect('/produk/'.$product->slug);
        $this->assertNull(session('referral.product_'.$product->id));
    }

    /**
     * Skenario 5: Member B beli via link Member A → komisi level 1 ke A.
     */
    public function test_scenario_5_level1_commission(): void
    {
        Mail::fake();
        $this->ensurePaymentSettings();

        $product = $this->makeProduct(['price' => 100000, 'commission_rate' => 20]);
        $a = $this->makeMember(['name' => 'Member A']);
        $this->grantOwnership($a, $product);

        $b = $this->makeMember(['name' => 'Member B']);
        $admin = $this->makeAdmin();

        // B klik link affiliasi A.
        $this->actingAs($b)->get('/ref/'.$a->id.'/'.$product->id);

        // B checkout produk (manual).
        $this->actingAs($b)->post('/checkout/'.$product->slug);
        $order = Order::where('user_id', $b->id)->firstOrFail();
        $this->assertSame($a->id, $order->referred_by);

        // Admin konfirmasi.
        $this->actingAs($admin)->post('/admin/pesanan/'.$order->id.'/konfirmasi');

        // 20% × 100000 = 20000.
        $this->assertDatabaseHas('commissions', [
            'order_id' => $order->id,
            'earner_id' => $a->id,
            'level' => 1,
            'amount' => '20000.00',
        ]);

        $a->refresh();
        $this->assertSame('20000.00', (string) $a->balance);

        Mail::assertQueued(CommissionEarnedMail::class, fn ($m) => $m->hasTo($a->email));
    }

    /**
     * Skenario 6: C beli via link B; B referrer-nya A → komisi masuk ke B
     * (level 1) DAN ke A (level 2 / grandparent).
     */
    public function test_scenario_6_level2_commission_cascade(): void
    {
        Mail::fake();
        $this->ensurePaymentSettings();

        $product = $this->makeProduct(['price' => 100000, 'commission_rate' => 20]);

        $a = $this->makeMember(['name' => 'Member A']);
        $this->grantOwnership($a, $product);

        $b = $this->makeMember(['name' => 'Member B', 'referrer_id' => $a->id]);
        $this->grantOwnership($b, $product);

        $c = $this->makeMember(['name' => 'Member C']);
        $admin = $this->makeAdmin();

        // C klik link affiliasi B, lalu beli.
        $this->actingAs($c)->get('/ref/'.$b->id.'/'.$product->id);
        $this->actingAs($c)->post('/checkout/'.$product->slug);
        $order = Order::where('user_id', $c->id)->firstOrFail();
        $this->assertSame($b->id, $order->referred_by);

        $this->actingAs($admin)->post('/admin/pesanan/'.$order->id.'/konfirmasi');

        // Level 1 ke B (20000).
        $this->assertDatabaseHas('commissions', [
            'order_id' => $order->id,
            'earner_id' => $b->id,
            'level' => 1,
            'amount' => '20000.00',
        ]);

        // Level 2 ke A (20000 — sama besar level 1 sesuai service).
        $this->assertDatabaseHas('commissions', [
            'order_id' => $order->id,
            'earner_id' => $a->id,
            'level' => 2,
            'amount' => '20000.00',
        ]);

        $a->refresh();
        $b->refresh();
        $this->assertSame('20000.00', (string) $a->balance);
        $this->assertSame('20000.00', (string) $b->balance);
    }

    /**
     * Skenario 7: Member ajukan pencairan → admin approve → balance berkurang.
     */
    public function test_scenario_7_withdrawal_request_and_approve(): void
    {
        Mail::fake();

        $member = $this->makeMember(['balance' => 100000]);
        $admin = $this->makeAdmin();

        $this->actingAs($member)->post('/saldo/ajukan', [
            'amount' => 75000,
            'bank_name' => 'BCA',
            'account_number' => '1112223334',
            'account_name' => 'Budi',
        ])->assertRedirect();

        $w = Withdrawal::where('user_id', $member->id)->firstOrFail();
        $this->assertSame('pending', $w->status);

        // Validasi minimal Rp 50.000.
        $this->actingAs($member)->post('/saldo/ajukan', [
            'amount' => 1000,
            'bank_name' => 'BCA',
            'account_number' => '1',
            'account_name' => 'Budi',
        ])->assertSessionHasErrors('amount');

        // Validasi tidak melebihi balance.
        $this->actingAs($member)->post('/saldo/ajukan', [
            'amount' => 999999999,
            'bank_name' => 'BCA',
            'account_number' => '1',
            'account_name' => 'Budi',
        ])->assertSessionHasErrors('amount');

        $this->actingAs($admin)->post('/admin/pencairan/'.$w->id, [
            'action' => 'approve',
            'admin_note' => 'OK',
        ])->assertRedirect();

        $member->refresh();
        $w->refresh();
        $this->assertSame('approved', $w->status);
        $this->assertSame('25000.00', (string) $member->balance);

        Mail::assertQueued(WithdrawalProcessedMail::class, fn ($m) => $m->hasTo($member->email));
    }

    /**
     * Skenario 8: Akses /belajar tanpa beli → redirect ke halaman produk
     * dengan flash error.
     */
    public function test_scenario_8_belajar_without_purchase_redirects(): void
    {
        $product = $this->makeProduct();
        $member = $this->makeMember();

        $this->actingAs($member)->get('/belajar/'.$product->slug)
            ->assertRedirect('/produk/'.$product->slug);
    }

    /**
     * Skenario 9: Akses /admin sebagai member → 403 (CheckRole middleware).
     */
    public function test_scenario_9_member_cannot_access_admin(): void
    {
        $member = $this->makeMember();

        $this->actingAs($member)->get('/admin')->assertForbidden();
        $this->actingAs($member)->get('/admin/produk')->assertForbidden();
        $this->actingAs($member)->get('/admin/laporan')->assertForbidden();
    }

    /**
     * Skenario 10: Buat produk baru di admin → upload thumbnail → aktifkan
     * → produk muncul di /produk publik.
     */
    public function test_scenario_10_admin_create_product_with_thumbnail(): void
    {
        Storage::fake('public');

        $admin = $this->makeAdmin();

        $payload = [
            'title' => 'Kelas Baru',
            'slug' => 'kelas-baru',
            'description' => 'Belajar coding dari nol.',
            'price' => 250000,
            'type' => 'course',
            'commission_rate' => 25,
            'status' => 'active',
            'thumbnail' => UploadedFile::fake()->image('thumb.png', 800, 600),
        ];

        $this->actingAs($admin)->post('/admin/produk', $payload)
            ->assertRedirectContains('/admin/produk/');

        $this->assertDatabaseHas('products', [
            'slug' => 'kelas-baru',
            'status' => 'active',
        ]);

        $product = Product::where('slug', 'kelas-baru')->firstOrFail();
        $this->assertNotNull($product->thumbnail);
        Storage::disk('public')->assertExists($product->thumbnail);

        $this->get('/produk')
            ->assertOk()
            ->assertSee('Kelas Baru');

        $this->get('/produk/kelas-baru')
            ->assertOk()
            ->assertSee('Kelas Baru');
    }

    /**
     * Skenario 10b: Validasi thumbnail tipe dan ukuran via StoreProductRequest.
     */
    public function test_scenario_10b_thumbnail_validation_rejects_pdf(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post('/admin/produk', [
            'title' => 'X',
            'slug' => 'kelas-x',
            'description' => 'desc',
            'price' => 100,
            'type' => 'course',
            'commission_rate' => 10,
            'status' => 'draft',
            'thumbnail' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('thumbnail');
    }
}
