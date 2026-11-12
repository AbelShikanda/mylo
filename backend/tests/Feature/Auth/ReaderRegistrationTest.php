<?php

namespace Tests\Feature\Auth;

use App\Models\Identity\Reader;
use App\Models\Identity\Writer;
use App\Models\Identity\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReaderRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reader_can_register(): void
    {
        $response = $this->postJson('/api/v1/auth/reader/register', [
            'email' => 'reader@example.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#',
            'display_name' => 'Test Reader',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['reader', 'token']]);

        $this->assertDatabaseHas('readers', ['email' => 'reader@example.com']);
        $this->assertDatabaseHas('reader_profiles', ['reader_id' => Reader::where('email', 'reader@example.com')->first()->id]);
    }

    public function test_reader_registration_requires_unique_email(): void
    {
        Reader::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/v1/auth/reader/register', [
            'email' => 'existing@example.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#',
        ]);

        $response->assertStatus(422);
    }

    public function test_reader_registration_validates_password(): void
    {
        $response = $this->postJson('/api/v1/auth/reader/register', [
            'email' => 'reader@example.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
        ]);

        $response->assertStatus(422);
    }
}
