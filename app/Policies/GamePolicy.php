<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GamePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Game $game): bool
    {
        return $user->id === $game->user_id || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, Game $game): bool
    {
        return $user->id === $game->user_id || $user->isAdmin();
    }

    public function delete(User $user, Game $game): bool
    {
        return $user->id === $game->user_id || $user->isAdmin();
    }

    public function restore(User $user, Game $game): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Game $game): bool
    {
        return $user->isAdmin();
    }
}
