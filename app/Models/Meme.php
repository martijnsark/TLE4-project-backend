<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meme extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id', 'title', 'image_url', 'caption',
        'author', 'author_name', 'top', 'bot', 'cat',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(MemeView::class);
    }

    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_memes')->withPivot('saved_at');
    }
}
