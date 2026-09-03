<?php

namespace App\Providers;

use App\Models\Goal;
use App\Models\Withdrawal;
use App\Policies\GoalPolicy;
use App\Policies\WithdrawalPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Goal::class, GoalPolicy::class);
        Gate::policy(Withdrawal::class, WithdrawalPolicy::class);
    }
}
