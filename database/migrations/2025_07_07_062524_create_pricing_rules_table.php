<?php

declare(strict_types=1);

use App\Enums\PricingRuleType;
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
        Schema::create('pricing_rules', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('This is specific to a user(customer)');

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->onDelete('cascade')
                ->comment('This is specific to a company');

            $table->foreignId('shipping_company_id')
                ->nullable()
                ->constrained('shipping_companies')
                ->onDelete('cascade');

            $table->decimal('weight_from', 10, 2)->default(0);
            $table->decimal('weight_to', 10, 2)->comment('in KG');

            $table->string('type')->default(PricingRuleType::GLOBAL);

            $table->decimal('local_price_per_kg', 10, 2);
            $table->decimal('international_price_per_kg', 10, 2);

            $table->index(['company_id', 'shipping_company_id']);
            $table->index(['weight_from', 'weight_to']);

            // Ensure no overlapping weight ranges for same company-shipping_company combination
            $table->unique([
                'user_id',
                'company_id',
                'shipping_company_id',
                'weight_from',
                'weight_to',
            ], 'unique_weight_range_per_contract');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
