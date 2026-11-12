<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All API routes are versioned under /api/v1.
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    require __DIR__.'/api/v1/auth.php';
    require __DIR__.'/api/v1/public.php';
    require __DIR__.'/api/v1/reader.php';
    require __DIR__.'/api/v1/writer.php';
    require __DIR__.'/api/v1/admin.php';
});
