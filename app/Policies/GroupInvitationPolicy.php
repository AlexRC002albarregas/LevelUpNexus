<?php

namespace App\Policies;

use App\Models\GroupInvitation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupInvitationPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, GroupInvitation $groupInvitation): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, GroupInvitation $groupInvitation): bool
    {
        return false;
    }

    public function delete(User $user, GroupInvitation $groupInvitation): bool
    {
        return false;
    }

    public function restore(User $user, GroupInvitation $groupInvitation): bool
    {
        return false;
    }

    public function forceDelete(User $user, GroupInvitation $groupInvitation): bool
    {
        return false;
    }
}
