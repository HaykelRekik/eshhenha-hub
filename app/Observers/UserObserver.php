<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\UserRole;
use App\Models\User;
use App\Settings\RewardSettings;
use Random\RandomException;

class UserObserver
{
    /**
     * @throws RandomException
     */
    public function creating(User $user): void
    {
        if ( ! $user->hasRole(UserRole::ADMIN)) {
            do {
                $referralCode = mb_str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            } while (User::where('referral_code', $referralCode)->exists());

            $user->referral_code = $referralCode;
        }
    }

    public function created(User $user): void
    {
        if ( ! $user->hasRole(UserRole::ADMIN)) {
            $user->wallet()->create([
                'balance' => 0,
                'last_operation_at' => now(),
            ]);

            $user->update([
                'loyalty_points' => app(RewardSettings::class)?->welcome_bonus ?? 0,
            ]);

        }
    }
}
