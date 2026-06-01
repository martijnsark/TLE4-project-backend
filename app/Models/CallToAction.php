<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallToAction extends Model
{
    protected $fillable = [
        'article_id', 'title', 'context_text', 'goal_text', 'target_url',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
