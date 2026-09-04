<?php

namespace App\Jobs;

use App\Models\Deposit;
use App\Models\AiInteraction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDepositJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Deposit $deposit) {}

    public function handle(): void
    {
        AiInteraction::create([
            'user_id' => $this->deposit->user_id,
            'related_type' => Deposit::class,
            'related_id' => $this->deposit->id,
            'type' => 'deposit_processed',
            'prompt' => "Process recurring deposit of {$this->deposit->amount} for goal: {$this->deposit->goal->name}",
            'response' => "Recurring deposit processed.",
            'context' => [
                'goal_id' => $this->deposit->goal_id,
                'amount' => $this->deposit->amount,
                'frequency' => $this->deposit->frequency,
            ],
        ]);
    }
}
