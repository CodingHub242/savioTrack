<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\WithdrawalService;
use App\Services\AiService;

class WithdrawalController extends Controller
{
    public function __construct(
        private WithdrawalService $withdrawalService,
        private AiService $aiService
    ) {}

    public function index(Request $request)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($request->query('goal_id'));
        $withdrawals = $goal->withdrawals()->latest()->paginate(20);

        return view('withdrawals.index', compact('goal', 'withdrawals'));
    }

    public function create(Request $request)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($request->query('goal_id'));

        if (! $goal->canWithdraw()) {
            return redirect()->route('goals.show', $goal)
                ->with('status', 'Withdrawals are only available when your goal reaches 75% of the target amount. Current progress: ' . number_format($goal->progress_percentage, 1) . '%.');
        }

        return view('withdrawals.create', compact('goal'));
    }

    public function store(Request $request)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($request->input('goal_id'));

        if (! $goal->canWithdraw()) {
            return redirect()->route('goals.show', $goal)
                ->with('status', 'Withdrawals are only available when your goal reaches 75% of the target amount. Current progress: ' . number_format($goal->progress_percentage, 1) . '%.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $goal->effective_saved_amount,
            'reason' => 'required|string|max:2000',
        ]);

        $withdrawal = $this->withdrawalService->createWithdrawal($goal, $validated['amount'], $validated['reason']);

        return redirect()->route('withdrawals.show', $withdrawal)
            ->with('status', 'Withdrawal request created. Please complete the decision process.');
    }

    public function show($id)
    {
        $withdrawal = \App\Models\Withdrawal::where('user_id', Auth::id())->findOrFail($id);
        $goal = $withdrawal->goal;

        return view('withdrawals.show', compact('withdrawal', 'goal'));
    }

    public function processDecision(Request $request, $id)
    {
        $withdrawal = \App\Models\Withdrawal::where('user_id', Auth::id())->findOrFail($id);
        $validated = $request->validate([
            'decision' => 'required|in:approved,rejected',
            'user_notes' => 'nullable|string|max:2000',
        ]);

        $this->withdrawalService->processDecision($withdrawal, $validated['decision'], $validated['user_notes'] ?? null);

        return redirect()->route('withdrawals.show', $withdrawal)
            ->with('status', 'Decision recorded successfully.');
    }
}
