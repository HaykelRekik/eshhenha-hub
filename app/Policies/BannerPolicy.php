<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Banner;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BannerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function view(User $user, Banner $banner): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function update(User $user, Banner $banner): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function delete(User $user, Banner $banner): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function restore(User $user, Banner $banner): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function forceDelete(User $user, Banner $banner): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }
}
