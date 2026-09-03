<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Create Goal</h1>
        <p class="mt-1 text-sm text-slate-500">Set up a new savings goal</p>
    </div>

    <div class="bg-white rounded-lg border border-slate-200">
        <div class="p-6">
            <form action="{{ route('goals.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" required>
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="target_amount" class="block text-sm font-medium text-slate-700">Target Amount</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-slate-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" step="0.01" name="target_amount" id="target_amount" value="{{ old('target_amount') }}" class="pl-7 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" required>
                        </div>
                        @error('target_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="deadline" class="block text-sm font-medium text-slate-700">Deadline (optional)</label>
                        <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                        @error('deadline') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('goals.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-slate-700 text-sm font-medium rounded-md border border-slate-300 hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-md hover:bg-slate-800">
                            Create Goal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
