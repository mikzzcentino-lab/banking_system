<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\Transaction;
use App\Notifications\LowBalanceNotification;
use Illuminate\Validation\ValidationException;

class AccountObserver
{
    public function created(Transaction $transaction): void
    {
        $account = Account::whereKey($transaction->account_id)
            ->lockForUpdate()
            ->firstOrFail();

        $amount = (float) $transaction->amount;

        if ($transaction->type === 'deposit') {
            $account->balance += $amount;
        } elseif (
            in_array($transaction->type, ['withdrawal', 'transfer'], true)
        ) {
            $newBalance = (float) $account->balance - $amount;

            if ($newBalance < (float) $account->minimum_balance) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient funds. Minimum balance must be maintained.',
                ]);
            }

            $account->balance = $newBalance;
        }

        $account->save();

        // Send a notification when a withdrawal reaches the minimum balance.
        if (
            $transaction->type === 'withdrawal'
            && (float) $account->balance <= (float) $account->minimum_balance
        ) {
            $user = $account->user;

            if ($user) {
                $alreadyNotified = $user->notifications()
                    ->where('type', LowBalanceNotification::class)
                    ->whereJsonContains('data->account_id', $account->id)
                    ->exists();

                if (! $alreadyNotified) {
                    $user->notify(new LowBalanceNotification(
                        (float) $account->balance,
                        (float) $account->minimum_balance,
                        (int) $account->id
                    ));
                }
            }
        }
    }
}
