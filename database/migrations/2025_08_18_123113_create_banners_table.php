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
        Schema::create('banners', function (Blueprint $table): void {
            $table->id();

            $table->string('image');

            $table->string('title_ar')
                ->nullable();

            $table->string('title_en')
                ->nullable();

            $table->string('title_ur')
                ->nullable();

            $table->string('description_ar')
                ->nullable();

            $table->string('description_en')
                ->nullable();

            $table->string('description_ur')
                ->nullable();

            $table->string('link')
                ->nullable();

            $table->unsignedInteger('position');

            $table->unsignedInteger('clicks_count')
                ->default(0);

            $table->boolean('is_active')
                ->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
