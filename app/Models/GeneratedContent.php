<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneratedContent extends Model
{
    use HasFactory;
    protected $fillable = [
        'admin_id', 'title', 'generated_text', 'original_news_url', 'status',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ContentReview::class);
    }
}
