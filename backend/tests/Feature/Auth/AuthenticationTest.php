<?php

namespace Tests\Feature\Auth;

use App\Models\Identity\Reader;
use App\Models\Identity\Writer;
use App\Models\Identity\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reader_can_login(): void
    {
        $reader = Reader::factory()->create([
            'email' => 'reader@example.com',
            'password' => bcrypt('Password123!@#'),
        ]);

        $response = $this->postJson('/api/v1/auth/reader/login', [
            'email' => 'reader@example.com',
            'password' => 'Password123!@#',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['reader', 'token']]);
    }

    public function test_writer_can_login(): void
    {
        $writer = Writer::factory()->create([
            'email' => 'writer@example.com',
            'password' => bcrypt('Password123!@#'),
        ]);

        $response = $this->postJson('/api/v1/auth/writer/login', [
            'email' => 'writer@example.com',
            'password' => 'Password123!@#',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['writer', 'token']]);
    }

    public function test_admin_can_login(): void
    {
        $admin = Administrator::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('Password123!@#'),
        ]);

        $response = $this->postJson('/api/v1/auth/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'Password123!@#',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['administrator', 'token']]);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        Reader::factory()->create([
            'email' => 'reader@example.com',
            'password' => bcrypt('Password123!@#'),
        ]);

        $response = $this->postJson('/api/v1/auth/reader/login', [
            'email' => 'reader@example.com',
            'password' => 'WrongPassword123!@#',
        ]);

        $response->assertStatus(401);
    }
}
