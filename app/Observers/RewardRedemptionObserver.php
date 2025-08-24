<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\RewardRedemption;

class RewardRedemptionObserver
{
    public function creating(RewardRedemption $rewardRedemption): void
    {
        $rewardRedemption->reference = uniqid();
    }
}
