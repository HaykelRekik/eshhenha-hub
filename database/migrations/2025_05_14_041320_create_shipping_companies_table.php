<?php

declare(strict_types=1);

use App\Enums\ShippingCompanyInsuranceType;
use App\Enums\ShippingRange;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_companies', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')
                ->unique();
            $table->string('logo');
            $table->string('phone_number')
                ->unique();
            $table->boolean('is_active')
                ->default(true);
            $table->string('insurance_type')
                ->default(ShippingCompanyInsuranceType::AMOUNT->value);
            $table->decimal('insurance_value')
                ->default(0);
            $table->string('bank_code');
            $table->string('bank_account_number');
            $table->string('iban');
            $table->string('swift');
            $table->string('shipping_range')
                ->default(ShippingRange::LOCAL->value);
            $table->decimal('home_pickup_cost')
                ->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_companies');
    }
};
