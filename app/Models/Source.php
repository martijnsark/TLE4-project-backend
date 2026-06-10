<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Source extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'url', 'reliability_score'];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_sources')
            ->withPivot(['source_url', 'is_primary'])
            ->withTimestamps();
    }
}
