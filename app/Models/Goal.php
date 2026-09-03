<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Attributes\Fillable;

class Goal extends Model
{
    use HasFactory;

    #[Fillable(['user_id', 'name', 'description', 'target_amount', 'current_amount', 'deadline', 'status', 'metadata'])]
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

        return min(100, ($this->current_amount / $this->target_amount) * 100);
    }
}
