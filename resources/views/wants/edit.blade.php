<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Edit Want</h1>
        <p class="mt-1 text-sm text-slate-500">Update want details</p>
    </div>

    <div class="bg-white rounded-lg border border-slate-200">
        <div class="p-6">
            <form action="{{ route('wants.update', $want) }}" method="POST">
                @csrf @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $want->name) }}" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" required>
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">{{ old('description', $want->description) }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="cost" class="block text-sm font-medium text-slate-700">Cost</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-slate-500 sm:text-sm">$</span>
                            </div>
                            <input type="number" step="0.01" name="cost" id="cost" value="{{ old('cost', $want->cost) }}" class="pl-7 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" required>
                        </div>
                        @error('cost') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-slate-700">Priority</label>
                        <select name="priority" id="priority" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                            @foreach(['low', 'medium', 'high'] as $priority)
                                <option value="{{ $priority }}" {{ old('priority', $want->priority) === $priority ? 'selected' : '' }}>
                                    {{ ucfirst($priority) }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                            @foreach(['pending', 'saved', 'purchased', 'cancelled'] as $status)
                                <option value="{{ $status }}" {{ old('status', $want->status) === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('wants.index', ['goal_id' => $want->goal_id]) }}" class="inline-flex items-center px-4 py-2 bg-white text-slate-700 text-sm font-medium rounded-md border border-slate-300 hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-md hover:bg-slate-800">
                            Update Want
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
