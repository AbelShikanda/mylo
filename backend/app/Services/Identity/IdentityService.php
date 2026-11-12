<?php

namespace App\Services\Identity;

use App\Models\Identity\Reader;
use App\Models\Identity\Writer;
use App\Models\Identity\Administrator;
use App\Models\Identity\AdminRole;
use App\Models\Identity\AdminPermission;
use Illuminate\Support\Facades\Hash;

class IdentityService extends \App\Services\Shared\BaseService
{
    public function createReader(array $data): Reader
    {
        return Reader::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function createWriter(array $data): Writer
    {
        return Writer::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function createAdministrator(array $data): Administrator
    {
        return Administrator::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function assignRole(Administrator $admin, string $roleSlug): void
    {
        $role = AdminRole::where('slug', $roleSlug)->firstOrFail();
        $admin->roles()->attach($role->id);
    }

    public function seedDefaultRolesAndPermissions(): void
    {
        $superAdmin = AdminRole::firstOrCreate(
            ['slug' => 'super-administrator'],
            ['name' => 'Super Administrator', 'description' => 'Full access to all platform features.']
        );

        $editorialAdmin = AdminRole::firstOrCreate(
            ['slug' => 'editorial-administrator'],
            ['name' => 'Editorial Administrator', 'description' => 'Articles, categories, homepage, editorial schedules.']
        );

        $communityModerator = AdminRole::firstOrCreate(
            ['slug' => 'community-moderator'],
            ['name' => 'Community Moderator', 'description' => 'Comments, reports, content moderation, abuse management.']
        );

        $marketplaceManager = AdminRole::firstOrCreate(
            ['slug' => 'marketplace-manager'],
            ['name' => 'Marketplace Manager', 'description' => 'Products, books, orders, marketplace disputes.']
        );

        $financeAdmin = AdminRole::firstOrCreate(
            ['slug' => 'finance-administrator'],
            ['name' => 'Finance Administrator', 'description' => 'Transactions, refunds, payouts, financial reports.']
        );

        $analyticsAdmin = AdminRole::firstOrCreate(
            ['slug' => 'analytics-administrator'],
            ['name' => 'Analytics Administrator', 'description' => 'Reports, statistics, authorized exports.']
        );

        $permissions = [
            'readers.view', 'readers.suspend', 'readers.reinstate',
            'writers.view', 'writers.suspend', 'writers.reinstate',
            'articles.view', 'articles.feature', 'articles.unfeature', 'articles.remove',
            'categories.manage',
            'comments.moderate',
            'media.manage',
            'books.manage', 'orders.manage',
            'finance.view', 'finance.payouts.manage', 'finance.refunds.manage',
            'payment-gateways.manage',
            'homepage.manage',
            'notifications.manage',
            'analytics.view',
            'administrators.manage',
            'roles.manage',
            'audit-logs.view',
            'config.manage',
        ];

        foreach ($permissions as $slug) {
            AdminPermission::firstOrCreate(
                ['slug' => $slug],
                ['name' => ucwords(str_replace('.', ' ', $slug)), 'description' => 'Permission for ' . $slug]
            );
        }

        $superAdmin->permissions()->attach(
            AdminPermission::all()->pluck('id')
        );
    }
}
