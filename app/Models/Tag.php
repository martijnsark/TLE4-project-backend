<?php

namespace App\Models;

use App\Enums\TagCategory;
use App\Enums\TagIcon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'icon'];

    protected $casts = [
        'category' => TagCategory::class,
        'icon' => TagIcon::class,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_tags')->withTimestamps();
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_tags')->withTimestamps();
    }
}
