<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\Deposit;
use App\Models\AiInteraction;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcessDepositJob;

class DepositService
{
    public function createDeposit(Goal $goal, array $data): Deposit
    {
        $deposit = $goal->deposits()->create([
            'user_id' => Auth::id(),
            'amount' => $data['amount'],
            'frequency' => $data['frequency'],
            'deposited_at' => $data['deposited_at'],
        ]);

        $goal->increment('current_amount', $data['amount']);

        if ($data['frequency'] !== 'one_time') {
            ProcessDepositJob::dispatch($deposit);
        }

        return $deposit;
    }
}
