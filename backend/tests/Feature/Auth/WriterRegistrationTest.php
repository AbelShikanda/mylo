<?php

namespace Tests\Feature\Auth;

use App\Models\Identity\Writer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WriterRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_writer_can_register_without_approval(): void
    {
        $response = $this->postJson('/api/v1/auth/writer/register', [
            'email' => 'writer@example.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#',
            'display_name' => 'Test Writer',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['writer', 'token']]);

        $this->assertDatabaseHas('writers', ['email' => 'writer@example.com']);
    }

    public function test_writer_registration_requires_unique_email(): void
    {
        Writer::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/v1/auth/writer/register', [
            'email' => 'existing@example.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#',
        ]);

        $response->assertStatus(422);
    }
}
