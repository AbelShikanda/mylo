<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Writer\ProfileController;
use App\Http\Controllers\Api\V1\Writer\ArticleController;
use App\Http\Controllers\Api\V1\Writer\DraftController;
use App\Http\Controllers\Api\V1\Writer\ScheduledArticleController;
use App\Http\Controllers\Api\V1\Writer\MediaController;
use App\Http\Controllers\Api\V1\Writer\BookController;
use App\Http\Controllers\Api\V1\Writer\CommentController;
use App\Http\Controllers\Api\V1\Writer\FinancialSupportController;
use App\Http\Controllers\Api\V1\Writer\EarningsController;
use App\Http\Controllers\Api\V1\Writer\AnalyticsController;

/*
|--------------------------------------------------------------------------
| Writer Routes
|--------------------------------------------------------------------------
| Guard: auth:sanctum,writer
*/

Route::prefix('writer')->middleware('auth:sanctum,writer')->group(function () {
    // Profile
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Articles
    Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::post('articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
    Route::put('articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
    Route::post('articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
    Route::post('articles/{article}/schedule', [ArticleController::class, 'schedule'])->name('articles.schedule');
    Route::post('articles/{article}/autosave', [ArticleController::class, 'autosave'])->name('articles.autosave');

    // Drafts
    Route::get('drafts', [DraftController::class, 'index'])->name('drafts.index');

    // Scheduled
    Route::get('scheduled', [ScheduledArticleController::class, 'index'])->name('scheduled.index');

    // Media
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::post('media', [MediaController::class, 'store'])->name('media.store');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

    // Books
    Route::get('books', [BookController::class, 'index'])->name('books.index');
    Route::post('books', [BookController::class, 'store'])->name('books.store');
    Route::get('books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::put('books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    // Comments
    Route::get('comments', [CommentController::class, 'index'])->name('comments.index');
    Route::put('comments/{comment}', [CommentController::class, 'update'])->name('comments.update');

    // Financial support
    Route::post('support/activate', [FinancialSupportController::class, 'activate'])->name('support.activate');
    Route::post('support/deactivate', [FinancialSupportController::class, 'deactivate'])->name('support.deactivate');
    Route::post('support/recurring', [FinancialSupportController::class, 'createRecurring'])->name('support.recurring');
    Route::delete('support/recurring/{supportId}', [FinancialSupportController::class, 'cancelRecurring'])->name('support.recurring.cancel');

    // Earnings
    Route::get('earnings', [EarningsController::class, 'index'])->name('earnings.index');
    Route::post('earnings/payout-request', [EarningsController::class, 'requestPayout'])->name('earnings.payout-request');
    Route::get('earnings/payouts', [EarningsController::class, 'getPayouts'])->name('earnings.payouts');

    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
});
