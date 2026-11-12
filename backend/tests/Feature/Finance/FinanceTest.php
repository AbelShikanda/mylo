<?php

namespace Tests\Feature\Finance;

use App\Models\Identity\Administrator;
use App\Models\Identity\AdminRole;
use App\Models\Identity\AdminPermission;
use App\Models\Identity\Reader;
use App\Models\Identity\Writer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class FinanceTestCase extends TestCase
{
    use RefreshDatabase;

    protected function createSuperAdmin(): Administrator
    {
        $admin = Administrator::factory()->create([
            'email' => 'admin@test.com',
        ]);

        $role = AdminRole::firstOrCreate(
            ['slug' => 'super-administrator'],
            ['name' => 'Super Administrator']
        );

        $permissions = AdminPermission::all();
        if ($permissions->isEmpty()) {
            $permissions = collect([
                AdminPermission::firstOrCreate(['slug' => 'finance.view'], ['name' => 'Finance View']),
                AdminPermission::firstOrCreate(['slug' => 'finance.payouts.manage'], ['name' => 'Finance Payouts Manage']),
                AdminPermission::firstOrCreate(['slug' => 'finance.refunds.manage'], ['name' => 'Finance Refunds Manage']),
            ]);
        }

        $admin->roles()->attach($role->id);
        $role->permissions()->attach($permissions->pluck('id'));

        return $admin;
    }
}

class ReaderContributionTest extends FinanceTestCase
{
    public function test_reader_can_make_contribution(): void
    {
        $writer = Writer::factory()->create();

        $reader = Reader::factory()->create();
        $token = $reader->createToken('reader-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/reader/contributions', [
                'writer_id' => $writer->id,
                'amount' => 100.00,
                'currency' => 'KES',
                'type' => 'one-time',
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'amount' => '100.00',
                'currency' => 'KES',
                'type' => 'one-time',
                'status' => 'completed',
            ],
        ]);

        $this->assertDatabaseHas('contributions', [
            'writer_id' => $writer->id,
            'reader_id' => $reader->id,
            'amount' => 100.00,
        ]);
    }

    public function test_reader_can_view_own_contributions(): void
    {
        $writer = Writer::factory()->create();
        $reader = Reader::factory()->create();
        $token = $reader->createToken('reader-api')->plainTextToken;

        \App\Models\Identity\Contribution::factory()->create([
            'writer_id' => $writer->id,
            'reader_id' => $reader->id,
            'amount' => 50.00,
            'currency' => 'KES',
            'transaction_reference' => 'test-ref-1',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/reader/contributions');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }
}

class WriterEarningsTest extends FinanceTestCase
{
    public function test_writer_can_view_earnings(): void
    {
        $writer = Writer::factory()->create();
        $token = $writer->createToken('writer-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/writer/earnings');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'contributions_received',
                'book_sales',
                'platform_deductions',
                'available_balance',
                'pending_balance',
                'withdrawn_amount',
            ],
        ]);
    }

    public function test_writer_can_request_payout(): void
    {
        $writer = Writer::factory()->create();
        $token = $writer->createToken('writer-api')->plainTextToken;

        // First, add some earnings
        \App\Models\Identity\WriterEarnings::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'writer_id' => $writer->id,
            'contributions' => 100.00,
            'available_balance' => 100.00,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/writer/earnings/payout-request', [
                'amount' => 50.00,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('payout_requests', [
            'writer_id' => $writer->id,
            'amount' => 50.00,
            'status' => 'pending',
        ]);
    }
}

class AdminFinanceTest extends FinanceTestCase
{
    public function test_admin_can_view_finance_overview(): void
    {
        $admin = $this->createSuperAdmin();
        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/finance/overview');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'transaction_volume',
                'writer_contributions',
                'refunds',
            ],
        ]);
    }

    public function test_admin_can_view_transactions(): void
    {
        $admin = $this->createSuperAdmin();
        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/finance/transactions');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_payouts(): void
    {
        $admin = $this->createSuperAdmin();
        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/finance/payouts');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_refunds(): void
    {
        $admin = $this->createSuperAdmin();
        $token = $admin->createToken('admin-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/finance/refunds');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_finance(): void
    {
        $reader = Reader::factory()->create();
        $token = $reader->createToken('reader-api')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/finance/overview');

        $response->assertStatus(403);
    }
}
