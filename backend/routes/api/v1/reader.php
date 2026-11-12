<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Reader\ProfileController;
use App\Http\Controllers\Api\V1\Reader\PreferenceController;
use App\Http\Controllers\Api\V1\Reader\BookmarkController;
use App\Http\Controllers\Api\V1\Reader\ReadingHistoryController;
use App\Http\Controllers\Api\V1\Reader\NotificationController;
use App\Http\Controllers\Api\V1\Reader\LikeController;
use App\Http\Controllers\Api\V1\Reader\ReportController;
use App\Http\Controllers\Api\V1\Reader\ContributionController;
use App\Http\Controllers\Api\V1\Reader\LibraryController;
use App\Http\Controllers\Api\V1\Reader\OrderController;

/*
|--------------------------------------------------------------------------
| Reader Routes
|--------------------------------------------------------------------------
| Guard: auth:sanctum
*/

Route::prefix('reader')->middleware('auth:sanctum')->group(function () {
    // Profile
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Preferences
    Route::get('preferences', [PreferenceController::class, 'show'])->name('preferences.show');
    Route::put('preferences', [PreferenceController::class, 'update'])->name('preferences.update');

    // Bookmarks
    Route::get('bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('bookmarks/{article}/toggle', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::get('bookmarks/{article}/status', [BookmarkController::class, 'isBookmarked'])->name('bookmarks.status');

    // Likes
    Route::post('articles/{article}/like/toggle', [LikeController::class, 'toggle'])->name('articles.like.toggle');
    Route::get('articles/{article}/like/status', [LikeController::class, 'isLiked'])->name('articles.like.status');
    Route::get('likes', [LikeController::class, 'getLiked'])->name('likes.index');

    // Reading history
    Route::get('reading-history', [ReadingHistoryController::class, 'index'])->name('reading-history.index');
    Route::post('articles/{article}/reading-history', [ReadingHistoryController::class, 'record'])->name('reading-history.record');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::put('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Reports
    Route::post('reports', [ReportController::class, 'store'])->name('reports.store');

    // Contributions (financial support)
    Route::get('contributions', [ContributionController::class, 'index'])->name('contributions.index');
    Route::post('contributions', [ContributionController::class, 'store'])->name('contributions.store');

    // Library (purchased ebooks)
    Route::get('library', [LibraryController::class, 'index'])->name('library.index');

    // Orders
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});
