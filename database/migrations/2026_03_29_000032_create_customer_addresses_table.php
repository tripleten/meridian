<?php

/**
 * This file is part of Meridian Ecommerce Platform.
 *
 * @package    Database\Migrations
 * @author     L K Lalitesh <lalitesh@live.com>
 * @company    Bytics Lab
 * @copyright  2026 Bytics Lab. All rights reserved.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('customer_id', 26);
            $table->string('label', 50)->nullable();           // Home, Office, etc.
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('company', 150)->nullable();
            $table->string('line1', 200);
            $table->string('line2', 200)->nullable();
            $table->string('city', 100);
            $table->string('county', 100)->nullable();
            $table->string('postcode', 20);
            $table->char('country_code', 2);                   // ISO 3166-1 alpha-2
            $table->string('phone', 30)->nullable();
            $table->string('vat_number', 30)->nullable();
            $table->boolean('is_default_billing')->default(false);
            $table->boolean('is_default_shipping')->default(false);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
            $table->index('customer_id');
            $table->index('country_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
