<?php

namespace Tests\Feature\Admin;

use App\Models\Identity\Writer;
use Tests\Feature\Admin\AdminTestCase;

class AdminWriterManagementTest extends AdminTestCase
{
    public function test_admin_can_list_writers(): void
    {
        $admin = $this->createSuperAdmin();
        Writer::factory()->count(3)->create();

        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/writers');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_admin_can_suspend_writer(): void
    {
        $admin = $this->createSuperAdmin();
        $writer = Writer::factory()->create();

        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson('/api/v1/writers/'.$writer->id.'/suspend');

        $response->assertStatus(200);
        $this->assertSoftDeleted($writer);
    }
}
