<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_failed_withdrawal_is_not_saved(): void
    {
        $user = User::factory()->create();

        $account = Account::create([
            'user_id' => $user->id,
            'account_number' => 'TEST100001',
            'balance' => 1200,
            'minimum_balance' => 100,
        ]);

        $response = $this->postJson('/transactions', [
            'account_id' => $account->id,
            'type' => 'withdrawal',
            'amount' => 1150,
            'notes' => 'Failed withdrawal test',
        ]);

        $response->assertUnprocessable();

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => 1200,
        ]);

        $this->assertDatabaseMissing('transactions', [
            'account_id' => $account->id,
            'notes' => 'Failed withdrawal test',
        ]);
    }
}
