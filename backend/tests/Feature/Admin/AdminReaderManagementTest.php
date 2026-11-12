<?php

namespace Tests\Feature\Admin;

use App\Models\Identity\Reader;
use Tests\Feature\Admin\AdminTestCase;

class AdminReaderManagementTest extends AdminTestCase
{
    public function test_admin_can_list_readers(): void
    {
        $admin = $this->createSuperAdmin();
        Reader::factory()->count(3)->create();

        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/readers');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_admin_can_suspend_reader(): void
    {
        $admin = $this->createSuperAdmin();
        $reader = Reader::factory()->create();

        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/admin/users/'.$reader->id.'/suspend');

        $response->assertStatus(200);
        $this->assertSoftDeleted($reader);
    }

    public function test_admin_can_reinstate_reader(): void
    {
        $admin = $this->createSuperAdmin();
        $reader = Reader::factory()->create();
        $reader->delete();

        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/admin/users/'.$reader->id.'/reinstate');

        $response->assertStatus(200);
        $this->assertNotSoftDeleted($reader);
    }
}
