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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('product_variant_id', 26);
            $table->char('source_id', 26);
            $table->unsignedInteger('qty_available')->default(0);
            $table->unsignedInteger('qty_reserved')->default(0);  // held for pending orders
            $table->unsignedInteger('qty_incoming')->default(0);  // purchase orders in transit
            $table->integer('low_stock_threshold')->default(5);   // alert below this qty
            $table->boolean('backorders_allowed')->default(false);
            $table->boolean('manage_stock')->default(true);        // false = always in stock
            $table->timestamps();

            $table->unique(['product_variant_id', 'source_id']);
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->cascadeOnDelete();
            $table->foreign('source_id')->references('id')->on('inventory_sources')->cascadeOnDelete();
            $table->index(['source_id', 'qty_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
