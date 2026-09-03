<x-app-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-slate-900">Dashboard</h1>
        <p class="mt-1 text-sm text-slate-500">Overview of your savings goals and progress</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Saved</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">${{ number_format($totalSaved, 2) }}</p>
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
                    <p class="text-sm font-medium text-slate-500">Total Target</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">${{ number_format($totalTarget, 2) }}</p>
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
                    <p class="text-sm font-medium text-slate-500">Active Goals</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $goals->count() }}</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-full">
                    <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <a href="{{ route('goals.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-md hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Goal
        </a>
    </div>

    <div class="bg-white rounded-lg border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-lg font-medium text-slate-900">Your Goals</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($goals as $goal)
                    <a href="{{ route('goals.show', $goal) }}" class="group block bg-slate-50 rounded-lg border border-slate-200 p-6 hover:border-slate-300 hover:shadow-sm transition">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-slate-900 truncate group-hover:text-slate-700">{{ $goal->name }}</h3>
                                <p class="mt-1 text-sm text-slate-500 line-clamp-2">{{ $goal->description }}</p>
                            </div>
                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $goal->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                                {{ ucfirst($goal->status) }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-slate-500">Progress</span>
                                <span class="font-medium text-slate-900">{{ number_format($goal->progress_percentage, 1) }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2">
                                <div class="bg-slate-900 h-2 rounded-full transition-all duration-500" style="width: {{ $goal->progress_percentage }}%"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <div>
                                <span class="text-slate-500">Saved:</span>
                                <span class="font-medium text-slate-900 ml-1">${{ number_format($goal->current_amount, 2) }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-500">Target:</span>
                                <span class="font-medium text-slate-900 ml-1">${{ number_format($goal->target_amount, 2) }}</span>
                            </div>
                        </div>

                        @if($goal->deadline)
                            <div class="mt-3 pt-3 border-t border-slate-200">
                                <p class="text-xs text-slate-500">Deadline: {{ $goal->deadline->format('M d, Y') }}</p>
                            </div>
                        @endif
                    </a>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-slate-900">No goals</h3>
                        <p class="mt-1 text-sm text-slate-500">Get started by creating your first savings goal.</p>
                        <div class="mt-6">
                            <a href="{{ route('goals.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-md hover:bg-slate-800">
                                New Goal
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
