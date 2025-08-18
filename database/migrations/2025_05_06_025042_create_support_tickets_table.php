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
        Schema::create('support_tickets', function (Blueprint $table): void {
            $table->id();

            $table->string('subject');

            $table->string('description');

            $table->string('response')
                ->nullable();

            $table->string('status')
                ->default('new');

            $table->foreignId('user_id');

            $table->foreignId('contact_message_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->comment('The contact message related to that support ticket because we can transform a message to a support ticket directly');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
