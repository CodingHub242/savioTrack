<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Deposits</h1>
            <p class="mt-1 text-sm text-slate-500">Savings contributions for {{ $goal->name }}</p>
        </div>
        <a href="{{ route('deposits.create', ['goal_id' => $goal->id]) }}" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-md hover:bg-slate-800">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Deposit
        </a>
    </div>

    <div class="bg-white rounded-lg border border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Frequency</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($deposits as $deposit)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">${{ number_format($deposit->amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ ucfirst($deposit->frequency) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $deposit->deposited_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <p class="text-sm text-slate-500">No deposits yet. Record your first deposit to start tracking.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($deposits->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $deposits->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
