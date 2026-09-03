<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Want;
use App\Models\Need;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WantController extends Controller
{
    public function index(Request $request)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($request->query('goal_id'));
        $wants = $goal->wants()->latest()->paginate(20);

        return view('wants.index', compact('goal', 'wants'));
    }

    public function create(Request $request)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($request->query('goal_id'));

        return view('wants.create', compact('goal'));
    }

    public function store(Request $request)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($request->input('goal_id'));
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'cost' => 'required|numeric|min:0.01',
            'priority' => 'required|in:low,medium,high',
        ]);

        $goal->wants()->create($validated + ['user_id' => Auth::id()]);

        return redirect()->route('wants.index', ['goal_id' => $goal->id])
            ->with('status', 'Want created successfully.');
    }

    public function edit($id)
    {
        $want = Want::where('user_id', Auth::id())->findOrFail($id);

        return view('wants.edit', compact('want'));
    }

    public function update(Request $request, $id)
    {
        $want = Want::where('user_id', Auth::id())->findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'cost' => 'required|numeric|min:0.01',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,saved,purchased,cancelled',
        ]);

        $want->update($validated);

        return redirect()->route('wants.index', ['goal_id' => $want->goal_id])
            ->with('status', 'Want updated successfully.');
    }

    public function destroy($id)
    {
        $want = Want::where('user_id', Auth::id())->findOrFail($id);
        $goalId = $want->goal_id;
        $want->delete();

        return redirect()->route('wants.index', ['goal_id' => $goalId])
            ->with('status', 'Want deleted successfully.');
    }
}
