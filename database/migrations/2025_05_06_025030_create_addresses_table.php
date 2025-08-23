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
        Schema::create('addresses', function (Blueprint $table): void {
            $table->id();
            $table->string('label')
                ->nullable()
                ->comment('Stores user addresses with labels like Home or Office');

            $table->string('contact_name')
                ->nullable()
                ->comment('Name of the person receiving deliveries at this address');

            $table->string('contact_phone_number')
                ->nullable();

            $table->string('street');

            $table->string('zip_code')
                ->nullable();

            $table->boolean('is_default')
                ->default(false);

            $table->boolean('is_recipient_address')
                ->default(false);

            $table->foreignId('country_id')->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('region_id')->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('city_id')->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->morphs('addressable');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
