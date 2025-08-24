<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RewardRedemptionStatus;
use App\Observers\RewardRedemptionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([RewardRedemptionObserver::class])]
class RewardRedemption extends Model
{
    protected $fillable = [
        'reward_id',
        'user_id',
        'reference',
        'status',
        'redemption_instructions',
    ];

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'status' => RewardRedemptionStatus::class,
        ];
    }
}
