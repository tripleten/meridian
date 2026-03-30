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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('cart_id', 26);
            $table->char('product_id', 26);
            $table->char('product_variant_id', 26)->nullable();
            $table->unsignedSmallInteger('qty')->default(1);
            // Snapshot prices at time of add (stale detection if prices change)
            $table->unsignedBigInteger('unit_price_snapshot');
            $table->string('name_snapshot', 250);
            $table->string('sku_snapshot', 100);
            $table->json('custom_options')->nullable();             // gift wrapping, engraving etc
            $table->timestamps();

            $table->foreign('cart_id')
                ->references('id')->on('carts')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->cascadeOnDelete();

            $table->foreign('product_variant_id')
                ->references('id')->on('product_variants')
                ->nullOnDelete();

            $table->index('cart_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
