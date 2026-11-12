<?php

namespace Database\Seeders;

use App\Services\Identity\IdentityService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(IdentityService::class)->seedDefaultRolesAndPermissions();
    }
}
