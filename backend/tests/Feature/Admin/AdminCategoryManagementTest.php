<?php

namespace Tests\Feature\Admin;

use App\Models\Identity\Category;
use Tests\Feature\Admin\AdminTestCase;

class AdminCategoryManagementTest extends AdminTestCase
{
    public function test_admin_can_list_categories(): void
    {
        $admin = $this->createSuperAdmin();
        Category::factory()->count(3)->create();

        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/categories');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_admin_can_create_category(): void
    {
        $admin = $this->createSuperAdmin();

        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/categories', [
                'name' => 'Food and Culinary',
                'slug' => 'food-culinary',
                'description' => 'Food and cooking articles',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', [
            'name' => 'Food and Culinary',
            'slug' => 'food-culinary',
        ]);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = $this->createSuperAdmin();
        $category = Category::factory()->create();

        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson('/api/v1/categories/'.$category->id);

        $response->assertStatus(200);
        $this->assertSoftDeleted($category);
    }
}
