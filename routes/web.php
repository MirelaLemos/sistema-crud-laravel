<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

Route::get('/', fn() => view('welcome'));

Route::get('/dashboard', function () {
    return redirect()->route('products.index'); // ou outra rota já existente
})->middleware(['auth'])->name('dashboard');

// PRODUCTS
Route::middleware(['auth','role:admin'])->group(function () {
    Route::resource('products', ProductController::class)->except(['index','show']);
});
Route::resource('products', ProductController::class)->only(['index','show']);

// CART
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/{id}/qty', [CartController::class, 'updateQty'])->name('cart.qty');
Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

// CHECKOUT
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/complete', [CheckoutController::class, 'complete'])->name('checkout.complete');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
});


require __DIR__.'/auth.php';
