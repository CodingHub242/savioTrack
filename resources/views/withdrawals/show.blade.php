<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Withdrawal Decision</h1>
        <p class="mt-1 text-sm text-slate-500">Review and decide on this withdrawal request</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Requested Amount</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">${{ number_format($withdrawal->amount, 2) }}</p>
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
                    <p class="text-sm font-medium text-slate-500">Current Savings</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">${{ number_format($goal->effective_saved_amount, 2) }}</p>
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
                    <p class="text-sm font-medium text-slate-500">Viability Score</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $withdrawal->viability_score ?? 'Pending' }}/10</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-full">
                    <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 mb-8">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-lg font-medium text-slate-900">Reason</h2>
        </div>
        <div class="p-6">
            <p class="text-slate-700">{{ $withdrawal->reason }}</p>
        </div>
    </div>

    @if(!$withdrawal->ai_summary)
        <div class="bg-white rounded-lg border border-slate-200 mb-8">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-slate-900 mr-3"></div>
                <h2 class="text-lg font-medium text-slate-900">AI is analyzing your withdrawal...</h2>
            </div>
            <div class="p-6">
                <p class="text-slate-600">The AI is evaluating your withdrawal request based on your savings progress, wants, and needs. This analysis includes a viability score and personalized recommendation.</p>
                <p class="text-sm text-slate-500 mt-2">Please wait while the analysis completes...</p>
                <div class="mt-4">
                    <a href="{{ route('withdrawals.show', $withdrawal) }}" class="text-sm text-slate-600 hover:text-slate-900">Refresh to check status</a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg border border-slate-200 mb-8">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-medium text-slate-900">AI Analysis</h2>
            </div>
            <div class="p-6">
                <pre class="text-sm text-slate-700 whitespace-pre-wrap font-mono bg-slate-50 p-4 rounded">{{ $withdrawal->ai_summary }}</pre>
            </div>
        </div>
    @endif

    @if($withdrawal->decision === 'pending')
        <div class="bg-white rounded-lg border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-medium text-slate-900">Make Your Decision</h2>
            </div>
            <div class="p-6">
                <p class="text-slate-700 mb-6">Based on the AI analysis and your own assessment, would you like to proceed with this withdrawal?</p>

                <form action="{{ route('withdrawals.process', $withdrawal) }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Your Notes (optional)</label>
                        <textarea name="user_notes" rows="3" class="w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" placeholder="Add any notes about your decision..."></textarea>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="submit" name="decision" value="approved" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-md hover:bg-slate-800">
                            Approve Withdrawal
                        </button>
                        <button type="submit" name="decision" value="rejected" class="inline-flex items-center px-4 py-2 bg-white text-slate-700 text-sm font-medium rounded-md border border-slate-300 hover:bg-slate-50">
                            Reject Withdrawal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg border border-slate-200 {{ $withdrawal->decision_quality === 'safe' ? 'border-emerald-200' : 'border-red-200' }}">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-medium text-slate-900">Decision Recorded</h2>
            </div>
            <div class="p-6">
                <p class="text-slate-700">You {{ $withdrawal->decision }} this withdrawal.</p>
                <p class="text-sm text-slate-500 mt-2">Decision Quality: <strong>{{ ucfirst($withdrawal->decision_quality) }}</strong></p>
                @if($withdrawal->user_notes)
                    <p class="text-sm text-slate-500 mt-2">Your notes: {{ $withdrawal->user_notes }}</p>
                @endif
            </div>
        </div>
    @endif
</x-app-layout>
