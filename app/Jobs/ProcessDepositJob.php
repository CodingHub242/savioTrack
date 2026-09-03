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
        $goal = $this->deposit->goal;
        $goal->increment('current_amount', $this->deposit->amount);

        AiInteraction::create([
            'user_id' => $this->deposit->user_id,
            'related_type' => Deposit::class,
            'related_id' => $this->deposit->id,
            'type' => 'deposit_processed',
            'prompt' => "Process deposit of {$this->deposit->amount} for goal: {$goal->name}",
            'response' => "Deposit processed. Goal current amount updated to {$goal->current_amount}.",
            'context' => [
                'goal_id' => $goal->id,
                'amount' => $this->deposit->amount,
                'frequency' => $this->deposit->frequency,
            ],
        ]);
    }
}
