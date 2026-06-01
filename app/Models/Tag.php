<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['name', 'category'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_tags')->withTimestamps();
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_tags')->withTimestamps();
    }
}
