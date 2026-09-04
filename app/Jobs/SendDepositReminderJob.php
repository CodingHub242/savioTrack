<?php

namespace App\Jobs;

use App\Models\Goal;
use App\Notifications\ArkeselDepositReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendDepositReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Goal $goal) {}

    public function handle(): void
    {
        if (! $this->goal->phone_number) {
            $user = $this->goal->user;
            if (! $user || ! $user->phone_number) {
                return;
            }
            $recipient = $user->phone_number;
        } else {
            $recipient = $this->goal->phone_number;
        }

        $notification = new ArkeselDepositReminder($this->goal);

        \Notification::route('arkesel', $recipient)
            ->notify($notification);
    }
}
