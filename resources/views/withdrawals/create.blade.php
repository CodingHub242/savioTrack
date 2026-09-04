<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Request Withdrawal</h1>
        <p class="mt-1 text-sm text-slate-500">Request a withdrawal from {{ $goal->name }}</p>
    </div>

    <div class="bg-white rounded-lg border border-slate-200">
        <div class="p-6">
            <form action="{{ route('withdrawals.store') }}" method="POST">
                @csrf
                <input type="hidden" name="goal_id" value="{{ $goal->id }}">
                <div class="space-y-6">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-slate-700">Amount</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-slate-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount') }}" class="pl-7 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" required max="{{ $goal->effective_saved_amount }}">
                        </div>
                        <p class="mt-1 text-sm text-slate-500">Available: ${{ number_format($goal->effective_saved_amount, 2) }}</p>
                        @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="reason" class="block text-sm font-medium text-slate-700">Reason</label>
                        <textarea name="reason" id="reason" rows="4" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" required placeholder="What is this withdrawal for?">{{ old('reason') }}</textarea>
                        @error('reason') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('goals.show', $goal) }}" class="inline-flex items-center px-4 py-2 bg-white text-slate-700 text-sm font-medium rounded-md border border-slate-300 hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-md hover:bg-slate-800">
                            Request Withdrawal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
