<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('products.show');

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
        Route::get('/pesanan', fn () => view('admin.orders'))->name('orders');
        Route::get('/komisi', fn () => view('admin.commissions'))->name('commissions');
        Route::get('/pencairan', fn () => view('admin.withdrawals'))->name('withdrawals');
    });

require __DIR__.'/auth.php';
