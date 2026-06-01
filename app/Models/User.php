<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function interestTags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'user_tags')->withTimestamps();
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function savedArticles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'saved_articles')->withPivot('saved_at');
    }

    public function savedMemes(): BelongsToMany
    {
        return $this->belongsToMany(Meme::class, 'saved_memes')->withPivot('saved_at');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    public function pollVotes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function articleViews(): HasMany
    {
        return $this->hasMany(ArticleView::class);
    }

    public function memeViews(): HasMany
    {
        return $this->hasMany(MemeView::class);
    }

    public function generatedContents(): HasMany
    {
        return $this->hasMany(GeneratedContent::class, 'admin_id');
    }

    public function contentReviews(): HasMany
    {
        return $this->hasMany(ContentReview::class, 'admin_id');
    }
}
