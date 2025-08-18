<?php

declare(strict_types=1);

use App\Enums\ShipmentDeliveryStatus;
use App\Enums\ShipmentPaymentMethod;
use App\Enums\ShipmentPaymentStatus;
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
        Schema::create('shipments', function (Blueprint $table): void {
            $table->id();

            $table->string('tracking_number')->unique()->nullable();

            $table->morphs('senderable');

            $table->foreignId('shipping_company_id')
                ->constrained();
            $table->foreignId('address_id')
                ->constrained();
            $table->foreignId('warehouse_id')
                ->nullable()
                ->constrained()
                ->comment('Only valid if the sender is a company , not a user');

            /** Recipient Information */
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('recipient_street');
            $table->string('recipient_zip')
                ->nullable();
            $table->string('recipient_city');
            $table->string('recipient_region');
            $table->string('recipient_country');

            /** Shipment Details */
            $table->dateTime('shipping_date')->nullable();
            $table->decimal('weight')->comment('in KG');
            $table->integer('length')->comment('in CM');
            $table->integer('width')->comment('in CM');
            $table->integer('height')->comment('in CM');
            $table->string('content_description', 1000)
                ->nullable()
                ->comment('Required for SMSA');
            $table->string('delivery_instructions')
                ->nullable()
                ->comment('Special delivery instructions, for example : Fragile , Frozen , ..');

            /** Prices */
            $table->decimal('base_price')
                ->comment('The base shipping price calculated based on the pricing rules');
            $table->decimal('insurance_price');
            $table->decimal('home_pickup_price')
                ->default(0);
            $table->decimal('total_price');

            $table->string('payment_status')->default(ShipmentPaymentStatus::UNPAID->value);
            $table->string('payment_method')->default(ShipmentPaymentMethod::CREDIT_CARD->value);

            $table->string('status')->default(ShipmentDeliveryStatus::PENDING->value);

            $table->json('raw_response')
                ->nullable()
                ->comment('The raw response from the shipping company API');

            $table->timestamps();

            $table->index('shipping_company_id');
            $table->index('address_id');
            $table->index('payment_status');
            $table->index('status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
