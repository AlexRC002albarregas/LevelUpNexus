<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Profile $profile): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return !$user->profile()->exists() || $user->isAdmin();
    }

    public function update(User $user, Profile $profile): bool
    {
        return $user->id === $profile->user_id || $user->isAdmin();
    }

    public function delete(User $user, Profile $profile): bool
    {
        return $user->id === $profile->user_id || $user->isAdmin();
    }

    public function restore(User $user, Profile $profile): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Profile $profile): bool
    {
        return $user->isAdmin();
    }
}
