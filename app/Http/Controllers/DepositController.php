<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\DepositService;

class DepositController extends Controller
{
    public function __construct(private DepositService $depositService) {}

    public function index(Request $request)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($request->query('goal_id'));
        $deposits = $goal->deposits()->latest()->paginate(20);

        return view('deposits.index', compact('goal', 'deposits'));
    }

    public function create(Request $request)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($request->query('goal_id'));

        return view('deposits.create', compact('goal'));
    }

    public function store(Request $request)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($request->input('goal_id'));
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'deposited_at' => 'required|date',
        ]);

        $deposit = $this->depositService->createDeposit($goal, [
            'amount' => $validated['amount'],
            'frequency' => 'one_time',
            'deposited_at' => $validated['deposited_at'],
        ]);

        return redirect()->route('deposits.index', ['goal_id' => $goal->id])
            ->with('status', 'Deposit recorded successfully.');
    }

    public function pending(Request $request)
    {
        $deposits = \App\Models\Deposit::where('user_id', Auth::id())
            ->where('deposited_at', '>', now())
            ->latest()
            ->paginate(20);

        return view('deposits.pending', compact('deposits'));
    }
}
