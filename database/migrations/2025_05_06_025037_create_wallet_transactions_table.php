<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table): void {
            $table->id();
            $table->decimal('amount')->default(0);
            $table->string('type');
            $table->decimal('balance_after');
            $table->ulid('identifier');
            $table->string('external_identifier')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('wallet_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('performed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('The user who performed the action , can be the user himself , another user in case of transfer , or the admin in case of balance adjustment ');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
