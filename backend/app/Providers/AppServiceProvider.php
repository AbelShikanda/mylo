<?php

namespace App\Providers;

use App\Models\Identity\Reader;
use App\Models\Identity\Writer;
use App\Models\Identity\Administrator;
use App\Policies\ReaderPolicy;
use App\Policies\WriterPolicy;
use App\Policies\AdminPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Reader::class, ReaderPolicy::class);
        Gate::policy(Writer::class, WriterPolicy::class);
        Gate::policy(Administrator::class, AdminPolicy::class);
    }
}
