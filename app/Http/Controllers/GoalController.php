<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Want;
use App\Models\Need;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AiService;
use App\Jobs\SendDepositReminderJob;

class GoalController extends Controller
{
    public function __construct(private AiService $aiService) {}

    public function index()
    {
        $goals = Auth::user()->goals()->latest()->paginate(20);

        return view('goals.index', compact('goals'));
    }

    public function create()
    {
        return view('goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'target_amount' => 'required|numeric|min:0.01',
            'deadline' => 'nullable|date',
            'deposit_frequency' => 'required|in:none,daily,weekly,monthly,one_time',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $goal = Auth::user()->goals()->create($validated);

        if ($goal->deposit_frequency !== 'none') {
            if ($goal->phone_number || Auth::user()->phone_number) {
                SendDepositReminderJob::dispatch($goal)
                    ->delay(now()->addSeconds(30));
            }
        }

        return redirect()->route('goals.show', $goal)
            ->with('status', 'Goal created successfully.');
    }

    public function show($id)
    {
        $goal = Auth::user()->goals()->findOrFail($id);
        $wants = $goal->wants()->latest()->get();
        $needs = $goal->needs()->latest()->get();
        $recentDeposits = $goal->deposits()->latest()->take(5)->get();
        $recentWithdrawals = $goal->withdrawals()
            ->where('decision', 'approved')
            ->latest()
            ->take(5)
            ->get();
        $totalWantsCost = $wants->sum('cost');
        $totalNeedsCost = $needs->sum('cost');
        $canAccessWantsNeeds = $goal->canAccessWantsNeeds();
        $canWithdraw = $goal->canWithdraw();

        return view('goals.show', compact('goal', 'wants', 'needs', 'recentDeposits', 'recentWithdrawals', 'totalWantsCost', 'totalNeedsCost', 'canAccessWantsNeeds', 'canWithdraw'));
    }

    public function edit($id)
    {
        $goal = Auth::user()->goals()->findOrFail($id);

        return view('goals.edit', compact('goal'));
    }

    public function update(Request $request, $id)
    {
        $goal = Auth::user()->goals()->findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'target_amount' => 'required|numeric|min:0.01',
            'deadline' => 'nullable|date',
            'status' => 'required|in:active,paused,completed,archived',
            'deposit_frequency' => 'required|in:none,daily,weekly,monthly,one_time',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $goal->update($validated);

        return redirect()->route('goals.show', $goal)
            ->with('status', 'Goal updated successfully.');
    }

    public function destroy($id)
    {
        $goal = Auth::user()->goals()->findOrFail($id);
        $goal->delete();

        return redirect()->route('goals.index')
            ->with('status', 'Goal deleted successfully.');
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $goals = $user->goals()->latest()->get();
        $totalSaved = $goals->sum('effective_saved_amount');
        $totalTarget = $goals->sum('target_amount');
        $recentDeposits = \App\Models\Deposit::where('user_id', $user->id)->latest()->take(10)->get();
        $recentWithdrawals = \App\Models\Withdrawal::where('user_id', $user->id)->latest()->take(10)->get();

        $pendingDeposits = \App\Models\Deposit::where('user_id', $user->id)
            ->where('deposited_at', '>', now())
            ->latest()
            ->get();

        $viewMode = $request->query('view', 'default');

        if ($viewMode !== 'default') {
            $dashboardConfig = $this->aiService->arrangeDashboard($goals, $viewMode);
            return view('dashboard.ai', compact('goals', 'totalSaved', 'totalTarget', 'recentDeposits', 'recentWithdrawals', 'dashboardConfig', 'pendingDeposits'));
        }

        return view('dashboard.index', compact('goals', 'totalSaved', 'totalTarget', 'recentDeposits', 'recentWithdrawals', 'pendingDeposits'));
    }

    public function arrangeDashboard(Request $request)
    {
        $request->validate([
            'arrangement_prompt' => 'required|string|max:2000',
        ]);

        $user = Auth::user();
        $prompt = $request->input('arrangement_prompt');

        $viewMode = $this->aiService->interpretArrangePrompt($prompt);

        $goals = $user->goals()->latest()->get();
        $totalSaved = $goals->sum('effective_saved_amount');
        $totalTarget = $goals->sum('target_amount');
        $recentDeposits = \App\Models\Deposit::where('user_id', $user->id)->latest()->take(10)->get();
        $recentWithdrawals = \App\Models\Withdrawal::where('user_id', $user->id)->latest()->take(10)->get();

        $dashboardConfig = $this->aiService->arrangeDashboard($goals, $viewMode, $prompt);

        return view('dashboard.ai', compact('goals', 'totalSaved', 'totalTarget', 'recentDeposits', 'recentWithdrawals', 'dashboardConfig'));
    }
}
