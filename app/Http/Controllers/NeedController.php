<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Need;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NeedController extends Controller
{
    public function index(Request $request)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($request->query('goal_id'));
        $needs = $goal->needs()->latest()->paginate(20);

        return view('needs.index', compact('goal', 'needs'));
    }

    public function create(Request $request)
    {
        $goal = Goal::where('user_id', Auth::id())->findOrFail($request->query('goal_id'));

        return view('needs.create', compact('goal'));
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

        $goal->needs()->create($validated + ['user_id' => Auth::id()]);

        return redirect()->route('needs.index', ['goal_id' => $goal->id])
            ->with('status', 'Need created successfully.');
    }

    public function edit($id)
    {
        $need = Need::where('user_id', Auth::id())->findOrFail($id);

        return view('needs.edit', compact('need'));
    }

    public function update(Request $request, $id)
    {
        $need = Need::where('user_id', Auth::id())->findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'cost' => 'required|numeric|min:0.01',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,saved,purchased,cancelled',
        ]);

        $need->update($validated);

        return redirect()->route('needs.index', ['goal_id' => $need->goal_id])
            ->with('status', 'Need updated successfully.');
    }

    public function destroy($id)
    {
        $need = Need::where('user_id', Auth::id())->findOrFail($id);
        $goalId = $need->goal_id;
        $need->delete();

        return redirect()->route('needs.index', ['goal_id' => $goalId])
            ->with('status', 'Need deleted successfully.');
    }
}
