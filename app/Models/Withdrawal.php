<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Withdrawal extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'user_id',
        'goal_id',
        'amount',
        'reason',
        'viability_score',
        'decision',
        'decision_quality',
        'ai_summary',
        'user_notes',
        'metadata',
    ];
    
    protected $casts = [
        'metadata' => 'array',
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
