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
        Schema::table('companies', function (Blueprint $table): void {
            $table->after('phone_number', function (Blueprint $table): void {
                $table->string('cr_number')->nullable()->unique();
                $table->string('vat_number')->nullable()->unique();

            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropUnique(['cr_number', 'vat_number']);
            $table->dropColumn('cr_number');
            $table->dropColumn('vat_number');
        });
    }
};
