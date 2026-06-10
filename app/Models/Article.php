<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Article extends Model
{
    protected $fillable = [
        'title', 'summary', 'content', 'image_url', 'original_url',
        'tone', 'status', 'author_id', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tags')->withTimestamps();
    }

    public function sources(): BelongsToMany
    {
        return $this->belongsToMany(Source::class, 'article_sources')
            ->withPivot(['source_url', 'is_primary'])
            ->withTimestamps();
    }

    public function callToAction(): HasOne
    {
        return $this->hasOne(CallToAction::class);
    }
    // The reactions() method defines the polymorphic relationship to the Reaction model
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }

    public function polls(): HasMany
    {
        return $this->hasMany(Poll::class);
    }

    public function memes(): HasMany
    {
        return $this->hasMany(Meme::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ArticleView::class);
    }

    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_articles')->withPivot('saved_at');
    }
}
