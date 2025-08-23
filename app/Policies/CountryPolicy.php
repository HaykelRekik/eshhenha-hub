<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Country;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CountryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function view(User $user, Country $country): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function update(User $user, Country $country): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function delete(User $user, Country $country): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function restore(User $user, Country $country): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function forceDelete(User $user, Country $country): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }
}
