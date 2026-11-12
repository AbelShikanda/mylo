<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Public\ArticleController;
use App\Http\Controllers\Api\V1\Public\CategoryController;
use App\Http\Controllers\Api\V1\Public\TagController;
use App\Http\Controllers\Api\V1\Public\SearchController;
use App\Http\Controllers\Api\V1\Public\BookController;
use App\Http\Controllers\Api\V1\Public\CartController;
use App\Http\Controllers\Api\V1\Public\CheckoutController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| Accessible without authentication.
*/

// Payment webhooks
Route::prefix('webhooks')->group(function () {
    Route::post('payment/mpesa', [\App\Http\Controllers\Api\V1\Payment\WebhookController::class, 'mpesa'])->name('webhooks.mpesa');
    Route::post('payment/stripe', [\App\Http\Controllers\Api\V1\Payment\WebhookController::class, 'stripe'])->name('webhooks.stripe');
});

// Articles
Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::post('articles/{article}/like', [ArticleController::class, 'like'])->name('articles.like')->middleware('auth:sanctum,reader');
Route::delete('articles/{article}/like', [ArticleController::class, 'unlike'])->name('articles.unlike')->middleware('auth:sanctum,reader');
Route::get('articles/{article}/comments', [ArticleController::class, 'comments'])->name('articles.comments');
Route::post('articles/{article}/comments', [ArticleController::class, 'addComment'])->name('articles.comments.store')->middleware('auth:sanctum,reader');
Route::post('articles/{article}/report', [ArticleController::class, 'report'])->name('articles.report')->middleware('auth:sanctum,reader');
Route::get('articles/{article}/related', [ArticleController::class, 'related'])->name('articles.related');

// Categories
Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('categories/{category}/articles', [CategoryController::class, 'articles'])->name('categories.articles');

// Tags
Route::get('tags', [TagController::class, 'index'])->name('tags.index');
Route::get('tags/{slug}', [TagController::class, 'show'])->name('tags.show');

// Search
Route::get('search', [SearchController::class, 'search'])->name('search');

// Books (marketplace)
Route::get('books', [BookController::class, 'index'])->name('books.index');
Route::get('books/{slug}', [BookController::class, 'show'])->name('books.show');

// Cart
Route::middleware('auth:sanctum,reader')->group(function () {
    Route::get('cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('cart/items', [CartController::class, 'addItem'])->name('cart.items.store');
    Route::delete('cart/items/{item}', [CartController::class, 'removeItem'])->name('cart.items.destroy');

    // Checkout
    Route::post('checkout', [CheckoutController::class, 'checkout'])->name('checkout');
});
