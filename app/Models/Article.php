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
        'tone', 'status', 'author_id', 'category_id', 'published_at',
        'is_good_news', 'is_trending', 'body_paragraphs',
    ];

    protected $casts = [
        'published_at'    => 'datetime',
        'body_paragraphs' => 'array',
        'is_good_news'    => 'boolean',
        'is_trending'     => 'boolean',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
}
