<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Article $article): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Article $article): bool
    {
        return $user->role === 'admin';
    }
}
