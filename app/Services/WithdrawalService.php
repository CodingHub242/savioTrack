<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\Withdrawal;
use App\Models\AiInteraction;
use App\Jobs\AIAnalysisJob;

class WithdrawalService
{
    public function createWithdrawal(Goal $goal, float $amount, string $reason): Withdrawal
    {
        $withdrawal = $goal->withdrawals()->create([
            'user_id' => $goal->user_id,
            'amount' => $amount,
            'reason' => $reason,
            'decision' => 'pending',
        ]);

        AIAnalysisJob::dispatch($withdrawal);

        return $withdrawal;
    }

    public function processDecision(Withdrawal $withdrawal, string $decision, ?string $userNotes): void
    {
        $withdrawal->update([
            'decision' => $decision,
            'user_notes' => $userNotes,
        ]);

        $goal = $withdrawal->goal;
        $viabilityScore = $withdrawal->viability_score ?? 5;
        $isGoodDecision = $decision === 'approved' ? $viabilityScore >= 7 : $viabilityScore < 4;

        $withdrawal->update([
            'decision_quality' => $isGoodDecision ? 'safe' : 'bad',
        ]);

        if ($decision === 'approved') {
            $goal->decrement('current_amount', $withdrawal->amount);
        }

        AiInteraction::create([
            'user_id' => $withdrawal->user_id,
            'related_type' => Withdrawal::class,
            'related_id' => $withdrawal->id,
            'type' => 'withdrawal_decision',
            'prompt' => "User decided to {$decision} withdrawal of {$withdrawal->amount} for goal: {$goal->name}",
            'response' => "Decision quality: " . ($isGoodDecision ? 'safe' : 'bad') . ". AI viability score was {$viabilityScore}/10.",
            'context' => [
                'goal_id' => $goal->id,
                'amount' => $withdrawal->amount,
                'user_decision' => $decision,
                'ai_score' => $viabilityScore,
                'decision_quality' => $isGoodDecision ? 'safe' : 'bad',
            ],
        ]);
    }
}
