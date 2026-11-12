<?php

namespace Tests\Feature\Admin;

use App\Models\Identity\Reader;
use Tests\Feature\Admin\AdminTestCase;

class AdminDashboardTest extends AdminTestCase
{
    public function test_admin_dashboard_returns_stats(): void
    {
        $admin = $this->createSuperAdmin();

        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/dashboard/stats');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'users',
                'publishing',
                'engagement',
                'marketplace',
                'finance',
            ],
        ]);
    }

    public function test_non_admin_cannot_access_dashboard(): void
    {
        $reader = Reader::factory()->create();

        $token = $reader->createToken('reader-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/dashboard/stats');

        $response->assertStatus(403);
    }
}
