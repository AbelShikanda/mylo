<?php

namespace Tests\Feature\Admin;

use Tests\Feature\Admin\AdminTestCase;

class AdminRolesAndPermissionsTest extends AdminTestCase
{
    public function test_admin_can_list_roles(): void
    {
        $admin = $this->createSuperAdmin();

        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/roles');

        $response->assertStatus(200);
    }

    public function test_admin_can_list_permissions(): void
    {
        $admin = $this->createSuperAdmin();

        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/permissions');

        $response->assertStatus(200);
    }

    public function test_admin_can_list_audit_logs(): void
    {
        $admin = $this->createSuperAdmin();

        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/audit-logs');

        $response->assertStatus(200);
    }

    public function test_admin_can_read_config(): void
    {
        $admin = $this->createSuperAdmin();

        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/config');

        $response->assertStatus(200);
    }
}
