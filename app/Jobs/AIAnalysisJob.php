<?php

namespace App\Jobs;

use App\Models\Withdrawal;
use App\Services\AiService;
use App\Models\AiInteraction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AIAnalysisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Withdrawal $withdrawal) {}

    public function handle(AiService $aiService): void
    {
        $analysis = $aiService->analyzeWithdrawal($this->withdrawal);

        AiInteraction::create([
            'user_id' => $this->withdrawal->user_id,
            'related_type' => Withdrawal::class,
            'related_id' => $this->withdrawal->id,
            'type' => 'withdrawal_analysis',
            'prompt' => "AI analysis for withdrawal of {$this->withdrawal->amount}",
            'response' => $analysis['ai_summary'],
            'context' => [
                'viability_score' => $analysis['viability_score'],
                'goal_id' => $this->withdrawal->goal_id,
            ],
        ]);
    }
}
