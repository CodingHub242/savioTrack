<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Pending Deposits</h1>
            <p class="mt-1 text-sm text-slate-500">Scheduled deposits based on your goal frequency settings</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm text-slate-600 hover:text-slate-900">Back to Dashboard</a>
    </div>

    <div class="bg-white rounded-lg border border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Goal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Frequency</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Scheduled Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Days Remaining</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($deposits as $deposit)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('goals.show', $deposit->goal) }}" class="text-sm font-medium text-slate-900 hover:text-slate-700">{{ $deposit->goal->name }}</a>
                                <div class="text-sm text-slate-500">{{ Str::limit($deposit->goal->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">${{ number_format($deposit->amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ ucfirst(str_replace('_', ' ', $deposit->frequency)) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $deposit->deposited_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                @php
                                    $days = now()->diffInDays($deposit->deposited_at, false);
                                    if ($days > 0) {
                                        echo $days . ' days';
                                    } else {
                                        echo 'Past due';
                                    }
                                @endphp
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-slate-900">No pending deposits</h3>
                                <p class="mt-1 text-sm text-slate-500">Deposits scheduled via your goal's frequency settings will appear here.</p>
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
