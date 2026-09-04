<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Need extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'user_id',
        'goal_id',
        'name',
        'description',
        'cost',
        'priority',
        'status',
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
