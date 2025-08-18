<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_company_delivery_zones', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('shipping_company_id')
                ->constrained('shipping_companies')
                ->cascadeOnDelete();

            $table->foreignId('region_id')
                ->constrained('regions')
                ->cascadeOnDelete();

            $table->unique(['shipping_company_id', 'region_id'], 'unique_delivery_zones');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_company_delivery_zones');
    }
};
