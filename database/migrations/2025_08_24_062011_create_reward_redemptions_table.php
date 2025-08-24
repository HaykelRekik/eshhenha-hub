<?php

declare(strict_types=1);

use App\Enums\RewardRedemptionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('reward_redemptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('reward_id')
                ->constrained('rewards');
            $table->foreignId('user_id')
                ->constrained('users');
            $table->string('reference');
            $table->string('status')->default(RewardRedemptionStatus::NEW);
            $table->text('redemption_instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reward_redemptions');
    }
};
