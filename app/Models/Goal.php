<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory; 

class Goal extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'target_amount',
        'current_amount',
        'deadline',
        'status',
        'deposit_frequency',
        'phone_number',
        'metadata',
    ];
    
    protected $casts = [
        'metadata' => 'array',
        'deadline' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wants(): HasMany
    {
        return $this->hasMany(Want::class);
    }

    public function needs(): HasMany
    {
        return $this->hasMany(Need::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return min(100, ($this->effective_saved_amount / $this->target_amount) * 100);
    }

    public function getEffectiveSavedAmountAttribute(): float
    {
        $totalDeposits = $this->deposits()->sum('amount');
        $totalWithdrawals = $this->withdrawals()
            ->where('decision', 'approved')
            ->sum('amount');

        return max(0, $totalDeposits - $totalWithdrawals);
    }

    public function canAccessWantsNeeds(): bool
    {
        return $this->progress_percentage >= 75;
    }

    public function canWithdraw(): bool
    {
        return $this->progress_percentage >= 75;
    }
}
