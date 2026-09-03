<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Want;
use App\Models\Need;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AiService;

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
        ]);

        $goal = Auth::user()->goals()->create($validated);

        return redirect()->route('goals.show', $goal)
            ->with('status', 'Goal created successfully.');
    }

    public function show($id)
    {
        $goal = Auth::user()->goals()->findOrFail($id);
        $wants = $goal->wants()->latest()->get();
        $needs = $goal->needs()->latest()->get();
        $recentDeposits = $goal->deposits()->latest()->take(5)->get();
        $totalWantsCost = $wants->sum('cost');
        $totalNeedsCost = $needs->sum('cost');

        return view('goals.show', compact('goal', 'wants', 'needs', 'recentDeposits', 'totalWantsCost', 'totalNeedsCost'));
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
        $totalSaved = $goals->sum('current_amount');
        $totalTarget = $goals->sum('target_amount');
        $recentDeposits = \App\Models\Deposit::where('user_id', $user->id)->latest()->take(10)->get();
        $recentWithdrawals = \App\Models\Withdrawal::where('user_id', $user->id)->latest()->take(10)->get();

        $viewMode = $request->query('view', 'default');

        if ($viewMode !== 'default') {
            $dashboardConfig = $this->aiService->arrangeDashboard($user, $viewMode);
            return view('dashboard.ai', compact('goals', 'totalSaved', 'totalTarget', 'recentDeposits', 'recentWithdrawals', 'dashboardConfig'));
        }

        return view('dashboard.index', compact('goals', 'totalSaved', 'totalTarget', 'recentDeposits', 'recentWithdrawals'));
    }
}
