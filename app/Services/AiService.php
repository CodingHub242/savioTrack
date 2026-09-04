<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\Withdrawal;
use App\Models\AiInteraction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    private function callAiApi(string $prompt, int $maxTokens = 2000): ?string
    {
        $apiKey = config('services.deepseek.key', env('DEEPSEEK_API_KEY'));

        if (! $apiKey) {
            Log::warning('DeepSeek API key not configured. AI features will use fallback analysis.');
            return null;
        }

        $apiUrl = config('services.deepseek.api_url', env('DEEPSEEK_API_URL', 'https://api.deepseek.com/v1/chat/completions'));

        try {
            $response = Http::timeout(30)->withToken($apiKey)->post($apiUrl, [
                'model' => 'deepseek-chat',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are SavioTrack AI, a personal finance assistant specializing in savings analysis. Provide clear, actionable insights about savings goals, withdrawals, and financial planning. Always format responses professionally and include specific numbers.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens' => $maxTokens,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            }

            Log::error('DeepSeek API error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('DeepSeek API exception: ' . $e->getMessage());
        }

        return null;
    }

    public function arrangeDashboard(array $goals, string $viewMode, ?string $naturalPrompt = null): array
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

        $prompt = $naturalPrompt ?? "Arrange dashboard in {$viewMode} view mode for user with " . $goals->count() . " goals.";

        $aiInsight = $this->callAiApi(
            "You are a dashboard arrangement AI for a savings tracker app. The user has requested: \"{$prompt}\". " .
            "Given {$goals->count()} goals, provide a brief insight (1-2 sentences) on how the goals should be displayed. " .
            "Here are the goals: " . $goals->map(fn($g) => "{$g->name} - {$g->progress_percentage}% progress, $" . number_format($g->effective_saved_amount, 2) . " saved of $" . number_format($g->target_amount, 2) . " target")->join(', ')
        );

        if ($aiInsight) {
            $config['ai_insight'] = $aiInsight;
        }

        AiInteraction::create([
            'user_id' => Auth::id(),
            'type' => 'dashboard_arrangement',
            'prompt' => $prompt,
            'response' => json_encode($config),
            'context' => ['view_mode' => $viewMode, 'natural_prompt' => $naturalPrompt],
        ]);

        return $config;
    }

    public function interpretArrangePrompt(string $prompt): string
    {
        $promptLower = strtolower($prompt);

        if (str_contains($promptLower, 'progress') || str_contains($promptLower, 'completion') || str_contains($promptLower, 'nearest') || str_contains($promptLower, 'close')) {
            return 'progress';
        }

        if (str_contains($promptLower, 'savings') || str_contains($promptLower, 'largest') || str_contains($promptLower, 'amount') || str_contains($promptLower, 'money') || str_contains($promptLower, 'most')) {
            return 'savings';
        }

        if (str_contains($promptLower, 'deadline') || str_contains($promptLower, 'due') || str_contains($promptLower, 'date') || str_contains($promptLower, 'soon')) {
            return 'deadlines';
        }

        return 'default';
    }

    public function analyzeWithdrawal(Withdrawal $withdrawal): array
    {
        $goal = $withdrawal->goal;
        $wants = $goal->wants()->where('status', '!=', 'cancelled')->get();
        $needs = $goal->needs()->where('status', '!=', 'cancelled')->get();

        $aiSummary = $this->callAiApi($this->buildWithdrawalPrompt($withdrawal, $goal, $wants, $needs));

        $viabilityScore = $this->calculateViabilityScore(
            $withdrawal->amount,
            $goal->effective_saved_amount,
            $wants->sum('cost'),
            $needs->sum('cost'),
            $goal->progress_percentage
        );

        if (! $aiSummary) {
            $aiSummary = $this->generateFallbackSummary($withdrawal, $goal, $wants, $needs, $viabilityScore);
        }

        $withdrawal->update([
            'viability_score' => $viabilityScore,
            'ai_summary' => $aiSummary,
        ]);

        AiInteraction::create([
            'user_id' => $withdrawal->user_id,
            'related_type' => Withdrawal::class,
            'related_id' => $withdrawal->id,
            'type' => 'withdrawal_analysis',
            'prompt' => $this->buildWithdrawalPrompt($withdrawal, $goal, $wants, $needs),
            'response' => $aiSummary,
            'context' => [
                'viability_score' => $viabilityScore,
                'goal_id' => $goal->id,
            ],
        ]);

        return [
            'viability_score' => $viabilityScore,
            'ai_summary' => $aiSummary,
        ];
    }

    private function buildWithdrawalPrompt(Withdrawal $withdrawal, Goal $goal, $wants, $needs): string
    {
        $remaining = max(0, $goal->effective_saved_amount - $withdrawal->amount);

        $prompt = "You are SavioTrack AI, a personal finance assistant. Analyze this withdrawal request:\n\n";
        $prompt .= "WITHDRAWAL ANALYSIS REQUEST\n";
        $prompt .= "============================\n";
        $prompt .= "Goal Name: {$goal->name}\n";
        $prompt .= "Amount requested: $" . number_format($withdrawal->amount, 2) . "\n";
        $prompt .= "Reason given by user: " . ($withdrawal->reason ?? 'No reason provided') . "\n\n";
        $prompt .= "CURRENT SAVINGS STATUS\n";
        $prompt .= "======================\n";
        $prompt .= "Current savings: $" . number_format($goal->effective_saved_amount, 2) . "\n";
        $prompt .= "Target amount: $" . number_format($goal->target_amount, 2) . "\n";
        $prompt .= "Progress: " . round($goal->progress_percentage, 1) . "%\n";
        $prompt .= "Remaining after withdrawal would be: $" . number_format($remaining, 2) . "\n\n";
        $prompt .= "WANTS LIST\n";
        $prompt .= "==========\n";
        $prompt .= $wants->count() . " items, total: $" . number_format($wants->sum('cost'), 2) . ":\n";

        foreach ($wants as $want) {
            $prompt .= "- {$want->name}: $" . number_format($want->cost, 2) . " ({$want->priority} priority, status: {$want->status})\n";
        }

        $prompt .= "\nNEEDS LIST\n";
        $prompt .= "==========\n";
        $prompt .= $needs->count() . " items, total: $" . number_format($needs->sum('cost'), 2) . ":\n";

        foreach ($needs as $need) {
            $prompt .= "- {$need->name}: $" . number_format($need->cost, 2) . " ({$need->priority} priority, status: {$need->status})\n";
        }

        $prompt .= "\nProvide a detailed analysis including:\n";
        $prompt .= "1. Whether this withdrawal is financially advisable\n";
        $prompt .= "2. Impact on the goal's progress\n";
        $prompt .= "3. Ability to still cover wants and needs\n";
        $prompt .= "4. Specific recommendation with clear assessment\n";
        $prompt .= "\nFormat your response clearly with headers and use real dollar amounts.";

        return $prompt;
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

    private function generateFallbackSummary(Withdrawal $withdrawal, Goal $goal, $wants, $needs, int $score): string
    {
        $effectiveAmount = $goal->effective_saved_amount;
        $progress = $goal->progress_percentage;
        $remaining = max(0, $effectiveAmount - $withdrawal->amount);
        $remainingPercentage = $goal->target_amount > 0 ? ($remaining / $goal->target_amount) * 100 : 0;
        $wantsTotal = $wants->sum('cost');
        $needsTotal = $needs->sum('cost');

        $lines = [];
        $lines[] = "Withdrawal Analysis for: {$goal->name}";
        $lines[] = "Amount requested: $" . number_format($withdrawal->amount, 2);
        $lines[] = "Current savings: $" . number_format($effectiveAmount, 2);
        $lines[] = "Progress: " . round($progress, 1) . "%";
        $lines[] = "";
        $lines[] = "Wants ({$wants->count()} items, total: $" . number_format($wantsTotal, 2) . "):";

        foreach ($wants as $want) {
            $lines[] = "- {$want->name}: $" . number_format($want->cost, 2) . " ({$want->priority} priority)";
        }

        $lines[] = "";
        $lines[] = "Needs ({$needs->count()} items, total: $" . number_format($needsTotal, 2) . "):";

        foreach ($needs as $need) {
            $lines[] = "- {$need->name}: $" . number_format($need->cost, 2) . " ({$need->priority} priority)";
        }

        $lines[] = "";
        $lines[] = "AI Viability Score: {$score}/10";
        $lines[] = "Remaining after withdrawal: $" . number_format($remaining, 2) . " (" . round($remainingPercentage, 1) . "% of target)";

        if ($score >= 7) {
            $lines[] = "Assessment: This withdrawal appears financially viable based on your savings progress and remaining wants/needs.";
        } elseif ($score >= 4) {
            $lines[] = "Assessment: This withdrawal has moderate risk. Consider if there are alternative uses for these funds.";
        } else {
            $lines[] = "Assessment: This withdrawal is NOT recommended. It would significantly impact your goal progress and ability to meet wants/needs.";
        }

        if ($withdrawal->reason) {
            $lines[] = "Reason given: {$withdrawal->reason}";
        }

        return implode("\n", $lines);
    }
}
