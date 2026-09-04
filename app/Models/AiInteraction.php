<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiInteraction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'related_type',
        'type',
        'prompt',
        'response',
        'context',
    ];
    
    protected $casts = [
        'context' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function related()
    {
        return $this->morphTo();
    }
}
