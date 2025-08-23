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
        Schema::create('companies', function (Blueprint $table): void {
            $table->id();

            $table->string('name');
            $table->string('email');
            $table->string('phone_number');
            $table->string('logo')
                ->nullable();

            $table->string('bank_code')
                ->nullable();
            $table->string('bank_account_number')
                ->nullable();
            $table->string('iban')
                ->nullable();
            $table->string('swift')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->foreignId('user_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
