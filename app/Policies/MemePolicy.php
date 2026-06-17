<?php

namespace App\Policies;

use App\Models\Meme;
use App\Models\User;

class MemePolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Meme $meme): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Meme $meme): bool
    {
        return $user->role === 'admin';
    }
}
