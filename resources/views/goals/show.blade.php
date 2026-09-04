<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">{{ $goal->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $goal->description }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('goals.edit', $goal) }}" class="inline-flex items-center px-4 py-2 bg-white text-slate-700 text-sm font-medium rounded-md border border-slate-300 hover:bg-slate-50">
                Edit
            </a>
            <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                @csrf @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-white text-red-700 text-sm font-medium rounded-md border border-slate-300 hover:bg-red-50">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Saved</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">${{ number_format($goal->effective_saved_amount, 2) }}</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-full">
                    <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Target</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">${{ number_format($goal->target_amount, 2) }}</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-full">
                    <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Progress</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($goal->progress_percentage, 1) }}%</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-full">
                    <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 mb-8">
        <a href="{{ route('deposits.create', ['goal_id' => $goal->id]) }}" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-md hover:bg-slate-800">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Deposit
        </a>
        <a href="{{ route('withdrawals.create', ['goal_id' => $goal->id]) }}" class="inline-flex items-center px-4 py-2 bg-white text-slate-700 text-sm font-medium rounded-md border border-slate-300 hover:bg-slate-50 {{ $canWithdraw ? '' : 'opacity-50 cursor-not-allowed' }}">
            Request Withdrawal
        </a>
        <a href="{{ route('wants.index', ['goal_id' => $goal->id]) }}" class="inline-flex items-center px-4 py-2 bg-white text-slate-700 text-sm font-medium rounded-md border border-slate-300 hover:bg-slate-50 {{ $canAccessWantsNeeds ? '' : 'opacity-50 cursor-not-allowed' }}">
            Wants
        </a>
        <a href="{{ route('needs.index', ['goal_id' => $goal->id]) }}" class="inline-flex items-center px-4 py-2 bg-white text-slate-700 text-sm font-medium rounded-md border border-slate-300 hover:bg-slate-50 {{ $canAccessWantsNeeds ? '' : 'opacity-50 cursor-not-allowed' }}">
            Needs
        </a>
        <a href="{{ route('withdrawals.index', ['goal_id' => $goal->id]) }}" class="inline-flex items-center px-4 py-2 bg-white text-slate-700 text-sm font-medium rounded-md border border-slate-300 hover:bg-slate-50">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Withdrawals
        </a>
    </div>

    @if(!$canAccessWantsNeeds)
        <div class="mb-8 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded">
            <p class="font-medium">Wants and Needs are locked until your goal reaches 75% of the target amount.</p>
            <p class="text-sm mt-1">Current progress: {{ number_format($goal->progress_percentage, 1) }}% of ${{ number_format($goal->target_amount, 2) }}</p>
        </div>
    @endif

    @if(!$canWithdraw)
        <div class="mb-8 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded">
            <p class="font-medium">Withdrawals are locked until your goal reaches 75% of the target amount.</p>
            <p class="text-sm mt-1">Current progress: {{ number_format($goal->progress_percentage, 1) }}% of ${{ number_format($goal->target_amount, 2) }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-lg font-medium text-slate-900">Wants</h2>
                @if($canAccessWantsNeeds)
                    <a href="{{ route('wants.create', ['goal_id' => $goal->id]) }}" class="text-sm text-slate-600 hover:text-slate-900">Add</a>
                @else
                    <span class="text-sm text-slate-400">Locked</span>
                @endif
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($wants as $want)
                        <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $want->name }}</p>
                                <p class="text-sm text-slate-500">${{ number_format($want->cost, 2) }} · {{ ucfirst($want->priority) }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $want->status === 'saved' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                {{ ucfirst($want->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No wants yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-lg font-medium text-slate-900">Needs</h2>
                @if($canAccessWantsNeeds)
                    <a href="{{ route('needs.create', ['goal_id' => $goal->id]) }}" class="text-sm text-slate-600 hover:text-slate-900">Add</a>
                @else
                    <span class="text-sm text-slate-400">Locked</span>
                @endif
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($needs as $need)
                        <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $need->name }}</p>
                                <p class="text-sm text-slate-500">${{ number_format($need->cost, 2) }} · {{ ucfirst($need->priority) }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $need->status === 'saved' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                {{ ucfirst($need->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No needs yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-lg font-medium text-slate-900">Recent Deposits</h2>
            <a href="{{ route('deposits.index', ['goal_id' => $goal->id]) }}" class="text-sm text-slate-600 hover:text-slate-900">View all</a>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @forelse($recentDeposits as $deposit)
                    <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-slate-900">${{ number_format($deposit->amount, 2) }}</p>
                            <p class="text-sm text-slate-500">{{ ucfirst($deposit->frequency) }} · {{ $deposit->deposited_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No deposits yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-lg font-medium text-slate-900">Recent Withdrawals</h2>
            <a href="{{ route('withdrawals.index', ['goal_id' => $goal->id]) }}" class="text-sm text-slate-600 hover:text-slate-900">View all</a>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @forelse($recentWithdrawals as $withdrawal)
                    <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-slate-900">${{ number_format($withdrawal->amount, 2) }}</p>
                            <p class="text-sm text-slate-500">{{ ucfirst($withdrawal->frequency ?? 'approved') }} · {{ $withdrawal->created_at->format('M d, Y') }}</p>
                            @if($withdrawal->reason)
                                <p class="text-xs text-slate-400 mt-1">{{ Str::limit($withdrawal->reason, 80) }}</p>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $withdrawal->decision_quality === 'safe' ? 'bg-emerald-100 text-emerald-800' : ($withdrawal->decision_quality === 'bad' ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-700') }}">
                            {{ ucfirst($withdrawal->decision) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No withdrawals yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
