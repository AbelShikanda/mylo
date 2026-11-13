<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\Api\V1\Admin\ReaderController;
use App\Http\Controllers\Api\V1\Admin\WriterController;
use App\Http\Controllers\Api\V1\Admin\ArticleController;
use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\CommentController;
use App\Http\Controllers\Api\V1\Admin\MediaController;
use App\Http\Controllers\Api\V1\Admin\BookController;
use App\Http\Controllers\Api\V1\Admin\OrderController;
use App\Http\Controllers\Api\V1\Admin\FinanceController;
use App\Http\Controllers\Api\V1\Admin\TransactionController;
use App\Http\Controllers\Api\V1\Admin\PayoutController;
use App\Http\Controllers\Api\V1\Admin\RefundController;
use App\Http\Controllers\Api\V1\Admin\PaymentGatewayController;
use App\Http\Controllers\Api\V1\Admin\HomepageController;
use App\Http\Controllers\Api\V1\Admin\NotificationController;
use App\Http\Controllers\Api\V1\Admin\AnalyticsController;
use App\Http\Controllers\Api\V1\Admin\AdvancedAnalyticsController;
use App\Http\Controllers\Api\V1\Admin\AdvancedModerationController;
use App\Http\Controllers\Api\V1\Admin\AdministratorController;
use App\Http\Controllers\Api\V1\Admin\EditorialSchedulingController;
use App\Http\Controllers\Api\V1\Admin\RoleController;
use App\Http\Controllers\Api\V1\Admin\PermissionController;
use App\Http\Controllers\Api\V1\Admin\AuditLogController;
use App\Http\Controllers\Api\V1\Admin\ConfigController;

/*
|--------------------------------------------------------------------------
| Administrator Routes
|--------------------------------------------------------------------------
| Guard: auth:sanctum,admin
*/

Route::prefix('admin')->middleware('auth:sanctum,admin')->group(function () {
    // Dashboard
    Route::get('dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // User management
    Route::get('readers', [ReaderController::class, 'index'])->name('readers.index');
    Route::get('writers', [WriterController::class, 'index'])->name('writers.index');
    Route::put('users/{reader}/suspend', [ReaderController::class, 'suspend'])->name('users.suspend');
    Route::put('users/{reader}/reinstate', [ReaderController::class, 'reinstate'])->name('users.reinstate');
    Route::put('writers/{writer}/suspend', [WriterController::class, 'suspend'])->name('writers.suspend');
    Route::put('writers/{writer}/reinstate', [WriterController::class, 'reinstate'])->name('writers.reinstate');

    // Article management
    Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::put('articles/{article}/feature', [ArticleController::class, 'feature'])->name('articles.feature');
    Route::put('articles/{article}/unfeature', [ArticleController::class, 'unfeature'])->name('articles.unfeature');
    Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');

    // Category management
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Comment moderation
    Route::get('comments', [CommentController::class, 'index'])->name('comments.index');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Media management
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

    // Marketplace
    Route::get('books', [BookController::class, 'index'])->name('books.index');
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');

    // Finance
    Route::get('finance/overview', [FinanceController::class, 'overview'])->name('finance.overview');
    Route::get('finance/transactions', [TransactionController::class, 'index'])->name('finance.transactions');
    Route::get('finance/earnings', [FinanceController::class, 'earnings'])->name('finance.earnings');

    // Payouts
    Route::get('finance/payouts', [PayoutController::class, 'index'])->name('finance.payouts');
    Route::post('finance/payouts/{payout}/approve', [PayoutController::class, 'approve'])->name('finance.payouts.approve');
    Route::post('finance/payouts/{payout}/reject', [PayoutController::class, 'reject'])->name('finance.payouts.reject');

    // Refunds
    Route::get('finance/refunds', [RefundController::class, 'index'])->name('finance.refunds');
    Route::post('finance/refunds/{refund}/approve', [RefundController::class, 'approve'])->name('finance.refunds.approve');
    Route::post('finance/refunds/{refund}/reject', [RefundController::class, 'reject'])->name('finance.refunds.reject');

    // Payment gateways
    Route::get('payment-gateways', [PaymentGatewayController::class, 'index'])->name('payment-gateways.index');
    Route::put('payment-gateways/{gateway}', [PaymentGatewayController::class, 'update'])->name('payment-gateways.update');

    // Homepage
    Route::get('homepage/sections', [HomepageController::class, 'index'])->name('homepage.sections');
    Route::post('homepage/sections', [HomepageController::class, 'store'])->name('homepage.sections.store');
    Route::put('homepage/sections/{section}', [HomepageController::class, 'update'])->name('homepage.sections.update');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // Analytics
    Route::get('analytics/reports', [AnalyticsController::class, 'reports'])->name('analytics.reports');

    // Advanced Analytics
    Route::get('analytics/registration-trends', [AdvancedAnalyticsController::class, 'registrationTrends'])->name('analytics.registration-trends');
    Route::get('analytics/reader-engagement', [AdvancedAnalyticsController::class, 'readerEngagement'])->name('analytics.reader-engagement');
    Route::get('analytics/writer-activity', [AdvancedAnalyticsController::class, 'writerActivity'])->name('analytics.writer-activity');
    Route::get('analytics/article-performance', [AdvancedAnalyticsController::class, 'articlePerformance'])->name('analytics.article-performance');
    Route::get('analytics/category-popularity', [AdvancedAnalyticsController::class, 'categoryPopularity'])->name('analytics.category-popularity');
    Route::get('analytics/search-activity', [AdvancedAnalyticsController::class, 'searchActivity'])->name('analytics.search-activity');
    Route::get('analytics/book-sales', [AdvancedAnalyticsController::class, 'bookSales'])->name('analytics.book-sales');
    Route::get('analytics/financial-contributions', [AdvancedAnalyticsController::class, 'financialContributions'])->name('analytics.financial-contributions');
    Route::get('analytics/payouts', [AdvancedAnalyticsController::class, 'payouts'])->name('analytics.payouts');
    Route::get('analytics/failed-transactions', [AdvancedAnalyticsController::class, 'failedTransactions'])->name('analytics.failed-transactions');
    Route::get('analytics/moderation-activity', [AdvancedAnalyticsController::class, 'moderationActivity'])->name('analytics.moderation-activity');
    Route::get('analytics/platform-overview', [AdvancedAnalyticsController::class, 'platformOverview'])->name('analytics.platform-overview');

    // Advanced Moderation
    Route::get('moderation/reports', [AdvancedModerationController::class, 'getReports'])->name('moderation.reports');
    Route::post('moderation/reports/{reportId}/resolve', [AdvancedModerationController::class, 'resolveReport'])->name('moderation.reports.resolve');
    Route::get('moderation/records', [AdvancedModerationController::class, 'getModerationRecords'])->name('moderation.records');
    Route::post('moderation/accounts/suspend', [AdvancedModerationController::class, 'suspendAccount'])->name('moderation.accounts.suspend');
    Route::post('moderation/accounts/reinstate', [AdvancedModerationController::class, 'reinstateAccount'])->name('moderation.accounts.reinstate');
    Route::post('moderation/comments/{commentId}/remove', [AdvancedModerationController::class, 'removeComment'])->name('moderation.comments.remove');
    Route::post('moderation/accounts/restrict', [AdvancedModerationController::class, 'restrictAccount'])->name('moderation.accounts.restrict');

    // Editorial Scheduling
    Route::get('editorial/selections', [EditorialSchedulingController::class, 'getEditorialSelections'])->name('editorial.selections');
    Route::post('editorial/selections', [EditorialSchedulingController::class, 'createEditorialSelection'])->name('editorial.selections.store');
    Route::put('editorial/selections/{selection}', [EditorialSchedulingController::class, 'updateEditorialSelection'])->name('editorial.selections.update');
    Route::delete('editorial/selections/{selection}', [EditorialSchedulingController::class, 'deleteEditorialSelection'])->name('editorial.selections.destroy');
    Route::get('editorial/scheduled-articles', [EditorialSchedulingController::class, 'getScheduledArticles'])->name('editorial.scheduled-articles');
    Route::get('editorial/homepage-sections', [EditorialSchedulingController::class, 'getHomepageSections'])->name('editorial.homepage-sections');

    // Administrator accounts
    Route::get('administrators', [AdministratorController::class, 'index'])->name('administrators.index');

    // Roles and permissions
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');

    // Audit logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // Platform config
    Route::get('config', [ConfigController::class, 'index'])->name('config.index');
    Route::put('config/{key}', [ConfigController::class, 'update'])->name('config.update');
});
