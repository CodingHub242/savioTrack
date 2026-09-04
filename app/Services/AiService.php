<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\Withdrawal;
use App\Models\AiInteraction;
use Illuminate\Support\Facades\Auth;

class AiService
{
    public function arrangeDashboard(string $viewMode): array
    {
        $config = match ($viewMode) {
            'progress' => [
                'layout' => 'progress_focused',
                'sort_goals_by' => 'progress_percentage',
                'highlight' => 'goals_near_completion',
            ],
            'savings' => [
                'layout' => 'savings_focused',
                'sort_goals_by' => 'effective_saved_amount',
                'highlight' => 'top_savers',
            ],
            'deadlines' => [
                'layout' => 'deadline_focused',
                'sort_goals_by' => 'deadline',
                'highlight' => 'urgent_goals',
            ],
            default => [
                'layout' => 'default',
                'sort_goals_by' => 'created_at',
                'highlight' => 'recent_activity',
            ],
        };

        AiInteraction::create([
            'user_id' => Auth::id(),
            'type' => 'dashboard_arrangement',
            'prompt' => "Arrange dashboard in {$viewMode} mode for user",
            'response' => json_encode($config),
            'context' => ['view_mode' => $viewMode],
        ]);

        return $config;
    }

    public function analyzeWithdrawal(Withdrawal $withdrawal): array
    {
        $goal = $withdrawal->goal;
        $wants = $goal->wants()->where('status', '!=', 'cancelled')->get();
        $needs = $goal->needs()->where('status', '!=', 'cancelled')->get();
        $totalWantsCost = $wants->sum('cost');
        $totalNeedsCost = $needs->sum('cost');
        $progressPercentage = $goal->progress_percentage;

        $viabilityScore = $this->calculateViabilityScore(
            $withdrawal->amount,
            $goal->current_amount,
            $totalWantsCost,
            $totalNeedsCost,
            $progressPercentage
        );

        $aiSummary = $this->generateWithdrawalSummary(
            $withdrawal,
            $goal,
            $wants,
            $needs,
            $viabilityScore
        );

        $withdrawal->update([
            'viability_score' => $viabilityScore,
            'ai_summary' => $aiSummary,
        ]);

        return [
            'viability_score' => $viabilityScore,
            'ai_summary' => $aiSummary,
        ];
    }

    private function calculateViabilityScore(float $amount, float $currentAmount, float $wantsCost, float $needsCost, float $progress): int
    {
        $score = 5;

        if ($amount > $currentAmount * 0.5) {
            $score -= 2;
        }

        if ($amount > $wantsCost + $needsCost) {
            $score -= 2;
        }

        if ($progress < 30) {
            $score -= 1;
        }

        if ($progress >= 80) {
            $score += 2;
        }

        return max(1, min(10, $score));
    }

    private function generateWithdrawalSummary(Withdrawal $withdrawal, Goal $goal, $wants, $needs, int $score): string
    {
        $lines = [
            "Withdrawal Analysis for: {$goal->name}",
            "Amount requested: {$withdrawal->amount}",
            "Current savings: {$goal->current_amount}",
            "Progress: " . round($goal->progress_percentage, 1) . "%",
            "",
            "Wants ({$wants->count()} items, total: {$wants->sum('cost')}):",
        ];

        foreach ($wants as $want) {
            $lines[] = "- {$want->name}: {$want->cost} ({$want->priority} priority)";
        }

        $lines[] = "";
        $lines[] = "Needs ({$needs->count()} items, total: {$needs->sum('cost')}):";

        foreach ($needs as $need) {
            $lines[] = "- {$need->name}: {$need->cost} ({$need->priority} priority)";
        }

        $lines[] = "";
        $lines[] = "AI Viability Score: {$score}/10";

        if ($score >= 7) {
            $lines[] = "Assessment: This withdrawal appears financially viable based on your savings progress and remaining wants/needs.";
        } elseif ($score >= 4) {
            $lines[] = "Assessment: This withdrawal has moderate risk. Consider if there are alternative uses for these funds.";
        } else {
            $lines[] = "Assessment: This withdrawal is NOT recommended. It would significantly impact your goal progress and ability to meet wants/needs.";
        }

        return implode("\n", $lines);
    }
}
