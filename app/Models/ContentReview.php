<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentReview extends Model
{
    protected $fillable = [
        'generated_content_id', 'admin_id', 'feedback', 'approved', 'reviewed_at',
    ];

    protected $casts = [
        'approved' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function generatedContent(): BelongsTo
    {
        return $this->belongsTo(GeneratedContent::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
