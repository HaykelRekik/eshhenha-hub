<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table): void {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('description_ar', 1000)
                ->nullable();
            $table->string('description_en', 1000)
                ->nullable();
            $table->string('image');
            $table->string('supplier_name')
                ->nullable();
            $table->string('external_identifier')
                ->nullable()
                ->comment('identifier from supplier will be used to fetch the reward from suppliers API');
            $table->unsignedInteger('quantity')
                ->default(1);
            $table->unsignedInteger('required_points');
            $table->boolean('is_active')
                ->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
