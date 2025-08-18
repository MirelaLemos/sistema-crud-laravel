<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

// Página inicial redireciona pra lista de produtos
Route::get('/', fn() => redirect()->route('products.index'));

// CRUD de produtos
Route::resource('products', ProductController::class);

// Carrinho
Route::get('/cart', [CartController::class,'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class,'add'])->name('cart.add');
Route::post('/cart/{id}/qty', [CartController::class,'updateQty'])->name('cart.qty');
Route::delete('/cart/{id}', [CartController::class,'remove'])->name('cart.remove');
Route::delete('/cart', [CartController::class,'clear'])->name('cart.clear');


// Checkout
Route::post('/checkout', [CheckoutController::class,'checkout'])->name('checkout');
Route::get('/checkout/success', [CheckoutController::class,'success'])->name('checkout.success');
?>