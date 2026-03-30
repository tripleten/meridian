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
        Schema::create('tier_prices', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('product_variant_id', 26)->nullable();
            $table->char('product_id', 26)->nullable();           // if no variant
            $table->char('customer_group_id', 26)->nullable();    // null = all groups
            $table->unsignedSmallInteger('min_qty');              // minimum qty to qualify
            $table->unsignedBigInteger('price');                  // unit price at this tier
            $table->char('currency_code', 3)->default('GBP');
            $table->timestamps();

            $table->foreign('product_variant_id')
                ->references('id')->on('product_variants')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->cascadeOnDelete();

            $table->foreign('customer_group_id')
                ->references('id')->on('customer_groups')
                ->nullOnDelete();

            $table->index(['product_id', 'customer_group_id']);
            $table->index(['product_variant_id', 'customer_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tier_prices');
    }
};
