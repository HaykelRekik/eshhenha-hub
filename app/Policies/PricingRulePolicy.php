<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\PricingRule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PricingRulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function view(User $user, PricingRule $pricingRule): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function update(User $user, PricingRule $pricingRule): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function delete(User $user, PricingRule $pricingRule): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function restore(User $user, PricingRule $pricingRule): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }

    public function forceDelete(User $user, PricingRule $pricingRule): bool
    {
        return $user->hasRole(UserRole::ADMIN);
    }
}
