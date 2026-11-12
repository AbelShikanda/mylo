<?php

namespace Tests\Feature\Admin;

use App\Models\Identity\Administrator;
use App\Models\Identity\AdminRole;
use App\Models\Identity\AdminPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class AdminTestCase extends TestCase
{
    use RefreshDatabase;

    protected function createSuperAdmin(): Administrator
    {
        $admin = Administrator::factory()->create([
            'email' => 'admin@test.com',
        ]);

        $role = AdminRole::firstOrCreate(
            ['slug' => 'super-administrator'],
            ['name' => 'Super Administrator']
        );

        $permissions = AdminPermission::all();
        if ($permissions->isEmpty()) {
            $permissions = collect([
                AdminPermission::firstOrCreate(['slug' => 'readers.view'], ['name' => 'Readers View']),
                AdminPermission::firstOrCreate(['slug' => 'readers.suspend'], ['name' => 'Readers Suspend']),
                AdminPermission::firstOrCreate(['slug' => 'readers.reinstate'], ['name' => 'Readers Reinstate']),
                AdminPermission::firstOrCreate(['slug' => 'writers.view'], ['name' => 'Writers View']),
                AdminPermission::firstOrCreate(['slug' => 'writers.suspend'], ['name' => 'Writers Suspend']),
                AdminPermission::firstOrCreate(['slug' => 'writers.reinstate'], ['name' => 'Writers Reinstate']),
                AdminPermission::firstOrCreate(['slug' => 'categories.manage'], ['name' => 'Categories Manage']),
                AdminPermission::firstOrCreate(['slug' => 'config.manage'], ['name' => 'Config Manage']),
                AdminPermission::firstOrCreate(['slug' => 'audit-logs.view'], ['name' => 'Audit Logs View']),
                AdminPermission::firstOrCreate(['slug' => 'roles.manage'], ['name' => 'Roles Manage']),
            ]);
        }

        $admin->roles()->attach($role->id);
        $role->permissions()->attach($permissions->pluck('id'));

        return $admin;
    }
}
