<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Auth\Access\HandlesAuthorization;

class WalletPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN);

    }

    public function view(User $user, Wallet $wallet): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN);

    }

    public function update(User $user, Wallet $wallet): bool
    {
        return $user->hasRole(UserRole::ADMIN);

    }

    public function delete(User $user, Wallet $wallet): bool
    {
        return $user->hasRole(UserRole::ADMIN);

    }

    public function restore(User $user, Wallet $wallet): bool
    {
        return $user->hasRole(UserRole::ADMIN);

    }

    public function forceDelete(User $user, Wallet $wallet): bool
    {
        return $user->hasRole(UserRole::ADMIN);

    }
}
