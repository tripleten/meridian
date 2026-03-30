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
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('tax_zone_id', 26);
            $table->string('name', 100);                         // "UK Standard VAT 20%"
            $table->string('code', 50)->unique();                // uk_standard, uk_reduced, eu_de_standard
            $table->decimal('rate', 5, 4);                       // 0.2000 = 20%, 0.0500 = 5%
            $table->enum('type', ['inclusive', 'exclusive'])->default('inclusive');
            $table->boolean('is_compound')->default(false);      // stacked taxes (CA: GST+PST)
            $table->boolean('is_shipping_taxable')->default(false);
            $table->timestamps();

            $table->foreign('tax_zone_id')
                ->references('id')->on('tax_zones')
                ->cascadeOnDelete();

            $table->index('tax_zone_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
