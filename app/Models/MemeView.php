<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemeView extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'meme_id', 'viewed_at', 'viewing_time_seconds'];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meme(): BelongsTo
    {
        return $this->belongsTo(Meme::class);
    }
}
