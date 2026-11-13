<?php

namespace Database\Seeders;

use App\Services\Identity\IdentityService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app(IdentityService::class)->seedDefaultRolesAndPermissions();

        $this->call(DemoSeeder::class);
    }
}
