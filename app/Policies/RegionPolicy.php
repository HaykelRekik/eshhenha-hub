<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Region;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function view(User $user, Region $region): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function update(User $user, Region $region): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function delete(User $user, Region $region): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function restore(User $user, Region $region): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function forceDelete(User $user, Region $region): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }
}
