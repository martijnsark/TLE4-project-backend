<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'summary', 'content', 'image_url', 'original_url',
        'tone', 'status', 'author_id', 'published_at', 'body_paragraphs',
    ];

    protected $casts = [
        'published_at'    => 'datetime',
        'body_paragraphs' => 'array',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tags')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function primaryTag(): ?Tag
    {
        return $this->tags->firstWhere('pivot.is_primary', true);
    }

    public function getIsGoodNewsAttribute(): bool
    {
        return $this->tags->contains(fn ($t) => $t->name === 'Goed nieuws');
    }

    public function getIsTrendingAttribute(): bool
    {
        return $this->tags->contains(fn ($t) => $t->name === 'Trending');
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

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
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

    public function pruneEmptyCallToAction(): void
    {
        $cta = $this->callToAction()->first();

        if (! $cta) {
            return;
        }

        $hasContent = collect([$cta->title, $cta->context_text, $cta->goal_text, $cta->target_url])
            ->some(fn ($value) => filled($value));

        if (! $hasContent) {
            $cta->delete();
        }
    }
}
