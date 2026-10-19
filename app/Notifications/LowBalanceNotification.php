<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowBalanceNotification extends Notification
{
    use Queueable;

    public function __construct(
        public float $balance,
        public float $minimumBalance,
        public int $accountId
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'account_id' => $this->accountId,
            'title' => 'Low Balance Alert',
            'message' => 'Your account balance has reached the minimum balance.',
            'balance' => $this->balance,
            'minimum_balance' => $this->minimumBalance,
        ];
    }
}
