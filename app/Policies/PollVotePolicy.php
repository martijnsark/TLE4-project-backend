<?php

namespace App\Policies;

use App\Models\PollVote;
use App\Models\User;

class PollVotePolicy
{
    public function delete(User $user, PollVote $pollVote): bool
    {
        return $pollVote->user_id === $user->id || $user->role === 'admin';
    }

    public function view(User $user, PollVote $pollVote): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, PollVote $pollVote): bool
    {
        return $user->role === 'admin';
    }
}
