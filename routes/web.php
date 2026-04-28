<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\WithdrawalController as AdminWithdrawalController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\MemberOrderController;
use App\Http\Controllers\MemberProductController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\WithdrawalController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/produk-saya', [MemberProductController::class, 'index'])->name('member.products');

    Route::get('/afiliasi', [AffiliateController::class, 'dashboard'])->name('member.affiliate');

    Route::get('/saldo', [WithdrawalController::class, 'index'])->name('member.balance');
    Route::post('/saldo/ajukan', [WithdrawalController::class, 'store'])->name('member.balance.store');

    Route::get('/sertifikat/{product_id}', [CertificateController::class, 'generate'])
        ->whereNumber('product_id')
        ->name('certificate.generate');

    Route::get('/pesanan-saya', [MemberOrderController::class, 'index'])->name('member.orders');

    Route::get('/belajar/{slug}', [LearningController::class, 'show'])
        ->middleware('product.access')
        ->name('learning.show');
    Route::get('/belajar/{slug}/{lesson_id}', [LearningController::class, 'lesson'])
        ->middleware('product.access')
        ->whereNumber('lesson_id')
        ->name('learning.lesson');
    Route::post('/progress/{lesson_id}', [LearningController::class, 'markComplete'])
        ->whereNumber('lesson_id')
        ->name('lesson.complete');
    Route::get('/download/{lesson_id}', [DownloadController::class, 'download'])
        ->middleware('product.access')
        ->whereNumber('lesson_id')
        ->name('lesson.download');

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
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Produk
        Route::get('/produk', [AdminProductController::class, 'index'])->name('products');
        Route::get('/produk/baru', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/produk', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/produk/{id}/edit', [AdminProductController::class, 'edit'])
            ->whereNumber('id')->name('products.edit');
        Route::put('/produk/{id}', [AdminProductController::class, 'update'])
            ->whereNumber('id')->name('products.update');
        Route::delete('/produk/{id}', [AdminProductController::class, 'destroy'])
            ->whereNumber('id')->name('products.destroy');

        // Section
        Route::post('/produk/{id}/sections', [AdminProductController::class, 'storeSection'])
            ->whereNumber('id')->name('products.sections.store');
        Route::put('/produk/{id}/sections/{sectionId}', [AdminProductController::class, 'updateSection'])
            ->whereNumber(['id', 'sectionId'])->name('products.sections.update');
        Route::delete('/produk/{id}/sections/{sectionId}', [AdminProductController::class, 'destroySection'])
            ->whereNumber(['id', 'sectionId'])->name('products.sections.destroy');
        Route::post('/produk/{id}/sections/reorder', [AdminProductController::class, 'reorderSections'])
            ->whereNumber('id')->name('products.sections.reorder');

        // Lesson
        Route::post('/produk/{id}/sections/{sectionId}/lessons', [AdminProductController::class, 'storeLesson'])
            ->whereNumber(['id', 'sectionId'])->name('products.sections.lessons.store');
        Route::put('/produk/{id}/sections/{sectionId}/lessons/{lessonId}', [AdminProductController::class, 'updateLesson'])
            ->whereNumber(['id', 'sectionId', 'lessonId'])->name('products.sections.lessons.update');
        Route::delete('/produk/{id}/sections/{sectionId}/lessons/{lessonId}', [AdminProductController::class, 'destroyLesson'])
            ->whereNumber(['id', 'sectionId', 'lessonId'])->name('products.sections.lessons.destroy');
        Route::post('/produk/{id}/sections/{sectionId}/lessons/reorder', [AdminProductController::class, 'reorderLessons'])
            ->whereNumber(['id', 'sectionId'])->name('products.sections.lessons.reorder');

        // Member
        Route::get('/member', [AdminMemberController::class, 'index'])->name('members.index');
        Route::get('/member/{id}', [AdminMemberController::class, 'show'])
            ->whereNumber('id')->name('members.show');

        // Komisi (placeholder kept for sidebar compatibility)
        Route::get('/komisi', fn () => view('admin.commissions'))->name('commissions');

        // Pencairan
        Route::get('/pencairan', [AdminWithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/pencairan/{id}', [AdminWithdrawalController::class, 'update'])
            ->whereNumber('id')->name('withdrawals.update');

        // Laporan
        Route::get('/laporan', [AdminReportController::class, 'index'])->name('reports');

        // Pesanan
        Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders');
        Route::post('/pesanan/{id}/konfirmasi', [AdminOrderController::class, 'konfirmasi'])
            ->whereNumber('id')->name('orders.konfirmasi');
        Route::post('/pesanan/{id}/tolak', [AdminOrderController::class, 'tolak'])
            ->whereNumber('id')->name('orders.tolak');

        // Pengaturan
        Route::get('/pengaturan', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/pengaturan', [AdminSettingController::class, 'update'])->name('settings.update');
    });

require __DIR__.'/auth.php';
