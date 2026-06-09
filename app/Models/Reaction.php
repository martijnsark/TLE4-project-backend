<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reaction extends Model
{
    protected $fillable = [
        'user_id',
        'reactionable_id',
        'reactionable_type',
        'reaction',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    // The reactionable() method defines the inverse of the polymorphic relationship
    public function reactionable(): MorphTo
    {
        return $this->morphTo();
    }
}
