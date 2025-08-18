<?php

declare(strict_types=1);

use App\Enums\UserRole;
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
        Schema::create('users', function (Blueprint $table): void {
            $table->id();

            $table->string('name');

            $table->string('email')
                ->unique();

            $table->timestamp('email_verified_at')
                ->nullable();

            $table->string('password');

            $table->string('phone_number')
                ->unique()
                ->nullable();

            $table->string('national_id')
                ->unique()
                ->nullable()
                ->comment('The national id number or iqama number for a user');

            $table->string('avatar_url')
                ->nullable();

            $table->unsignedInteger('loyalty_points')
                ->default(0);

            $table->string('role')
                ->default(UserRole::USER);

            $table->boolean('is_active')
                ->default(true);

            $table->string('referral_code')
                ->nullable()
                ->unique();

            $table->foreignId('referred_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('The user who referred this user');

            $table->rememberToken();

            $table->timestamp('last_login_at')
                ->nullable();

            $table->ipAddress('last_login_ip')
                ->nullable();

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
