<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Group $group): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Group $group): bool
    {
        return false;
    }

    public function delete(User $user, Group $group): bool
    {
        return false;
    }

    public function restore(User $user, Group $group): bool
    {
        return false;
    }

    public function forceDelete(User $user, Group $group): bool
    {
        return false;
    }
}
