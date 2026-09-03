<?php

namespace App\Policies;

use App\Models\Withdrawal;
use App\Models\User;

class WithdrawalPolicy
{
    public function view(User $user, Withdrawal $withdrawal): bool
    {
        return $user->id === $withdrawal->user_id;
    }

    public function process(User $user, Withdrawal $withdrawal): bool
    {
        return $user->id === $withdrawal->user_id && $withdrawal->decision === 'pending';
    }
}
