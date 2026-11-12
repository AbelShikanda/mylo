<?php

namespace Tests\Feature\Identity;

use App\Models\Identity\Reader;
use App\Models\Identity\Writer;
use App\Models\Identity\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdentitySeparationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reader_cannot_access_writer_endpoints(): void
    {
        $reader = Reader::factory()->create([
            'email' => 'reader@example.com',
            'password' => bcrypt('Password123!@#'),
        ]);

        $token = $reader->createToken('reader-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/writer/articles', [
                'title' => 'Unauthorized Article',
                'content' => '{}',
            ]);

        $response->assertStatus(404);
    }

    public function test_writer_cannot_access_reader_endpoints(): void
    {
        $writer = Writer::factory()->create([
            'email' => 'writer@example.com',
            'password' => bcrypt('Password123!@#'),
        ]);

        $token = $writer->createToken('writer-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/reader/profile');

        $response->assertStatus(404);
    }

    public function test_reader_and_writer_are_separate_identities(): void
    {
        $reader = Reader::factory()->create(['email' => 'person@example.com']);
        $writer = Writer::factory()->create(['email' => 'person@example.com']);

        $this->assertNotEquals($reader->id, $writer->id);
        $this->assertInstanceOf(Reader::class, $reader);
        $this->assertInstanceOf(Writer::class, $writer);
    }

    public function test_administrator_is_independent_identity(): void
    {
        $admin = Administrator::factory()->create(['email' => 'admin@example.com']);

        $this->assertInstanceOf(Administrator::class, $admin);
        $this->assertDatabaseMissing('readers', ['email' => 'admin@example.com']);
        $this->assertDatabaseMissing('writers', ['email' => 'admin@example.com']);
    }
}
