<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deposit extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'user_id',
        'goal_id',
        'amount',
        'frequency',
        'deposited_at',
        'metadata',
    ];
    
    protected $casts = [
        'metadata' => 'array',
        'deposited_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }
}
