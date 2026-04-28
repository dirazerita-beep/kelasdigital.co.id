<?php

use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberOrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferralController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/ref/{user_id}/{product_id}', [ReferralController::class, 'capture'])
    ->whereNumber(['user_id', 'product_id'])
    ->name('referral.capture');

Route::post('/payment/notification', [PaymentController::class, 'notification'])
    ->name('payment.notification');

/*
|--------------------------------------------------------------------------
| Authenticated member routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/member/produk-saya', fn () => view('member.products'))->name('member.products');
    Route::get('/member/afiliasi', fn () => view('member.affiliate'))->name('member.affiliate');
    Route::get('/member/saldo', fn () => view('member.balance'))->name('member.balance');

    Route::get('/pesanan-saya', [MemberOrderController::class, 'index'])->name('member.orders');

    Route::get('/checkout/{slug}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/{slug}', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/manual/{order}', [CheckoutController::class, 'manual'])->name('checkout.manual');
    Route::post('/checkout/manual/{order}/wa', [CheckoutController::class, 'markWhatsappSent'])->name('checkout.manual.wa');
    Route::get('/checkout/midtrans/{order}', [CheckoutController::class, 'midtrans'])->name('checkout.midtrans');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin routes (require role:admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');
        Route::get('/produk', fn () => view('admin.products'))->name('products');
        Route::get('/member', fn () => view('admin.members'))->name('members');
        Route::get('/komisi', fn () => view('admin.commissions'))->name('commissions');
        Route::get('/pencairan', fn () => view('admin.withdrawals'))->name('withdrawals');

        Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders');
        Route::post('/pesanan/{id}/konfirmasi', [AdminOrderController::class, 'konfirmasi'])
            ->whereNumber('id')->name('orders.konfirmasi');
        Route::post('/pesanan/{id}/tolak', [AdminOrderController::class, 'tolak'])
            ->whereNumber('id')->name('orders.tolak');

        Route::get('/pengaturan', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/pengaturan', [AdminSettingController::class, 'update'])->name('settings.update');
    });

require __DIR__.'/auth.php';
