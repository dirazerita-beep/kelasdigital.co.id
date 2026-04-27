<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/produk', function () {
    return view('produk.index', [
        'products' => \App\Models\Product::where('status', 'active')->latest()->get(),
    ]);
})->name('products.index');

Route::get('/produk/{slug}', function (string $slug) {
    $product = \App\Models\Product::where('slug', $slug)->first();
    return view('produk.show', compact('product', 'slug'));
})->name('products.show');

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
