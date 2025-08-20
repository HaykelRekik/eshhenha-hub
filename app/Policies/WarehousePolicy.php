<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if ($user->hasRole(UserRole::ADMIN)) {
            return true;
        }

        return $user->hasRole(UserRole::COMPANY);
    }

    public function view(User $user, Warehouse $warehouse): bool
    {
        if ($user->hasRole(UserRole::ADMIN)) {
            return true;
        }

        return $user->hasRole(UserRole::COMPANY) && $user->company->id === $warehouse->company_id;
    }

    public function create(User $user): bool
    {
        if ($user->hasRole(UserRole::ADMIN)) {
            return true;
        }

        return $user->hasRole(UserRole::COMPANY);
    }

    public function update(User $user, Warehouse $warehouse): bool
    {
        if ($user->hasRole(UserRole::ADMIN)) {
            return true;
        }

        return $user->hasRole(UserRole::COMPANY) && $user->company->id === $warehouse->company_id;
    }

    public function delete(User $user, Warehouse $warehouse): bool
    {
        if ($user->hasRole(UserRole::ADMIN)) {
            return true;
        }

        return $user->hasRole(UserRole::COMPANY) && $user->company->id === $warehouse->company_id;
    }

    public function restore(User $user, Warehouse $warehouse): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function forceDelete(User $user, Warehouse $warehouse): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }
}
